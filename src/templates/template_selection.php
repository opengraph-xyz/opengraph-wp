<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Check user capabilities
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

// Template selection view
?>

<div class="wrap">
    <h1 style="margin-bottom: 20px">Choose OG Image Template</h1>

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

    <?php if ( isset( $templates['edges'] ) && is_array( $templates['edges'] ) ) : ?>
      <div class="template-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(500px, 1fr)); gap: 20px;">
          <?php foreach ( $templates['edges'] as $edge ) :
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
              <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="template-form">
                  <?php wp_nonce_field( 'opengraph_xyz_select_template_action', 'opengraph_xyz_select_template_nonce' ); ?>

                  <input type="hidden" name="action" value="create_opengraph_template">
                  <input type="hidden" name="template_id" value="<?php echo esc_attr( $id ); ?>">
                  <input type="hidden" name="template_name" value="<?php echo esc_attr( $name ); ?>">
                  <input type="hidden" name="template_version" value="<?php echo esc_attr( $versionNumber ); ?>">
              <?php endif; ?>
                  <div onclick="this.parentNode.submit()" style="cursor: pointer; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 1px -1px rgba(0,0,0,.1); border: 1px solid #dcdcde;">
                      <div class="template-card" style="position: relative; padding-top: 52.5%; background-color: #fff">
                          <img src="<?php echo esc_url( $imageUrl ); ?>" alt="<?php echo esc_attr( $name ); ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                      </div>
                      <div class="template-details" style="width: 100%; background: rgba(255,255,255,.65); color: #000; text-align: center; font-size: 16px;">
                        <div style="padding: 1rem; display: flex; align-items: center; justify-content: center; gap: .5rem; border-top: 1px solid #dcdcde;">
                          <?php echo esc_html( $name ); ?>
                          <?php if ( !isset( $organizationId ) ) : ?>
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
        <p>No templates available.</p>
    <?php endif; ?>
</div>