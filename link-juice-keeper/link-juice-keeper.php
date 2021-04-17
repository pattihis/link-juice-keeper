<?php

/**
 *
 * @link              https://profiles.wordpress.org/pattihis/
 * @since             2.0.0
 * @package           Link_Juice_Keeper
 *
 * @wordpress-plugin
 * Plugin Name:       Link Juice Keeper
 * Plugin URI:        https://wordpress.org/plugins/link-juice-keeper/
 * Description:       Improve your SEO and keep your link juice by automatically redirecting all 404 errors to any page/post/url. User friendly options and log feature.
 * Version:           2.0.0
 * Author:            George Pattihis
 * Author URI:        https://profiles.wordpress.org/pattihis/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       link-juice-keeper
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'LINK_JUICE_KEEPER_VERSION', '2.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-link-juice-keeper-activator.php
 */
function activate_link_juice_keeper($network_wide) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-link-juice-keeper-activator.php';
	
	if ( is_multisite() && $network_wide ) { 
		foreach (get_sites(['fields'=>'ids']) as $blog_id) {
			switch_to_blog($blog_id);
			Link_Juice_Keeper_Activator::activate();
			restore_current_blog();
		} 
	} else {
		Link_Juice_Keeper_Activator::activate();
	}
	
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-link-juice-keeper-deactivator.php
 */
function deactivate_link_juice_keeper() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-link-juice-keeper-deactivator.php';
	Link_Juice_Keeper_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_link_juice_keeper' );
register_deactivation_hook( __FILE__, 'deactivate_link_juice_keeper' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-link-juice-keeper.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_link_juice_keeper() {

	$plugin = new Link_Juice_Keeper();
	$plugin->run();

}
run_link_juice_keeper();
