<?php
/**
 * Uninstall plugin.
 *
 * Uninstalling the plugin deletes products, pages, tables, and options.
 *
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb, $wp_version;

// Options.
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'opengraph_xyz\_%';" );

// Products.
$wpdb->query(
	"DELETE a,b
	FROM {$wpdb->posts} a
	LEFT JOIN {$wpdb->postmeta} b
		ON (a.ID = b.post_id)
	WHERE a.post_type = 'opengraph_template'"
);

// Clear any cached data that has been removed.
wp_cache_flush();