<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

$troubleshooting_docs_url = opengraphxyz_get_wordpress_docs_url('troubleshooting');
$contact_url = opengraphxyz_get_base_url();
?>

<div class="opengraph-xyz-troubleshooting">
    <h3><?php esc_html_e('Having issues? Is your new OG image not showing up?', 'opengraph-xyz'); ?></h3>
    <ul>
        <li><?php esc_html_e('Confirm the template mapping and page types are saved.', 'opengraph-xyz'); ?></li>
        <li><?php esc_html_e('Check that the selected version matches the latest published template version.', 'opengraph-xyz'); ?>
        </li>
        <li><?php esc_html_e('Clear your WordPress, CDN, and social-network preview caches.', 'opengraph-xyz'); ?></li>
        <li><?php esc_html_e('View the page source and confirm the og:image tag contains an https://ogcdn.net/ URL.', 'opengraph-xyz'); ?>
        </li>
        <li><?php esc_html_e('If there are multiple og:image tags, confirm the OpenGraph.xyz tag is furthest down. Temporarily deactivate any plugin that outputs a later tag, then contact us if the conflict remains.', 'opengraph-xyz'); ?>
        </li>
    </ul>

    <h4><?php esc_html_e('Still having issues?', 'opengraph-xyz'); ?></h4>
    <p class="opengraph-xyz-troubleshooting-actions">
        <a class="button button-primary" href="<?php echo esc_url($troubleshooting_docs_url); ?>" target="_blank"
            rel="noopener noreferrer">
            <?php esc_html_e('Troubleshooting Docs', 'opengraph-xyz'); ?>
        </a>
        <a class="button" href="<?php echo esc_url($contact_url); ?>" target="_blank" rel="noopener noreferrer">
            <?php esc_html_e('Contact Us', 'opengraph-xyz'); ?>
        </a>
    </p>
</div>

<style>
    .opengraph-xyz-troubleshooting h3 {
        margin-top: 0;
    }

    .opengraph-xyz-troubleshooting ul {
        list-style: disc;
        margin-left: 20px;
    }

    .opengraph-xyz-troubleshooting h4 {
        margin-bottom: 8px;
    }

    .opengraph-xyz-troubleshooting-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 0;
    }
</style>