<?php
/**
 * Link Juice Keeper
 *
 * @package           Link_Juice_Keeper
 * @author            George Pattichis
 * @copyright         2021 George Pattichis
 * @license           GPL-2.0-or-later
 * @link              https://profiles.wordpress.org/pattihis/
 * @since             2.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Link Juice Keeper
 * Plugin URI:        https://wordpress.org/plugins/link-juice-keeper/
 * Description:       Improve your SEO and keep your link juice by automatically redirecting all 404 errors to any page/post/url. User friendly options and log feature.
 * Version:           2.1.1
 * Requires at least: 5.3.0
 * Tested up to:      6.4.2
 * Requires PHP:      7.2
 * Author:            George Pattichis
 * Author URI:        https://profiles.wordpress.org/pattihis/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       link-juice-keeper
 * Domain Path:       /languages
 */

/*
	Copyright 2009,2011  Daniel Frużyński (daniel@poradnik-webmastera.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
	Copyright 2021  George Pattichis (gpattihis@gmail.com)

	"Link Juice Keeper" is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	any later version.

	"Link Juice Keeper" is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	"along with Link Juice Keeper". If not, see http://www.gnu.org/licenses/gpl-2.0.txt.
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'LINK_JUICE_KEEPER_VERSION', '2.1.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-link-juice-keeper-activator.php
 *
 * @param bool $network_wide Whether to activate network-wide.
 */
function activate_link_juice_keeper( $network_wide ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-link-juice-keeper-activator.php';

	if ( is_multisite() && $network_wide ) {
		foreach ( get_sites( array( 'fields' => 'ids' ) ) as $blog_id ) {
			switch_to_blog( $blog_id );
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
