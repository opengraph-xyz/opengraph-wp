<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

$available_variables = opengraphxyz_get_template_variables($post->ID);

$opengraphxyz = get_post_meta($post->ID, 'opengraph-xyz', true);
$current_modifications = empty($opengraphxyz['modifications']) ? array() : $opengraphxyz['modifications'];
$custom_fields = empty($opengraphxyz['custom_fields']) ? array() : $opengraphxyz['custom_fields'];
$dynamic_tags = wp_list_pluck(opengraphxyz_init()->get_tags(), 'description');

wp_nonce_field('opengraph-xyz', 'opengraph-xyz-nonce');

$dynamic_tags_json = json_encode($dynamic_tags);

// Generate thumbnail URL
$thumbnailUrl = '';
if (isset($opengraphxyz['template_id']) && isset($opengraphxyz['template_version'])) {
  // Use the already fetched variables
  $thumbnailUrl = opengraphxyz_generate_image_url($opengraphxyz['template_id'], $opengraphxyz['template_version'], $available_variables);
}

?>

<style>
  /* Responsive adjustments for modifications table */
  #opengraph-modifications-table td select,
  #opengraph-modifications-table td input.regular-text {
    width: 100%;
    max-width: 100%;
    min-width: 0;
  }
  
  @media screen and (max-width: 782px) {
    .form-table th, .form-table td {
      padding: 10px 0;
    }
  }
</style>

<?php if (!empty($thumbnailUrl)): ?>
<div style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
  <div style="margin-bottom: 15px;">
    <h4 style="margin: 0 0 10px 0; color: #23282d;">Template Preview</h4>
    <img src="<?php echo esc_url($thumbnailUrl); ?>" alt="Template Preview" style="width: 200px; height: 105px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
  </div>
  
  <div>
    <h4 style="margin: 0 0 10px 0; color: #23282d;">Version</h4>
    <?php include plugin_dir_path(__FILE__) . 'versions.php'; ?>
  </div>
</div>
<?php endif; ?>

<table class="form-table" id="opengraph-modifications-table">
  <tbody>
    <?php if (empty($available_variables)): ?>
                      <tr><td colspan="2">No modifications available for this template.</td></tr>
    <?php endif; ?>
    <?php foreach ($available_variables as $variable): ?>

      <?php foreach ($variable['modifications'] as $modification): ?>
        <tr>
          <th scope="row">
            <label for="opengraph-modification-<?php echo esc_attr($variable['id']); ?>__<?php echo esc_attr($modification['property']); ?>">
              <?php echo esc_html($variable['id']); ?> (<?php echo esc_html($modification['property']); ?>)
            </label>
          </th>
          <td>
            <select name="opengraph[modifications][<?php echo esc_attr($variable['id']); ?>][<?php echo esc_attr($modification['property']); ?>]" id="opengraph-modification-<?php echo esc_attr($variable['id']); ?>__<?php echo esc_attr($modification['property']); ?>" class="regular-text">
              <?php
              $current_value = isset($current_modifications[$variable['id']][$modification['property']]) ? $current_modifications[$variable['id']][$modification['property']] : '';
              $custom_field = isset($custom_fields[$variable['id']][$modification['property']]) ? $custom_fields[$variable['id']][$modification['property']] : '';
              ?>
              <option value="_" <?php selected($current_value, ''); ?>><?php esc_html_e('Default', 'opengraph-xyz'); ?></option>
              <?php foreach ($dynamic_tags as $dynamic_tag => $description): ?>
                                <option value="{<?php echo esc_attr($dynamic_tag); ?>}" <?php selected($current_value, '{' . $dynamic_tag . '}'); ?>>
                                  <?php echo esc_html($description); ?>
                                </option>
              <?php endforeach; ?>
            </select>
            <input
              type="text"
              name="opengraph[custom_fields][<?php echo esc_attr($variable['id']); ?>][<?php echo esc_attr($modification['property']); ?>]"
              id="opengraph-custom-fields-<?php echo esc_attr($variable['id']); ?>__<?php echo esc_attr($modification['property']); ?>"
              class="regular-text"
              style="display: none; margin-left: 10px"
              placeholder="<?php esc_attr_e('Meta Key', 'opengraph-xyz'); ?>"
              value="<?php echo isset($custom_fields[$variable['id']][$modification['property']]) ? esc_attr($custom_fields[$variable['id']][$modification['property']]) : ''; ?>" />
          </td>
              </tr>
          <?php endforeach; ?>

    <?php endforeach; ?>
  </tbody>
</table>


<script type="text/javascript">
  var dynamicTags = <?php echo wp_json_encode($dynamic_tags_json); ?>;

  jQuery(document).ready(function($) {
    // Track form state
    var formIsDirty = false;
    var isSubmitting = false;
    var originalFormState = {};

    // Function to mark form as dirty
    function markFormAsDirty() {
      formIsDirty = true;
    }

    // Function to capture original form state
    function captureOriginalFormState() {
      originalFormState = {};
      $('select[id^="opengraph-modification-"], input[id^="opengraph-custom-fields-"], #template-version-dropdown, #post_title, input[name="opengraph[post_types][]"]').each(function() {
        var $element = $(this);
        var id = $element.attr('id') || $element.attr('name');
        
        if ($element.is('select')) {
          originalFormState[id] = $element.val();
        } else if ($element.is('input[type="checkbox"]')) {
          // For checkboxes with same name (arrays), append value to make ID unique
          if (id.indexOf('[]') !== -1) {
            id += '_' + $element.val();
          }
          originalFormState[id] = $element.is(':checked');
        } else {
          originalFormState[id] = $element.val();
        }
      });
      // Also capture filters data
      originalFormState['filters_data'] = $('#opengraph-xyz-filters-data').val();
    }

    // Function to check if form has changed
    function checkFormChanges() {
      var hasChanges = false;
      $('select[id^="opengraph-modification-"], input[id^="opengraph-custom-fields-"], #template-version-dropdown, #post_title, input[name="opengraph[post_types][]"]').each(function() {
        var $element = $(this);
        var id = $element.attr('id') || $element.attr('name');
        var currentValue;
        
        if ($element.is('select')) {
          currentValue = $element.val();
        } else if ($element.is('input[type="checkbox"]')) {
          // For checkboxes with same name (arrays), append value to make ID unique
          if (id.indexOf('[]') !== -1) {
            id += '_' + $element.val();
          }
          currentValue = $element.is(':checked');
        } else {
          currentValue = $element.val();
        }
        
        // Only compare if we have an original value for this element
        if (originalFormState.hasOwnProperty(id) && originalFormState[id] !== currentValue) {
          hasChanges = true;
          return false; // break the loop
        }
      });

      // Check filters data
      if (originalFormState['filters_data'] !== $('#opengraph-xyz-filters-data').val()) {
        hasChanges = true;
      }
      
      formIsDirty = hasChanges;
    }

    function toggleCustomFieldInput(select) {
      var $customFieldInput = select.siblings('input[id^="opengraph-custom-fields-"]');
      if ('{post_meta}' === select.val() || '{user_meta}' === select.val() || '{author_meta}' === select.val()) {
        $customFieldInput.show();
      } else {
        $customFieldInput.hide().val('');
      }
    }

    // Capture original form state after DOM is fully ready
    $(document).ready(function() {
        setTimeout(function() {
            captureOriginalFormState();
            // Initial check to ensure we start with a clean state
            formIsDirty = false;
        }, 200);
    });

    // Attach change event using event delegation
    $('.form-table').on('change', 'select[id^="opengraph-modification-"]', function() {
        toggleCustomFieldInput($(this));
        markFormAsDirty();
    });

    // Track changes on custom field inputs
    $('.form-table').on('input', 'input[id^="opengraph-custom-fields-"]', function() {
        markFormAsDirty();
    });

    // Track changes on template version dropdown
    $('#template-version-dropdown').on('change', function() {
        markFormAsDirty();
    });

    // Track changes on post title
    $('#post_title').on('input', function() {
        markFormAsDirty();
    });

    // Track changes on post type checkboxes using event delegation for the entire document
    $(document).on('change', 'input[name="opengraph[post_types][]"]', function() {
        markFormAsDirty();
    });

    // Track changes on filters data
    $(document).on('change', '#opengraph-xyz-filters-data', function() {
        markFormAsDirty();
    });
    // Since the hidden input might be updated programmatically, we can also listen for a custom event or just rely on the beforeunload check which re-verifies everything if we implement checkFormChanges correctly.
    // However, the current implementation of beforeunload just checks formIsDirty.
    // Let's update the filters JS to trigger a change event on the hidden input when it updates.

    // Handle form submission to reset dirty state
    $('form#post').on('submit', function() {
        isSubmitting = true;
        formIsDirty = false;
    });

    // Warn user before leaving page with unsaved changes
    $(window).on('beforeunload', function() {
        if (isSubmitting) {
            return;
        }
        checkFormChanges(); // Re-check right before unloading to be sure
        if (formIsDirty) {
            return 'You have unsaved changes. Are you sure you want to leave this page?';
        }
    });

    // Trigger change event for existing select elements
    $('select[id^="opengraph-modification-"]').trigger('change');

    $('#template-version-dropdown').change(function() {


      var postId = '<?php echo esc_js($post->ID); ?>';
      var version = $(this).val();
      
      // Show loading overlay
      showLoadingOverlay();

      // Disable the update button
      $('#publish').prop('disabled', true);

      var tableBody = $('.form-table tbody');

      // Store current selections and custom fields
      var currentSelections = {};
      $('.form-table select').each(function() {
        var selectId = $(this).attr('id');
        var selectedValue = $(this).val();
        currentSelections[selectId] = selectedValue;

        var customFieldId = 'opengraph-custom-fields-' + selectId.split('opengraph-modification-')[1];
        var customFieldValue = $('#' + customFieldId).val();
        currentSelections[customFieldId] = customFieldValue;
      });
      

      tableBody.empty(); // Clear existing rows

      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
          action: 'fetch_template_variables',
          post_id: postId,
          template_version: version
        },
        complete: function() {
          // Re-enable the update button
          $('#publish').prop('disabled', false);
          // Hide loading overlay
          hideLoadingOverlay();
        },
        success: function(response) {
          
          if (response.success && response.data.variables && response.data.variables.length > 0) {
            var variables = response.data.variables;

            $.each(variables, function(i, variable) {
              
              $.each(variable.modifications, function(j, modification) {
                
                var row = $('<tr></tr>');
                var th = $('<th scope="row"></th>');
                var label = $('<label></label>').attr({
                  for: 'opengraph-modification-' + variable.id + '__' + modification.property
                }).text(variable.id + ' (' + modification.property + ')');
                th.append(label);
                row.append(th);

                var td = $('<td></td>');
                var select = $('<select></select>').attr({
                  name: 'opengraph[modifications][' + variable.id + '][' + modification.property + ']',
                  id: 'opengraph-modification-' + variable.id + '__' + modification.property,
                  class: 'regular-text'
                });

                // Add default option
                var defaultOption = $('<option></option>').attr({value: ''}).text('Default');
                select.append(defaultOption);

                // Add dynamic tags options
                
                // Parse dynamicTags if it's a string
                var parsedDynamicTags = dynamicTags;
                if (typeof dynamicTags === 'string') {
                  try {
                    parsedDynamicTags = JSON.parse(dynamicTags);
                  } catch (e) {
                    parsedDynamicTags = {};
                  }
                }
                
                
                Object.entries(parsedDynamicTags).forEach(function([tag, description]) {
                  var option = $('<option></option>').attr({value: '{' + tag + '}'}).text(description);
                  select.append(option);
                });

                td.append(select);

                // Custom field input
                var customFieldInput = $('<input>')
                  .attr({
                    type: 'text',
                    name: 'opengraph[custom_fields][' + variable.id + '][' + modification.property + ']',
                    id: 'opengraph-custom-fields-' + variable.id + '__' + modification.property,
                    class: 'regular-text',
                    style: 'display: none; margin-left: 14px',
                    placeholder: '<?php esc_attr_e('Meta Key', 'opengraph-xyz'); ?>'
                  });

                td.append(customFieldInput);
                row.append(td);
                tableBody.append(row);

                // Reapply stored selections and custom field values
                var selectId = 'opengraph-modification-' + variable.id + '__' + modification.property;
                
                if (currentSelections.hasOwnProperty(selectId)) {
                  select.val(currentSelections[selectId]);
                  toggleCustomFieldInput(select);
                } else {
                }

                var customFieldId = 'opengraph-custom-fields-' + variable.id + '__' + modification.property;
                if (currentSelections.hasOwnProperty(customFieldId)) {
                  customFieldInput.val(currentSelections[customFieldId]);
                } else {
                }

                // Add change event listeners to new elements
                select.on('change', function() {
                  toggleCustomFieldInput($(this));
                  markFormAsDirty();
                });

                customFieldInput.on('input', function() {
                  markFormAsDirty();
                });
              });
            });
          } else {
            // Display message when no variables are available
            tableBody.html('<tr><td colspan="2">No modifications available for this template.</td></tr>');
          }
        },
        error: function(xhr, status, error) {
          console.log('=== AJAX ERROR ===');
          console.log('XHR:', xhr);
          console.log('Status:', status);
          console.log('Error:', error);
          console.log('Response text:', xhr.responseText);
        }
      });
    });
  });
</script>

<?php include_once plugin_dir_path(__FILE__) . '../ui/loading-overlay.php'; ?>