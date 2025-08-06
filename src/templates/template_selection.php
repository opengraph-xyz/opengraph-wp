<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Check user capabilities
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

// Get the create template base URL
$create_template_base_url = opengraphxyz_get_create_template_baseurl_raw();

$apiKey = get_option('opengraph_xyz_api_key'); // Assuming the API key is stored with this name
$templates = $this->fetch_templates($apiKey);

// Pass API key status to the template
$hasApiKey = !empty($apiKey);

// Split templates into user templates and stock templates
$userTemplates = array();
$stockTemplates = array();

if ( isset( $templates['edges'] ) && is_array( $templates['edges'] ) ) {
    foreach ( $templates['edges'] as $edge ) {
        $template = $edge['node'];
        if ( isset( $template['organizationId'] ) && !empty( $template['organizationId'] ) ) {
            $userTemplates[] = $edge;
        } else {
            $stockTemplates[] = $edge;
        }
    }
}

// Determine default tab - if user has templates, default to "Your Templates", otherwise "Stock Templates"
$defaultTab = !empty($userTemplates) ? 'your-templates' : 'stock-templates';

// Template selection view
?>

<div class="wrap">
    <h1 style="margin-bottom: 20px">Select OG Image Template</h1>

    <?php if ( isset( $templates['authenticated'] ) && ! $templates['authenticated'] ) : ?>
        <!-- Banner for unauthenticated users -->
        <div class="unauthenticated-banner" style="border: 1px solid #eab308; color: #854d0e; background-color: #ffffcc; padding: 20px; margin-bottom: 20px; border-radius: 6px; display: flex; align-items: center; gap: 10px;">
            <svg style="width: 1.5rem; height: 1.5rem; fill: #eab308; flex-shrink: 0;" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
              <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V18H19V19Z"></path>
            </svg>
            <p style="margin: 0; font-size: 1.2em;">Create unique, custom Open Graph image templates that truly represent your brand's identity.</p>
            <a href="https://www.opengraph.xyz/get-started" target="_blank" style="text-decoration: underline; font-weight: bold; color: #854d0e; flex-shrink: 0;">Learn More</a>
        </div>
    <?php endif; ?>

    <!-- Tab Navigation -->
    <div class="tab-navigation" style="margin-bottom: 0; border-bottom: 1px solid #dcdcde;">
        <button class="tab-button active" data-tab="your-templates" style="padding: 12px 24px; margin-right: 0; border: 1px solid #dcdcde; border-bottom: none; background: #fff; cursor: pointer; border-radius: 4px 4px 0 0; font-weight: 500; color: #1d2327; position: relative; top: 1px;">
            Your Templates (<?php echo count($userTemplates); ?>)
        </button>
        <button class="tab-button" data-tab="stock-templates" style="padding: 12px 24px; margin-right: 0; border: 1px solid #dcdcde; border-bottom: none; background: #f9f9f9; cursor: pointer; border-radius: 4px 4px 0 0; font-weight: 500; color: #646970; position: relative; top: 1px;">
            Stock Templates (<?php echo count($stockTemplates); ?>)
        </button>
    </div>

    <!-- Your Templates Tab -->
    <div id="your-templates" class="tab-content" style="display: <?php echo $defaultTab === 'your-templates' ? 'block' : 'none'; ?>;">
        <div style="margin-top: 20px; margin-bottom: 20px; max-width: 700px; border: 1px solid #007cba; background-color: #eef5fa; color: #007cba; padding: 15px; border-radius: 5px;">
            <strong>Click a template card to create a row in OG Manager and start matching variables</strong>
        </div>
        <?php if ( !empty($userTemplates) ) : ?>
            <div class="template-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(500px, 1fr)); gap: 20px;">
                <?php foreach ( $userTemplates as $edge ) :
                    $template       = $edge['node'];
                    $name           = $template['name'];
                    $id             = $template['id'];
                    $premium        = $template['isPremium'];
                    $organizationId = $template['organizationId'];
                    $version        = $template['activeVersion'];
                    $versionNumber  = $version['versionNumber'];
                    
                    // Generate image URL
                    $imageUrl = "https://ogcdn.net/{$id}/v{$versionNumber}/";
                    foreach ( $version['data']['variables'] as $variable ) {
                        foreach ( $variable['modifications'] as $modification ) {
                            $imageUrl .= '_' . '/';
                        }
                    }
                    $imageUrl = rtrim( $imageUrl, '/' ) . '/og.png';
                    ?>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="template-form" id="template-form-<?php echo esc_attr( $id ); ?>">
                        <?php wp_nonce_field( 'opengraph_xyz_select_template_action', 'opengraph_xyz_select_template_nonce' ); ?>

                        <input type="hidden" name="action" value="create_opengraph_template">
                        <input type="hidden" name="template_id" value="<?php echo esc_attr( $id ); ?>">
                        <input type="hidden" name="template_name" value="<?php echo esc_attr( $name ); ?>">
                        <input type="hidden" name="template_version" value="<?php echo esc_attr( $versionNumber ); ?>">
                        
                        <div onclick="handleTemplateClick('<?php echo esc_attr( $id ); ?>', 'user')" style="cursor: pointer; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 1px -1px rgba(0,0,0,.1); border: 1px solid #dcdcde;">
                            <div class="template-card" style="position: relative; padding-top: 52.5%; background-color: #fff">
                                <img src="<?php echo esc_url( $imageUrl ); ?>" alt="<?php echo esc_attr( $name ); ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="template-details" style="width: 100%; background: rgba(255,255,255,.65); color: #000; text-align: center; font-size: 16px;">
                                <div style="padding: 1rem; display: flex; align-items: center; justify-content: center; gap: .5rem; border-top: 1px solid #dcdcde;">
                                    <?php echo esc_html( $name ); ?>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 6px;">
                <p style="font-size: 1.2em; color: #666; margin-bottom: 20px;">You don't have any custom templates yet.</p>
                <a href="https://www.opengraph.xyz/get-started" target="_blank" style="background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Create Your First Template</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Stock Templates Tab -->
    <div id="stock-templates" class="tab-content" style="display: <?php echo $defaultTab === 'stock-templates' ? 'block' : 'none'; ?>;">
        <div style="margin-top: 20px; margin-bottom: 20px; max-width: 700px; border: 1px solid #007cba; background-color: #eef5fa; color: #007cba; padding: 15px; border-radius: 5px;">
            <strong>Click a card to create a template based on the example image. (Opens in a new tab).</strong>
        </div>
        <?php if ( !empty($stockTemplates) ) : ?>
            <div class="template-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(500px, 1fr)); gap: 20px;">
                <?php foreach ( $stockTemplates as $edge ) :
                    $template       = $edge['node'];
                    $name           = $template['name'];
                    $id             = $template['id'];
                    $premium        = $template['isPremium'];
                    $organizationId = $template['organizationId'];
                    $version        = $template['activeVersion'];
                    $versionNumber  = $version['versionNumber'];
                    
                    // Generate image URL
                    $imageUrl = "https://ogcdn.net/{$id}/v{$versionNumber}/";
                    foreach ( $version['data']['variables'] as $variable ) {
                        foreach ( $variable['modifications'] as $modification ) {
                            $imageUrl .= '_' . '/';
                        }
                    }
                    $imageUrl = rtrim( $imageUrl, '/' ) . '/og.png';
                    ?>
                    <?php if ( $premium ) : ?>
                        <!-- Premium template -->
                        <a href="https://www.opengraph.xyz/get-started" target="_blank" class="template-card" style="text-decoration: none">
                    <?php else : ?>
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="template-form" id="template-form-<?php echo esc_attr( $id ); ?>">
                            <?php wp_nonce_field( 'opengraph_xyz_select_template_action', 'opengraph_xyz_select_template_nonce' ); ?>

                            <input type="hidden" name="action" value="create_opengraph_template">
                            <input type="hidden" name="template_id" value="<?php echo esc_attr( $id ); ?>">
                            <input type="hidden" name="template_name" value="<?php echo esc_attr( $name ); ?>">
                            <input type="hidden" name="template_version" value="<?php echo esc_attr( $versionNumber ); ?>">
                    <?php endif; ?>
                        <div onclick="handleTemplateClick('<?php echo esc_attr( $id ); ?>', 'stock')" style="cursor: pointer; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 1px -1px rgba(0,0,0,.1); border: 1px solid #dcdcde;">
                            <div class="template-card" style="position: relative; padding-top: 52.5%; background-color: #fff">
                                <img src="<?php echo esc_url( $imageUrl ); ?>" alt="<?php echo esc_attr( $name ); ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="template-details" style="width: 100%; background: rgba(255,255,255,.65); color: #000; text-align: center; font-size: 16px;">
                                <div style="padding: 1rem; display: flex; align-items: center; justify-content: center; gap: .5rem; border-top: 1px solid #dcdcde;">
                                    <?php echo esc_html( $name ); ?>
                                    <?php if ( isset( $premium ) ) : ?>
                                        <span style="font-size: .75rem; line-height: 1rem; padding: .25rem .5rem; border-radius: 9999px; background-color: #ffffcc; color: #854d0e; display: inline-flex; -moz-column-gap: .375rem; column-gap: 0.375rem; align-items: center;">
                                            <svg style="width: 1rem; height: 1rem; fill: #eab308;" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V18H19V19Z"></path>
                                            </svg>
                                            Premium
                                        </span>
                                    <?php else : ?>
                                        <span style="font-size: .75rem; line-height: 1rem; padding: .25rem .5rem; border-radius: 9999px; background-color: #f3f4f6; color: #4b5563; display: inline-flex; -moz-column-gap: .375rem; column-gap: 0.375rem; align-items: center;">
                                            <svg style="width: 0.375rem; height: 0.375rem; fill: #9ca3af;" viewBox="0 0 6 6" aria-hidden="true">
                                                <circle cx="3" cy="3" r="3"></circle>
                                            </svg>
                                            Free
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php if ( $premium ) : ?>
                        </a>
                    <?php else : ?>
                        </form>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p>No stock templates available.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once plugin_dir_path(__FILE__) . '../ui/loading-overlay.php'; ?>
<?php include_once plugin_dir_path(__FILE__) . '../ui/api-key-modal.php'; ?>

<script>
// Check if user has API key
const hasApiKey = <?php echo $hasApiKey ? 'true' : 'false'; ?>;

// Get the create template base URL from PHP
const createTemplateBaseUrl = '<?php echo esc_js($create_template_base_url); ?>';

// Debug: Log template data
<?php if ( isset( $templates['edges'] ) && is_array( $templates['edges'] ) ) : ?>
console.log('Template data:', <?php echo json_encode($templates['edges']); ?>);
<?php endif; ?>

// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Update button styles
            tabButtons.forEach(btn => {
                btn.style.background = '#f9f9f9';
                btn.style.color = '#646970';
                btn.classList.remove('active');
            });
            this.style.background = '#fff';
            this.style.color = '#1d2327';
            this.classList.add('active');
            
            // Show/hide tab content
            tabContents.forEach(content => {
                content.style.display = 'none';
            });
            document.getElementById(targetTab).style.display = 'block';
        });
    });
});

// Handle template click - check for API key first
function handleTemplateClick(templateId, templateType) {
    if (!hasApiKey) {
        // Show API key modal if no API key
        showApiKeyModal();
        return;
    }
    
    if (templateType === 'user') {
        // This is a user template - submit the form
        var formId = "template-form-" + templateId;
        const form = document.getElementById(formId);
        if (form && !form.dataset.submitted) {
            form.dataset.submitted = 'true';
            showLoadingOverlay();
            form.submit();
        }
    } else {
        // This is a stock template - use the existing behavior
        submitTemplateForm(templateId);
    }
}

// Prevent multiple template creations when choosing a template
function submitTemplateForm(templateId) {
    const createTemplateUrl = createTemplateBaseUrl + templateId;
    console.log(createTemplateUrl);
    window.open(createTemplateUrl, '_blank');
}
</script>