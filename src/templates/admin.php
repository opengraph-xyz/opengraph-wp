<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;


// Check user capabilities
if (!current_user_can('manage_options')) {
    return;
}

?>
<div class="wrap">
    <h1>Settings</h1>

    <!-- Banner for API Key information -->
    <div class="api-key-info-banner" style="margin-top: 10px; max-width: 700px; border: 1px solid #007cba; background-color: #eef5fa; color: #007cba; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <strong>Need an API Key?</strong> Obtain your API key from your <a href="https://www.opengraph.xyz/get-started" target="_blank">OpenGraph.xyz account</a>. In the OpenGraph.xyz dashboard, go to <em>Settings > API Keys</em> to find your existing keys or create a new one by clicking on <em>Create new API key</em>.
    </div>
    <div style="max-width: 740px;">
      <form method="post" action="options.php">
          <?php
          settings_fields('opengraph_xyz_settings');
          do_settings_sections('opengraph_xyz_settings');
          ?>
            <table class="form-table">
              <tr valign="top">
                  <th scope="row">API Key</th>
                  <td><input type="text" name="opengraph_xyz_api_key" style="width: 100%" value="<?php echo esc_attr(get_option('opengraph_xyz_api_key')); ?>" /></td>
              </tr>
          </table>
          <?php submit_button(); ?>
      </form>
    </div>
</div>