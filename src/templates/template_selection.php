<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Check user capabilities
if (!current_user_can('manage_options')) {
    return;
}

$create_template_url = opengraphxyz_get_base_url() . '/generate';

$apiKey = get_option('opengraph_xyz_api_key'); // Assuming the API key is stored with this name
$templates = $this->fetch_templates($apiKey);

// Pass API key status to the template
$hasApiKey = !empty($apiKey);

$userTemplates = array();

if (isset($templates['edges']) && is_array($templates['edges'])) {
    foreach ($templates['edges'] as $edge) {
        $template = $edge['node'];
        if (isset($template['organizationId']) && !empty($template['organizationId'])) {
            $userTemplates[] = $edge;
        }
    }
}

// Template selection view
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Select OG Image Template</h1>
    <a href="<?php echo esc_url($create_template_url); ?>" target="_blank" rel="noopener noreferrer"
        class="page-title-action">Add new template</a>
    <hr class="wp-header-end">

    <?php if (isset($templates['authenticated']) && !$templates['authenticated']): ?>
        <!-- Banner for unauthenticated users -->
        <div class="unauthenticated-banner"
            style="border: 1px solid #eab308; color: #854d0e; background-color: #ffffcc; padding: 20px; margin-bottom: 20px; border-radius: 6px; display: flex; align-items: center; gap: 10px;">
            <svg style="width: 1.5rem; height: 1.5rem; fill: #eab308; flex-shrink: 0;" width="24" height="24"
                fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V18H19V19Z">
                </path>
            </svg>
            <p style="margin: 0; font-size: 1.2em;">Create unique, custom Open Graph image templates that truly represent
                your brand's identity.</p>
            <a href="https://www.opengraph.xyz/get-started" target="_blank"
                style="text-decoration: underline; font-weight: bold; color: #854d0e; flex-shrink: 0;">Learn More</a>
        </div>
    <?php endif; ?>

    <div
        style="margin-top: 20px; margin-bottom: 20px; border: 1px solid #007cba; background-color: #eef5fa; color: #007cba; padding: 15px; border-radius: 5px;">
        <strong>Choose one of your templates to add it to WordPress.</strong>
    </div>
    <?php if (!empty($userTemplates)): ?>
        <div class="template-grid"
            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(500px, 1fr)); gap: 20px;">
            <?php foreach ($userTemplates as $edge):
                $template = $edge['node'];
                $name = $template['name'];
                $id = $template['id'];
                $version = $template['activeVersion'];
                $versionNumber = $version['versionNumber'];

                // Generate image URL
                $imageUrl = opengraphxyz_generate_image_url($id, $versionNumber, $version['data']['variables']);
                ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="template-form"
                    id="template-form-<?php echo esc_attr($id); ?>">
                    <?php wp_nonce_field('opengraph_xyz_select_template_action', 'opengraph_xyz_select_template_nonce'); ?>

                    <input type="hidden" name="action" value="create_opengraph_template">
                    <input type="hidden" name="template_id" value="<?php echo esc_attr($id); ?>">
                    <input type="hidden" name="template_name" value="<?php echo esc_attr($name); ?>">
                    <input type="hidden" name="template_version" value="<?php echo esc_attr($versionNumber); ?>">

                    <div onclick="handleTemplateClick('<?php echo esc_attr($id); ?>')"
                        style="cursor: pointer; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 1px -1px rgba(0,0,0,.1); border: 1px solid #dcdcde;">
                        <div class="template-card" style="position: relative; padding-top: 52.5%; background-color: #fff">
                            <img src="<?php echo esc_url($imageUrl); ?>" alt="<?php echo esc_attr($name); ?>"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="template-details"
                            style="width: 100%; background: rgba(255,255,255,.65); color: #000; text-align: center; font-size: 16px;">
                            <div
                                style="padding: 1rem; display: flex; align-items: center; justify-content: center; gap: .5rem; border-top: 1px solid #dcdcde;">
                                <?php echo esc_html($name); ?>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endforeach; ?>
        </div>
    <?php elseif (!$hasApiKey): ?>
        <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 6px;">
            <p style="font-size: 1.2em; color: #666; margin-bottom: 20px;">Connect your Open Graph account with an API
                Key to see your custom templates.</p>
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=opengraph_template&page=og-xyz-settings')); ?>"
                style="background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Add
                an API Key</a>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 6px;">
            <p style="font-size: 1.2em; color: #666; margin-bottom: 20px;">You don't have any custom templates yet.</p>
            <a href="<?php echo esc_url($create_template_url); ?>" target="_blank"
                style="background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Create
                Your First Template</a>
        </div>
    <?php endif; ?>
</div>

<?php include_once plugin_dir_path(__FILE__) . '../ui/loading-overlay.php'; ?>
<?php include_once plugin_dir_path(__FILE__) . '../ui/api-key-modal.php'; ?>

<script>
    // Check if user has API key
    const hasApiKey = <?php echo $hasApiKey ? 'true' : 'false'; ?>;

    // Debug: Log template data
    <?php if (isset($templates['edges']) && is_array($templates['edges'])): ?>
        console.log('Template data:', <?php echo json_encode($templates['edges']); ?>);
    <?php endif; ?>

    // Handle template click - check for API key first
    function handleTemplateClick(templateId) {
        if (!hasApiKey) {
            // Show API key modal if no API key
            showApiKeyModal();
            return;
        }

        var formId = "template-form-" + templateId;
        const form = document.getElementById(formId);
        if (form && !form.dataset.submitted) {
            form.dataset.submitted = 'true';
            showLoadingOverlay();
            form.submit();
        }
    }
</script>