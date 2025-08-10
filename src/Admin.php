<?php

namespace OpenGraphXYZ;

// Exit if accessed directly.
defined('ABSPATH') || exit;

class Admin
{
  /**
   * Add hooks and initialize admin functionalities.
   */
  public function init()
  {
    add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
    add_action('admin_menu', array($this, 'modify_add_new_submenu'));
    add_action('admin_head', array($this, 'add_custom_button'));
    add_action('admin_head', array($this, 'hide_add_new_button'));
    add_action('admin_init', array($this, 'register_plugin_settings'));

    // Meta
    add_action('add_meta_boxes_opengraph_template', array($this, 'add_meta_boxes'));
    add_action('save_post', array($this, 'save_edited_form'), 10, 2);

    // Custom columns.
    add_filter('manage_opengraph_template_posts_columns', array($this, 'manage_posts_columns'));
    add_action('manage_opengraph_template_posts_custom_column', array($this, 'manage_posts_custom_column'), 10, 2);

    // Add action to handle form submission
    add_action('admin_post_create_opengraph_template', array($this, 'handle_template_creation'));

    // Add hooks for AJAX
    add_action('wp_ajax_fetch_template_variables', array($this, 'fetch_template_variables'));
    add_action('wp_ajax_get_create_template_url', array($this, 'get_create_template_url'));
    
    // Add hook to conditionally display OG Manager content
    add_action('admin_head', array($this, 'check_api_key_for_og_manager'));
    
    // Add hook to display error messages
    add_action('admin_notices', array($this, 'display_error_messages'));
    
    // Enqueue scripts for admin pages
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
  }

  /**
   * Adds the admin menu for the plugin.
   */
  public function add_plugin_admin_menu()
  {
    add_menu_page(
      'OpenGraph XYZ Settings',
      'OpenGraph',
      'manage_options',
      'edit.php?post_type=opengraph_template',
      null,
      'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iMjBweCIgaGVpZ2h0PSIxMXB4IiB2aWV3Qm94PSIwIDAgMjAgMTEiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+CiAgICA8dGl0bGU+R3JvdXA8L3RpdGxlPgogICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgaWQ9Ikdyb3VwIiBmaWxsPSIjMDAwMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iPgogICAgICAgICAgICA8cGF0aCBkPSJNNS4wNjgwODY4NywwLjAxMzQ5NDY4NDkgQzMuNTkxODgzMjMsMC4xMTU0ODYyODcgMi4xNTQxMzkyOCwwLjg2MzYwODk3MSAxLjIyMTI4MzEyLDIuMDE0OTk5MjYgQy0wLjM2NDczOTE3OCwzLjk3Mjc0MDUxIC0wLjQxMDMzNDc3LDYuNzIzNDczMzcgMS4xMDk4ODkwMSw4LjczMzczMDYxIEMxLjk1NzQ4NTEyLDkuODU0NjI0NzcgMy4yNjQ3NDQxMiwxMC42Mjk5MjY4IDQuNjU3MzU1ODQsMTAuODM3ODcxNyBDNS4wMDQ3OTA1NSwxMC44ODk3NDI4IDYuMDMyMDgxNDksMTAuODgzMDE3MSA2LjM1MzQ3NDgxLDEwLjgyNjgxNTcgQzcuNTYzMzMzNDcsMTAuNjE1MDkzMyA4LjU2MTk4ODE1LDEwLjA5NDcyNDIgOS4zOTIzNDY5MSw5LjI0MzQxMjIzIEM5LjU1ODk3NDcsOS4wNzI1OTcwMiA5Ljc1OTcwNjUyLDguODQ2OTYyNDggOS44Mzg0NzkzOSw4Ljc0MjAyMjYxIEM5LjkxNzM0NDk0LDguNjM3MTc0ODggOS45OTM3MDgyOSw4LjU1MTMwNjYxIDEwLjAwODI1ODEsOC41NTEzMDY2MSBDMTAuMDIyODA3OSw4LjU1MTMwNjYxIDEwLjEwMTk1MTUsOC42NDQ2Mzc2OCAxMC4xODQxNTMzLDguNzU4NjA2NjEgQzEwLjQ5OTMzNzUsOS4xOTU1OTUwMiAxMS4xMjkyNDI0LDkuNzYwMDAzODQgMTEuNjkxOTU4OCwxMC4xMDk0NjU2IEMxMi4yMzQ1NjQ4LDEwLjQ0NjQ4OTMgMTMuMDQ0NzIwNywxMC43NDAyMTA0IDEzLjcwMTEzMDQsMTAuODM3OTYzOSBDMTQuMDY1NTI0NCwxMC44OTIyMzA0IDE1LjE0MTc0NzIsMTAuODgzMDE3MSAxNS40ODg5MDM5LDEwLjgyMjY2OTcgQzE3LjQ1MTM2NzksMTAuNDgxMzE1NyAxOS4wOTk2NjcxLDkuMDY4OTExNjkgMTkuNzMwMDM1NCw3LjE4ODU2MjQ1IEMxOS45MTIyMzI0LDYuNjQ0NzkxNSAxOS45OTY5MzY0LDYuMTU1ODM5ODkgMTkuOTk5NzE2Niw1LjYzMTY5MzM0IEMyMC4wMDE3NTU1LDUuMjMwOTEzMzQgMTkuOTk0MjQ4OSw1LjE2OTQ2MDQgMTkuOTI4NTQzLDUuMDQ3MTk5NDYgQzE5LjgwODkwMDksNC44MjQ4ODE3MyAxOS42OTQ1NDEzLDQuNzEyNjYzMzIgMTkuNDc4MDU0OSw0LjYwNTE0MzcyIEwxOS4yNzM4OTQxLDQuNTAzNzA0OTIgTDE2Ljg0ODk4NzEsNC41MDM3MDQ5MiBDMTQuMDc1OTAzOSw0LjUwMzcwNDkyIDE0LjIyMTY4MDEsNC40ODk3MDA2NSAxMy45MjYxNDI4LDQuNzgzNTEzODYgQzEzLjQ2NjAxNjUsNS4yNDA5NTU4NyAxMy41ODA0Njg5LDUuOTg1NzYxNzUgMTQuMTU3MzY0Myw2LjI4ODIzNTQ5IEwxNC4zMjg4MTEyLDYuMzc4MTU3NjMgTDE2LjE1NDc2NjQsNi4zODc1NTUyMyBMMTcuOTgwODE0Miw2LjM5Njk1MjgzIEwxNy44OTQzNDk0LDYuNjM2NDA3MzcgQzE3LjIzOTA1MTgsOC40NTE0MzQwNyAxNS4yMTM4NDc2LDkuNDQyMDUxNyAxMy40MTQwMjY0LDguODI3OTgzMDIgQzEyLjI2OTEzMjIsOC40Mzc0Mjk4MSAxMS40NTA5MTM4LDcuNTg2NjcwNTkgMTEuMDg2NTE5Nyw2LjQwODAwODgzIEMxMC45OTk5NjIyLDYuMTI4MTk5ODkgMTAuOTk1MTQzMiw2LjA3ODE3MTQ5IDEwLjk5NTE0MzIsNS40NDkxNzcyMSBDMTAuOTk1MTQzMiw0LjgyMzIyMzMzIDExLjAwMDMzMjksNC43Njg3NzI1MyAxMS4wODU1OTMsNC40ODcxMjA5MiBDMTEuNTk5NTYyOCwyLjc5MDM5MzQxIDEzLjE3MTY4NCwxLjczMzcxNjE5IDE0LjkxODQ5NTYsMS45MTA3MDQzMyBDMTUuMzcwODM3MywxLjk1NjU4NjczIDE1LjcwMDU3MTMsMi4wNDM4MzcgMTYuMDc0NTEwNywyLjIxNjU4NyBDMTYuNjA0MTQyNCwyLjQ2MTI5MzE0IDE2Ljk3MDAxOTMsMi43NDIxMTU1NSAxNy4zNTI1Nzc0LDMuMTk3NDM4NDkgQzE3LjY0MjY0NjksMy41NDI2NjIxIDE3LjgwODI1NTMsMy42NDQ1NjE1NyAxOC4xMTQ1NDI4LDMuNjY2Mzk3MTcgQzE4LjUyNTM2NjUsMy42OTU2MDM0MyAxOC44MzY2NTgzLDMuNTI4NDczNTYgMTkuMDM1MzUxMywzLjE3MTkxNzU2IEMxOS4xMjIwMDE1LDMuMDE2MzA0MzUgMTkuMTM0MzI3MSwyLjk2MDkzMjIyIDE5LjEzNDMyNzEsMi43Mjg2NjQwOCBDMTkuMTM0MzI3MSwyLjQ5NDI3Njg3IDE5LjEyMjY1MDIsMi40NDMwNTA3NCAxOS4wMzQyMzkyLDIuMjkwMzg1OCBDMTguNzk1Njk2NSwxLjg3ODU0OTc5IDE4LjE5ODY5MDcsMS4yOTQ2MDg3MSAxNy42MjYwNTgzLDAuOTEzMDg0NTcyIEMxNy4wMzQ3MDU3LDAuNTE5MTIyNDMgMTYuMjM1NzYzNCwwLjE5ODEyOTg4OSAxNS41MzAyMzY1LDAuMDcwOTg1ODg2MiBDMTUuMDk0NDgzNSwtMC4wMDc0MTk1ODIxNSAxNC4xMDU2NTIzLC0wLjAwODcwOTQ0ODg0IDEzLjY0NDMyMTIsMC4wNjg2ODI1NTI5IEMxMi43NjAxMTg5LDAuMjE2ODMyOTU2IDExLjkyNDAxNDQsMC41NzgwODc3NjQgMTEuMjAzODQ1LDEuMTIzMTQ4NTggQzEwLjg3NTEzMDQsMS4zNzE5MDg1OCAxMC4zOTk3MTI5LDEuODQzMzU0ODYgMTAuMTg2NTYyOCwyLjEzMjAwODYgQzEwLjA5OTE3MTMsMi4yNTAzMDc4IDEwLjAxNzM0MDEsMi4zNDcxMzk5NCAxMC4wMDQ3MzY1LDIuMzQ3MTM5OTQgQzkuOTkyMTMyODMsMi4zNDcxMzk5NCA5Ljg5OTQ1ODg2LDIuMjM4ODgzMjcgOS43OTg3MjIyNiwyLjEwNjU3OTggQzkuNTc2Mzk3NDEsMS44MTQ2MDkyNiA5LjA2Nzg5NTM0LDEuMzEyOTQzMjUgOC43NzIzNTgwNiwxLjA5NDAzNDQ0IEM3LjcwMjI1MTc0LDAuMzAxNDExMzU4IDYuMzk5OTA0NDcsLTAuMDc4NDU0MzgzOCA1LjA2ODA4Njg3LDAuMDEzNDk0Njg0OSBNNS45MDMyNjQ2NywxLjkxNzA2MTUzIEM2LjMxNzE0NjYxLDEuOTY3MzY2MzMgNi42NDMzNTg5OCwyLjA2MzU1MzUzIDcuMDI1MDgzMDYsMi4yNDc4MjAyIEM4LjI3MDQzNTg0LDIuODQ4OTkwMjIgOS4wMzkzNTE3Niw0LjA2ODQ2NzA0IDkuMDM5MzUxNzYsNS40NDI0NTE0NyBDOS4wMzkzNTE3Niw3LjI2NzI0NDMxIDcuNzI0OTU2ODcsOC43NDA1NDg0OCA1Ljg3ODQyODA1LDguOTg1NTMxMDIgQzQuODQ5MjgzNjMsOS4xMjIwNzI2MiAzLjcxMTg5NjAyLDguNzI0NDI1MTUgMi45NTQ4NDIzNyw3Ljk2MzIxOTUzIEMyLjUxNzIzNTg5LDcuNTIzMjgyODUgMi4yNzMwMzk5OCw3LjEyODIxNTExIDIuMDg0MjYzMTEsNi41NTUxNDU3NyBDMS4zMzQxNjAwMSw0LjI3NzYwOTcxIDMuMDA2MTgzNzUsMS45MzczMzA4NiA1LjQxOTQxMzg4LDEuODg2ODQxNzkgQzUuNTI5OTczOTMsMS44ODQ1Mzg0NiA1Ljc0Nzc1Nzc1LDEuODk4MTc0MTkgNS45MDMyNjQ2NywxLjkxNzA2MTUzIiBpZD0iU2hhcGUiPjwvcGF0aD4KICAgICAgICA8L2c+CiAgICA8L2c+Cjwvc3ZnPg=='
    );

    // Add the submenu item with a different name but same slug as the main menu item
    add_submenu_page(
      'edit.php?post_type=opengraph_template', // Parent slug
      'OG Manager',               // Page title
      'OG Manager',                   // Menu title
      'manage_options',                        // Capability
      'edit.php?post_type=opengraph_template', // Menu slug
      null                                     // Function (not needed as it's the same as the main menu)
    );

    // Add the settings menu item
    add_submenu_page(
      'edit.php?post_type=opengraph_template',
      'Open Graph XYZ Settings',
      'Settings',
      'manage_options',
      'og-xyz-settings',
      array($this, 'display_settings_page')
    );
  }

  public function save_edited_form($post_id, $post)
  {
    // Do not save for ajax requests.
    if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_REQUEST['bulk_edit'])) {
      return;
    }

    // $post_id and $post are required
    if (empty($post_id) || empty($post) || did_action('cloudpdf-xyz_save_metabox')) {
      return;
    }

    // Dont' save meta boxes for revisions or autosaves.
    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || is_int(wp_is_post_revision($post)) || is_int(wp_is_post_autosave($post))) {
      return;
    }

    // Check user has permission to edit.
    if (!current_user_can('edit_post', $post_id)) {
      return;
    }

    // Check the nonce.
    if (empty($_POST['opengraph-xyz-nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['opengraph-xyz-nonce'])), 'opengraph-xyz')) {
      return;
    }

    // Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
    if (empty($_POST['post_ID']) || absint($_POST['post_ID']) !== $post_id) {
      return;
    }

    $opengraph = isset($_POST['opengraph']) ? wp_kses_post_deep(wp_unslash($_POST['opengraph'])) : array();
    $existing = get_post_meta($post_id, 'opengraph-xyz', true);
    $existing = is_array($existing) ? $existing : array();

    $existing['modifications'] = isset($opengraph['modifications']) ? $opengraph['modifications'] : array();
    $existing['custom_fields'] = isset($opengraph['custom_fields']) ? $opengraph['custom_fields'] : array();
    $existing['post_types'] = isset($opengraph['post_types']) ? array_map('sanitize_text_field', $opengraph['post_types']) : array();

    // Check if post types are selected (server-side validation)
    if (empty($existing['post_types'])) {
      // Set an error message that will be displayed
      set_transient('opengraph_xyz_error', 'Select at least one page type.', 30);
      // Redirect back to the edit page to prevent the update
      wp_redirect(admin_url('post.php?post=' . $post_id . '&action=edit'));
      exit;
    }

    // Update template_version if it's available in the form submission
    if (isset($_POST['template_version'])) {
      $templateVersion = sanitize_text_field($_POST['template_version']);
      $existing['template_version'] = $templateVersion;
    }

    // Save data.
    update_post_meta($post_id, 'opengraph-xyz', $existing);

    do_action('cloudpdf-xyz_save_metabox', $post_id);
  }

  public function handle_template_creation()
  {
    $templateId = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : '';
    $templateName = isset($_POST['template_name']) ? sanitize_text_field($_POST['template_name']) : '';
    $templateVersion = isset($_POST['template_version']) ? sanitize_text_field($_POST['template_version']) : '';

    $new_post = array(
      'post_title' => $templateName,
      'post_status' => 'publish',
      'post_type' => 'opengraph_template',
      'meta_input' => array(
        'opengraph-xyz' => array(
          'template_id' => $templateId,
          'template_version' => $templateVersion,
          'modifications' => array(),
          'custom_fields' => array(),
          'post_types' => array(),
        ),
      ),
    );

    $postId = wp_insert_post($new_post);

    if ($postId) {
      // Redirect to the edit post screen or a confirmation page
      wp_redirect(admin_url('post.php?post=' . $postId . '&action=edit'));
      exit;
    } else {
      // Handle error
      wp_redirect(admin_url('edit.php?post_type=opengraph_template'));
      exit;
    }
  }

  /**
   * Registers custom columns.
   *
   * @param  array $columns Columns.
   * @return array
   */
  public function manage_posts_columns($columns)
  {

    $columns = array(
      'cb' => '<input type="checkbox" />',
      'thumbnail' => __('Preview', 'opengraph-xyz'),
      'title' => __('Title', 'opengraph-xyz'),
      'template_id' => __('ID', 'opengraph-xyz'),
      'template_version' => __('Version', 'opengraph-xyz'),
      'date' => __('Date', 'opengraph-xyz'),
    );

    return $columns;
  }


  /**
   * Displays custom columns.
   *
   * @param  string $column  Column name.
   * @param  int    $post_id Post ID.
   * @return void
   */
  public function manage_posts_custom_column($column, $post_id)
  {

    $meta = get_post_meta($post_id, 'opengraph-xyz', true);

    switch ($column) {
      case 'thumbnail':
        if (isset($meta['template_id']) && isset($meta['template_version'])) {
          // Fetch variables for this template
          $variables = opengraphxyz_get_template_variables($post_id);
          if (!is_wp_error($variables)) {
            $imageUrl = opengraphxyz_generate_image_url($meta['template_id'], $meta['template_version'], $variables);
          } else {
            // Fallback to default if variables can't be fetched
            $imageUrl = opengraphxyz_generate_image_url($meta['template_id'], $meta['template_version']);
          }
          echo '<img src="' . esc_url($imageUrl) . '" alt="Template Preview" style="width: 120px; height: 63px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">';
        } else {
          echo '&mdash;';
        }
        break;
      case 'template_id':
        echo isset($meta['template_id']) ? esc_html($meta['template_id']) : '&mdash;';
        break;
      case 'template_version':
        echo isset($meta['template_version']) ? esc_html($meta['template_version']) : '&mdash;';
        break;
    }
  }


  /**
   * Displays the settings page for the plugin.
   */
  public function display_settings_page()
  {
    include_once 'templates/admin.php';
  }

  public function register_plugin_settings()
  {
    // Register a new setting for the API key
    register_setting('opengraph_xyz_settings', 'opengraph_xyz_api_key');
  }

  /**
   * Registers the form editing metabox.
   *
   * @since 1.0.0
   */
  public function add_meta_boxes()
  {
    add_meta_box(
      'opengraph_template-map_fields',
      __('Modifications', 'opengraph-xyz'),
      array($this, 'display_modifications'),
      null,
      'normal',
      'high'
    );

    add_meta_box(
      'opengraph_template-versions',
      __('Versions', 'opengraph-xyz'),
      array($this, 'display_versions'),
      null,
      'side',
      'default'
    );

    add_meta_box(
      'opengraph_template-post-types',
      __('Page Types', 'opengraph-xyz'),
      array($this, 'display_post_types'),
      null,
      'side',
      'default'
    );
  }

  /**
   * Displays the post types metabox.
   *
   * @since 1.0.0
   */
  public function display_post_types($post)
  {
    include plugin_dir_path(__FILE__) . 'views/post-type.php';
  }

  /**
   * Displays the modifications metabox.
   *
   * @since 1.0.0
   */
  public function display_modifications($post)
  {
    include plugin_dir_path(__FILE__) . 'views/modifications.php';
  }

  /**
   * Displays the versions metabox.
   *
   * @since 1.0.0
   */
  public function display_versions($post)
  {
    include plugin_dir_path(__FILE__) . 'views/versions.php';
  }

  public function modify_add_new_submenu()
  {
    global $submenu;

    // Remove default "Add New" submenu
    if (isset($submenu['edit.php?post_type=opengraph_template'])) {
      foreach ($submenu['edit.php?post_type=opengraph_template'] as $key => $item) {
        if ($item[2] === 'post-new.php?post_type=opengraph_template') {
          unset($submenu['edit.php?post_type=opengraph_template'][$key]);
          break;
        }
      }
    }

    add_submenu_page(
      'edit.php?post_type=opengraph_template',
      'Select OG Template',
      'Select OG Template',
      'manage_options', // Standard capability
      'opengraph_template_selection',
      array($this, 'display_template_selection_page'),
      1
    );
  }

  public function display_template_selection_page()
  {    
    // Fetch templates from opengraph.xyz API and display them
    include_once 'templates/template_selection.php';
  }

  // Function to handle AJAX request for fetching variables
  public function fetch_template_variables()
  {
    $post_id = isset($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : '';
    $template_version = isset($_POST['template_version']) ? sanitize_text_field($_POST['template_version']) : '';

    $meta = get_post_meta($post_id, 'opengraph-xyz', true);

    if (!is_array($meta) || empty($meta['template_id'])) {
      wp_send_json_error(array('message' => 'Invalid template ID.'));
      return;
    }

    $apiKey = get_option('opengraph_xyz_api_key');
    $args = array();
    if (!empty($apiKey)) {
      $args['headers'] = array('api-key' => $apiKey);
    }

    // Fetch variables as per template_id and version
    $apiUrl = opengraphxyz_get_base_api_url() . '/v2/api/image-editor-templates/' . $meta['template_id'] . '/versions/' . $template_version;
    $response = wp_remote_get($apiUrl, $args);

    if (is_wp_error($response)) {
      wp_send_json_error(array('message' => 'Error fetching template variables.'));
      return;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!isset($data['data']['variables'])) {
      wp_send_json_error(array('message' => 'No variables found for this template version.'));
      return;
    }

    $variables = $data['data']['variables'];

    wp_send_json_success(array('variables' => $variables));
  }

  // Function to handle AJAX request for getting create template URL
  public function get_create_template_url()
  {
    $templateId = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : '';
    
    if (empty($templateId)) {
      wp_send_json_error(array('message' => 'Template ID is required.'));
      return;
    }
    
    $url = opengraphxyz_get_create_template_url($templateId);
    wp_send_json_success(array('url' => $url));
  }

  private function fetch_templates($apiKey = '')
  {
    $apiUrl = opengraphxyz_get_base_api_url() . '/v2/api/image-editor-templates';
    $args = array();

    // Include headers only if api-key is provided
    if (!empty($apiKey)) {
      $args['headers'] = array('api-key' => $apiKey);
    }

    $response = wp_remote_get($apiUrl, $args);

    if (is_wp_error($response)) {
      // Handle error
      return array();
    }

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
  }

  public function add_custom_button()
  {
    global $pagenow, $typenow, $post;

    if (($pagenow == 'edit.php' || $pagenow == 'post.php') && $typenow == 'opengraph_template') {
      $customButton = '<a href="edit.php?post_type=opengraph_template&page=opengraph_template_selection" class="page-title-action title-new-template">Select OG Template</a>';
      
      // Add Edit on Open Graph button only on post edit page (post.php) and if post has a template
      $editButton = '';
      if ($pagenow == 'post.php' && $post && $post->ID) {
        $meta = \get_post_meta($post->ID, 'opengraph-xyz', true);
        if (is_array($meta) && !empty($meta['template_id'])) {
          $editUrl = \opengraphxyz_get_edit_template_url($meta['template_id']);
          $editButton = '<a href="' . \esc_url($editUrl) . '" target="_blank" class="page-title-action title-edit-template">Edit on Open Graph</a>';
        }
      }
      
      // Combine both buttons into one string (Edit on Open Graph first, then Select OG Template)
      $allButtons = $editButton . $customButton;
      
      echo '<script type="text/javascript">
              jQuery(document).ready(function($) {
                  var allButtons = \'' . $allButtons . '\';
                  $(".wrap .page-title-action").after(allButtons);
              });
          </script>';
    }
  }

  private function is_opengraph_template_selection_page()
  {
    return isset($_GET['page']) && $_GET['page'] === 'opengraph_template_selection';
  }

  public function hide_add_new_button()
  {
    global $pagenow, $typenow;

    if (($pagenow == 'edit.php' || $pagenow == 'post.php') && $typenow == 'opengraph_template') {
      echo '<style type="text/css">
              .page-title-action:not([class*=" "]) { display: none !important; }
          </style>';
    }
  }

  // Enqueue scripts for admin pages
  public function enqueue_admin_scripts()
  {
    global $pagenow, $typenow;

    // Only run on the opengraph_template post type pages
    if (($pagenow === 'post.php' || $pagenow === 'post-new.php') && $typenow === 'opengraph_template') {
      // Enqueue jQuery as dependency
      wp_enqueue_script('jquery');
      
      // Add inline script with proper jQuery dependency
      wp_add_inline_script('jquery', '
        jQuery(document).ready(function($) {
            $("#post").on("submit", function(e) {
                // Check if any post type checkboxes are checked
                var checkedPostTypes = $("input[name=\'opengraph[post_types][]\']:checked");
                if (checkedPostTypes.length === 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    showErrorToast("Select at least one page type.");
                    return false;
                }
            });
        });
        
        function showErrorToast(message) {
            // Remove any existing error toast
            jQuery("#opengraph-xyz-error-toast").remove();
            
            var toast = \'<div id="opengraph-xyz-error-toast" class="opengraph-xyz-toast opengraph-xyz-toast-error"><span class="opengraph-xyz-toast-message"><span class="dashicons dashicons-warning"></span>\' + message + \'</span><button type="button" class="opengraph-xyz-toast-close" onclick="closeErrorToast()"><span class="dashicons dashicons-no-alt"></span></button></div>\';
            jQuery("body").append(toast);
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                closeErrorToast();
            }, 5000);
        }
        
        function closeErrorToast() {
            const toast = document.getElementById("opengraph-xyz-error-toast");
            if (toast) {
                toast.classList.add("hiding");
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
        }
      ');
      
      // Add CSS for the error toast
      wp_add_inline_style('wp-admin', '
        .opengraph-xyz-toast-error {
          position: fixed;
          top: 32px;
          right: 20px;
          z-index: 999999;
          max-width: 400px;
          padding: 12px 16px;
          border-radius: 4px;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
          display: flex;
          align-items: center;
          justify-content: space-between;
          animation: slideInRight 0.3s ease-out;
          font-size: 14px;
          line-height: 1.4;
          background-color: #f8d7da;
          border: 1px solid #f5c6cb;
          color: #721c24;
        }
        
        .opengraph-xyz-toast-error .opengraph-xyz-toast-close {
          background: none;
          border: none;
          cursor: pointer;
          padding: 0;
          margin-left: 12px;
          color: inherit;
          opacity: 0.7;
          transition: opacity 0.2s;
        }
        
        .opengraph-xyz-toast-error .opengraph-xyz-toast-close:hover {
          opacity: 1;
        }
        
        .opengraph-xyz-toast-error .opengraph-xyz-toast-close .dashicons {
          font-size: 16px;
          width: 16px;
          height: 16px;
        }
        
        .opengraph-xyz-toast-error.hiding {
          animation: slideOutRight 0.3s ease-in forwards;
        }
      ');
    }
  }

  /**
   * Check for API key and conditionally display OG Manager content
   */
  public function check_api_key_for_og_manager()
  {
    global $pagenow, $typenow;

    // Only run on the OG Manager page (edit.php for opengraph_template post type)
    if ($pagenow !== 'edit.php' || $typenow !== 'opengraph_template') {
      return;
    }

    $apiKey = get_option('opengraph_xyz_api_key');
    
    if (empty($apiKey)) {
      // Hide the default WordPress table and related elements, but keep the title
      echo '<style type="text/css">
              .wp-list-table { display: none !important; }
              .tablenav { display: none !important; }
              .page-title-action { display: none !important; }
              .subsubsub { display: none !important; }
              .search-box { display: none !important; }
          </style>';
      
      // Add JavaScript to insert the message after the title (only on OG Manager page)
      echo '<script type="text/javascript">
              jQuery(document).ready(function($) {
                  // Only run on the OG Manager page
                  if (window.location.href.indexOf("edit.php?post_type=opengraph_template") !== -1 && window.location.href.indexOf("page=") === -1) {
                      var message = \'<div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 6px; margin: 20px 0;"><p style="font-size: 1.2em; color: #666; margin-bottom: 20px;">Connect your Open Graph account with an API Key to see your OG Image Templates.</p><a href="' . esc_url(admin_url('edit.php?post_type=opengraph_template&page=og-xyz-settings')) . '" style="background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Add an API Key</a></div>\';
                      $(".wrap h1").after(message);
                  }
              });
          </script>';
    }
  }

  /**
   * Display error messages as toast notifications
   */
  public function display_error_messages()
  {
    global $pagenow, $typenow;

    // Only run on the opengraph_template post type pages
    if ($pagenow !== 'post.php' && $pagenow !== 'post-new.php' || $typenow !== 'opengraph_template') {
      return;
    }

    $error_message = get_transient('opengraph_xyz_error');
    if ($error_message) {
      delete_transient('opengraph_xyz_error');
      
      // Display error toast notification
      echo '<div id="opengraph-xyz-error-toast" class="opengraph-xyz-toast opengraph-xyz-toast-error">
              <span class="opengraph-xyz-toast-message">
                <span class="dashicons dashicons-warning"></span>
                ' . esc_html($error_message) . '
              </span>
              <button type="button" class="opengraph-xyz-toast-close" onclick="closeErrorToast()">
                <span class="dashicons dashicons-no-alt"></span>
              </button>
            </div>';
      
      // Add CSS and JavaScript for the error toast
      echo '<style>
              .opengraph-xyz-toast-error {
                position: fixed;
                top: 32px;
                right: 20px;
                z-index: 999999;
                max-width: 400px;
                padding: 12px 16px;
                border-radius: 4px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                display: flex;
                align-items: center;
                justify-content: space-between;
                animation: slideInRight 0.3s ease-out;
                font-size: 14px;
                line-height: 1.4;
                background-color: #f8d7da;
                border: 1px solid #f5c6cb;
                color: #721c24;
              }
              
              .opengraph-xyz-toast-error .opengraph-xyz-toast-close {
                background: none;
                border: none;
                cursor: pointer;
                padding: 0;
                margin-left: 12px;
                color: inherit;
                opacity: 0.7;
                transition: opacity 0.2s;
              }
              
              .opengraph-xyz-toast-error .opengraph-xyz-toast-close:hover {
                opacity: 1;
              }
              
              .opengraph-xyz-toast-error .opengraph-xyz-toast-close .dashicons {
                font-size: 16px;
                width: 16px;
                height: 16px;
              }
              
              .opengraph-xyz-toast-error.hiding {
                animation: slideOutRight 0.3s ease-in forwards;
              }
            </style>';
      
      echo '<script>
              function closeErrorToast() {
                const toast = document.getElementById("opengraph-xyz-error-toast");
                if (toast) {
                  toast.classList.add("hiding");
                  setTimeout(() => {
                    toast.remove();
                  }, 300);
                }
              }
              
              // Auto-hide error toast after 5 seconds
              document.addEventListener("DOMContentLoaded", function() {
                const toast = document.getElementById("opengraph-xyz-error-toast");
                if (toast) {
                  setTimeout(() => {
                    closeErrorToast();
                  }, 5000);
                }
              });
            </script>';
    }
  }
}