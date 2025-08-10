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

function opengraphxyz_get_base_api_url() {
    return defined('OPENGRAPHXYZ_BASE_API_URL') ? OPENGRAPHXYZ_BASE_API_URL : 'https://api.opengraph.xyz';
}

/**
 * Get the create template base URL without URL encoding for JavaScript concatenation
 * 
 * @return string The base URL for creating templates
 */
function opengraphxyz_get_create_template_baseurl_raw() {
    $base_url = opengraphxyz_get_base_url();
    return $base_url . "/register?redirect=/org/[organizationId]/create-template/";
}

/**
 * Get the OpenGraph.xyz settings URL with wp parameter
 * 
 * @return string The settings URL for OpenGraph.xyz
 */
function opengraphxyz_get_settings_url() {
    $base_url = opengraphxyz_get_base_url();
    $current_domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? '');
    
    return $base_url . "/register?redirect=/org/[organizationId]/settings/api-keys?wp=" . urlencode($current_domain);
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

    $response = wp_remote_get(opengraphxyz_get_base_api_url() . '/v2/api/image-editor-templates/' . $meta['template_id'] . '/versions/' . $meta['template_version'], $args);

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

    $response = wp_remote_get(opengraphxyz_get_base_api_url() . '/v2/api/image-editor-templates/' . $meta['template_id'] . '/versions', $args);

    if (is_wp_error($response)) {
        return $response;
    }

    $versions = json_decode(wp_remote_retrieve_body($response), true);

    return isset($versions) ? $versions : array();
}

/**
 * Generate the OG image URL with the correct number of underscores based on template variables
 * 
 * @param string $template_id The template ID
 * @param string $template_version The template version
 * @param array $variables Optional array of template variables (if not provided, will be fetched)
 * @return string The complete image URL
 */
function opengraphxyz_generate_image_url($template_id, $template_version, $variables = null)
{
    // Start with the base URL
    $imageUrl = "https://ogcdn.net/{$template_id}/v{$template_version}/";
    
    // If variables are not provided, try to get them from the template
    if ($variables === null) {
        // Create a temporary post meta structure to use the existing function
        $temp_meta = array(
            'template_id' => $template_id,
            'template_version' => $template_version
        );
        
        // We can't easily get variables without a post ID, so we'll use a default pattern
        // This is a fallback for when variables aren't provided
        $imageUrl .= '_/og.png';
        return $imageUrl;
    }
    
    // Add underscores for each modification in each variable
    foreach ($variables as $variable) {
        if (isset($variable['modifications']) && is_array($variable['modifications'])) {
            foreach ($variable['modifications'] as $modification) {
                $imageUrl .= '_' . '/';
            }
        }
    }
    
    // Clean up and return the final URL
    $imageUrl = rtrim($imageUrl, '/') . '/og.png';
    return $imageUrl;
}