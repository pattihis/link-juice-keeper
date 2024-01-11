<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://profiles.wordpress.org/pattihis/
 * @since      2.0.0
 *
 * @package    Link_Juice_Keeper
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// If we are on a multisite installation clean up all subsites.
if ( is_multisite() ) {

	foreach ( get_sites( array( 'fields' => 'ids' ) ) as $blogid ) {
		switch_to_blog( $blogid );
		link_juice_keeper_cleanup();
		restore_current_blog();
	}
} else {
	link_juice_keeper_cleanup();
}

/**
 * Delete all plugin options and custom table.
 *
 * @since 2.0.0
 */
function link_juice_keeper_cleanup() {

	// Plugin options.
	$options = array(
		'ljk_main_settings',
		'ljk_activated_time',
		'ljk_db_version',
		'ljk_version_no',
	);

	// Loop through each option.
	foreach ( $options as $option ) {
		delete_option( $option );
	}

	global $wpdb;

	// phpcs:disable -- This is a custom table.
	// Drop our custom table.
	$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'link_juice_keeper' );
}
