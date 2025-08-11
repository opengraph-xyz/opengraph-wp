<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;


// Check user capabilities
if (!current_user_can('manage_options')) {
    return;
}

// Get the settings URL using the helper function
$settings_url = opengraphxyz_get_settings_url();

// Check if user was redirected from OpenGraph.xyz
$redirected_from_opengraphxyz = isset($_GET['redirected_from_opengraphxyz']) && $_GET['redirected_from_opengraphxyz'] === 'true';

// Check if settings were updated
$settings_updated = isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true';

$settings_errors_list = get_settings_errors('opengraph_xyz_settings');
$has_settings_errors = !empty($settings_errors_list);

?>

<div class="wrap">
    <h1>Settings</h1>

    <?php if ($settings_updated && !$has_settings_errors): ?>
        <!-- Success Toast Notification -->
        <div id="opengraph-xyz-toast" class="opengraph-xyz-toast opengraph-xyz-toast-success">
            <span class="opengraph-xyz-toast-message">
                <span class="dashicons dashicons-yes-alt"></span>
                API key saved successfully!
            </span>
            <button type="button" class="opengraph-xyz-toast-close" onclick="closeToast()">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
    <?php endif; ?>
    <?php if ($has_settings_errors): ?>
        <div id="opengraph-xyz-error-toast" class="opengraph-xyz-toast opengraph-xyz-toast-error">
            <span class="opengraph-xyz-toast-message">
                <span class="dashicons dashicons-warning"></span>
                <?php
                // Concatenate any messages from add_settings_error
                echo esc_html(implode(' ', array_map(
                    fn($e) => is_string($e['message']) ? $e['message'] : '',
                    $settings_errors_list
                )));
                ?>
            </span>
            <button type="button" class="opengraph-xyz-toast-close" onclick="closeErrorToast()">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($redirected_from_opengraphxyz): ?>
        <!-- Message for users redirected from OpenGraph.xyz -->
        <div class="api-key-info-banner" style="margin-top: 10px; max-width: 700px; border: 1px solid #007cba; background-color: #eef5fa; color: #007cba; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
            <strong>Paste your API key from OpenGraph.xyz in the box below and click save</strong>
        </div>
    <?php else: ?>
        <!-- Banner for API Key information -->
        <div class="api-key-info-banner" style="margin-top: 10px; max-width: 700px; border: 1px solid #007cba; background-color: #eef5fa; color: #007cba; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
            <strong>Need an API Key?</strong> Obtain your API key from your OpenGraph.xyz account by clicking the button below. In the OpenGraph.xyz dashboard, go to <em>Settings > API Keys</em> to find your existing keys or create a new one by clicking on <em>Create new API key</em>.

            <div style="margin-top:20px;">
                <a href="<?php echo esc_url($settings_url); ?>" target="_blank" class="button button-primary" style="background: #0073aa; border-color: #0073aa; color: white; padding: 8px 16px; font-size: 14px; line-height: 1.4; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block;">
                    <span class="dashicons dashicons-external" style="margin-right: 5px;"></span>
                    Go to OpenGraph.xyz Settings
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Settings Button -->


    <div style="max-width: 740px;">
        <form method="post" action="options.php">
            <?php
            settings_fields('opengraph_xyz_settings');
            do_settings_sections('opengraph_xyz_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">API Key</th>
                    <td>
                        <textarea 
                            name="opengraph_xyz_api_key" 
                            style="width: 100%; min-height: 100px; font-family: 'Courier New', monospace; font-size: 13px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"
                            placeholder="Paste your OpenGraph.xyz API key here..."
                        ><?php echo esc_textarea(get_option('opengraph_xyz_api_key')); ?></textarea>
                        <p class="description">Your API key will be securely stored and used to connect to OpenGraph.xyz services.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
</div>

<?php include_once plugin_dir_path(__FILE__) . '../ui/loading-overlay.php'; ?>

<style>
    .opengraph-xyz-toast {
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
    }

    .opengraph-xyz-toast-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .opengraph-xyz-toast-message {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
    }

    .opengraph-xyz-toast-close {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        margin-left: 12px;
        color: inherit;
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    .opengraph-xyz-toast-close:hover {
        opacity: 1;
    }

    .opengraph-xyz-toast-close .dashicons {
        font-size: 16px;
        width: 16px;
        height: 16px;
    }

    .opengraph-xyz-toast-error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .opengraph-xyz-toast.hiding {
        animation: slideOutRight 0.3s ease-in forwards;
    }
</style>

<script>
    function closeToast() {
        const toast = document.getElementById('opengraph-xyz-toast');
        if (toast) {
            toast.classList.add('hiding');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    }

    // Auto-hide toast after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('opengraph-xyz-toast');
        if (toast) {
            setTimeout(() => {
                closeToast();
            }, 5000);
        }
    });

    function closeErrorToast() {
        const toast = document.getElementById('opengraph-xyz-error-toast');
        if (toast) {
            toast.classList.add('hiding');
            setTimeout(() => toast.remove(), 300);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const errorToast = document.getElementById('opengraph-xyz-error-toast');
        if (errorToast) {
            setTimeout(() => {
                closeErrorToast();
            }, 5000);
        }
    });

    // Show loading overlay when form is submitted
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action="options.php"]');
        if (form) {
            form.addEventListener('submit', function() {
                showLoadingOverlay();
            });
        }
    });
</script>