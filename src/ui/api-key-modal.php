<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Get the settings URL using the helper function
$settings_url = opengraphxyz_get_settings_url();
?>

<div id="api-key-modal" class="api-key-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; padding: 2rem; border-radius: 8px; max-width: 500px; width: 90%; text-align: center; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);">
        <div style="margin-bottom: 1rem;">
            <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../../assets/icon-128x128.png'); ?>" alt="OpenGraph Logo" style="width: 3rem; height: 3rem; margin: 0 auto 1rem; display: block;">
        </div>
        
        <div class="modal-header" style="margin-bottom: 1rem;">
            <h2 style="margin: 0; color: #333; font-size: 1.5rem;">API Key Required</h2>
        </div>
        
        <div class="modal-body" style="margin-bottom: 2rem;">
            <p style="margin: 0; color: #666; font-size: 1.1rem; line-height: 1.5;">
                You don't have an API key yet. To use Open Graph templates, you need to register and get an API key.
            </p>
        </div>
        
        <div class="modal-footer" style="display: flex; gap: 1rem; justify-content: center;">
            <button type="button" class="button button-secondary" onclick="closeApiKeyModal()" style="padding: 0.5rem 1rem; border: 1px solid #ddd; background: #f9f9f9; border-radius: 4px; cursor: pointer;">
                Cancel
            </button>
            <a id="register-link" href="<?php echo esc_url($settings_url); ?>" target="_blank" class="button button-primary" style="padding: 0.5rem 1rem; background: #0073aa; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">
                Get API Key
            </a>
        </div>
    </div>
</div>

<script>
function showApiKeyModal() {
    const modal = document.getElementById('api-key-modal');
    modal.style.display = 'flex';
}

function closeApiKeyModal() {
    const modal = document.getElementById('api-key-modal');
    modal.style.display = 'none';
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('api-key-modal');
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeApiKeyModal();
        }
    });
});
</script> 