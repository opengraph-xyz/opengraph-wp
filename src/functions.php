<?php
/**
 * Helper functions.
 *
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Get the configurable OpenGraph base URL
 * Can be overridden in wp-config.php with: define('OPENGRAPHXYZ_BASE_URL', 'http://opengraph.enter.me:4600');
 * 
 * @return string The base URL for OpenGraph services
 */
function opengraphxyz_get_base_url() {
    return defined('OPENGRAPHXYZ_BASE_URL') ? OPENGRAPHXYZ_BASE_URL : 'https://opengraph.enter.nl';
}
function opengraphxyz_get_template_variables($post_id)
{
    $meta = get_post_meta($post_id, 'opengraph-xyz', true);

    if (!is_array($meta) || empty($meta['template_id']) || empty($meta['template_version'])) {
        return new WP_Error('no_opengraph-xyz', __('No OpenGraph template or version found for this post.', 'opengraph-xyz'));
    }

    $cache_key = 'opengraph-xyz_variables_' . $meta['template_id'] . '_' . $meta['template_version'];
    $cached = get_transient($cache_key);

    if (false !== $cached) {
        return $cached;
    }

    $apiKey = get_option('opengraph_xyz_api_key');
    $args = array();
    if (!empty($apiKey)) {
        $args['headers'] = array('api-key' => $apiKey);
    }

    $response = wp_remote_get('https://api.opengraph.xyz/v2/api/image-editor-templates/' . $meta['template_id'] . '/versions/' . $meta['template_version'], $args);

    if (is_wp_error($response)) {
        return $response;
    }

    $template = json_decode(wp_remote_retrieve_body($response), true);

    $variables = isset($template['data']) && isset($template['data']['variables']) ? $template['data']['variables'] : array();

    set_transient($cache_key, $variables, 10 * MINUTE_IN_SECONDS);

    return $variables;
}

function opengraphxyz_get_template_versions($post_id)
{
    $meta = get_post_meta($post_id, 'opengraph-xyz', true);

    if (!is_array($meta) || empty($meta['template_id']) || empty($meta['template_version'])) {
        return new WP_Error('no_opengraph-xyz', __('No OpenGraph template or version found for this post.', 'opengraph-xyz'));
    }

    $apiKey = get_option('opengraph_xyz_api_key');
    $args = array();
    if (!empty($apiKey)) {
        $args['headers'] = array('api-key' => $apiKey);
    }

    $response = wp_remote_get('https://api.opengraph.xyz/v2/api/image-editor-templates/' . $meta['template_id'] . '/versions', $args);

    if (is_wp_error($response)) {
        return $response;
    }

    $versions = json_decode(wp_remote_retrieve_body($response), true);

    return isset($versions) ? $versions : array();
}