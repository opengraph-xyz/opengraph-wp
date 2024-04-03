<?php
/*
Plugin Name: OpenGraph.xyz
Plugin URI: https://github.com/opengraph-xyz/opengraph-wp
Description: Dynamic Open Graph images for your website
Version: 1.0.0
Author: OpenGraph.xyz
Author URI: https://opengraph.xyz
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Returns the main plugin instance.
 *
 * @since  1.0.0
 * @return OpenGraphXYZ\Plugin
 */
function opengraphxyz_init()
{
    return OpenGraphXYZ\Plugin::instance();
}

// Start the plugin.
opengraphxyz_init();