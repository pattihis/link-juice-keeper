<?php
/**
 * Fired during plugin activation
 *
 * @link       https://profiles.wordpress.org/pattihis/
 * @since      2.0.0
 *
 * @package    Link_Juice_Keeper
 * @subpackage Link_Juice_Keeper/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 * @package    Link_Juice_Keeper
 * @subpackage Link_Juice_Keeper/includes
 * @author     George Pattihis <gpattihis@gmail.com>
 */
class Link_Juice_Keeper_Activator {

	/**
	 *
	 * We register default options to WordPress if they do not exist.
	 *
	 * @since    2.0.0
	 */
	public static function activate() {

		// Default settings for our plugin.
		$options = array(
			'redirect_type' => '301',
			'redirect_link' => home_url(),
			'redirect_log'  => 1,
			'redirect_to'   => 'link',
			'redirect_page' => '',
			'redirect_post' => '',
			'notify'        => 0,
			'notify_to'     => get_option( 'admin_email' ),
		);

		// Get existing options if exists.
		$existing = get_option( 'ljk_main_settings' );
		// Check if valid settings exist.
		if ( $existing && is_array( $existing ) ) {
			foreach ( $options as $key => $value ) {
				if ( array_key_exists( $key, $existing ) ) {
					$options[ $key ] = $existing[ $key ];
				}
			}
		}

		// Update/create our settings.
		update_option( 'ljk_main_settings', $options );

		// Manage error log table.
		self::create_db();
	}

	/**
	 *
	 * Create or update database table for error logs
	 *
	 * @global object $wpdb WordPress database helper object.
	 * @uses   dbDelta() For safe db upgrades.
	 *
	 * @return void
	 */
	private static function create_db() {

		// Get db version number.
		$db = get_option( 'ljk_db_version' );

		// If table is up to date, do nothing.
		if ( defined( LINK_JUICE_KEEPER_VERSION ) && LINK_JUICE_KEEPER_VERSION === $db ) {
			return;
		}

		global $wpdb;

		// Our custom table name.
		$table = $wpdb->prefix . 'link_juice_keeper';

		// Define the table schema query.
		$query = "CREATE TABLE $table (
            id BIGINT NOT NULL AUTO_INCREMENT,
            date DATETIME NOT NULL,
            url VARCHAR(512) NOT NULL,
            ref VARCHAR(512) NOT NULL default '',
            ip VARCHAR(40) NOT NULL default '',
            ua VARCHAR(512) NOT NULL default '',
			status BIGINT NOT NULL default 1,
            PRIMARY KEY  (id)
        );";

		// Handle DB upgrades in proper WordPress way.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Update or create table in database.
		dbDelta( $query );

		// Update the db version number.
		update_option( 'ljk_db_version', LINK_JUICE_KEEPER_VERSION );
	}
}
