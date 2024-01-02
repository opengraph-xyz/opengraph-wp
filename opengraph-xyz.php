<?php
/*
Plugin Name: OpenGraph.xyz
Description: Dynamic Open Graph images for your website
Version: 1.0.0
Author: OpenGraph.xyz
Author URI: https://opengraph.xyz
*/

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Returns the main plugin instance.
 *
 * @since  1.0.0
 * @return OpenGraphXYZ\Plugin
 */
function opengraph() {
	return OpenGraphXYZ\Plugin::instance();
}

// Start the plugin.
opengraph();