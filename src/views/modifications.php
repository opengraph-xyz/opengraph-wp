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

<?php if (!empty($thumbnailUrl)): ?>
<div style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
  <h4 style="margin: 0 0 10px 0; color: #23282d;">Template Preview</h4>
  <img src="<?php echo esc_url($thumbnailUrl); ?>" alt="Template Preview" style="width: 200px; height: 105px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
</div>
<?php endif; ?>

<table class="form-table">
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
    function toggleCustomFieldInput(select) {
      var $customFieldInput = select.siblings('input[id^="opengraph-custom-fields-"]');
      if ('{post_meta}' === select.val() || '{user_meta}' === select.val() || '{author_meta}' === select.val()) {
        $customFieldInput.show();
      } else {
        $customFieldInput.hide().val('');
      }
    }

    // Attach change event using event delegation
    $('.form-table').on('change', 'select[id^="opengraph-modification-"]', function() {
        toggleCustomFieldInput($(this));
    });

    // Trigger change event for existing select elements
    $('select[id^="opengraph-modification-"]').trigger('change');

    $('#template-version-dropdown').change(function() {
      var postId = '<?php echo esc_js($post->ID); ?>';
      var version = $(this).val();

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
        currentSelections[customFieldId] = $('#' + customFieldId).val();
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
                $.each(dynamicTags, function(tag, description) {
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
                }

                var customFieldId = 'opengraph-custom-fields-' + variable.id + '__' + modification.property;
                if (currentSelections.hasOwnProperty(customFieldId)) {
                  customFieldInput.val(currentSelections[customFieldId]);
                }
              });
            });
          } else {
            // Display message when no variables are available
            tableBody.html('<tr><td colspan="2">No modifications available for this template.</td></tr>');
          }
        }
      });
    });
  });
</script>