<?php
/**
 * Provide an admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://profiles.wordpress.org/pattihis/
 * @since      2.0.0
 *
 * @package    Link_Juice_Keeper
 * @subpackage Link_Juice_Keeper/admin/partials
 */

global $wpdb;

$table = new Link_Juice_Keeper_Logs();

echo '<h1><span class="dashicons dashicons-admin-links"></span>&nbsp;' . esc_html__( '404 Logs', 'link-juice-keeper' ) . '</h1>';

echo '<h4>' . esc_html__( 'All 404 errors recorded in the database', 'link-juice-keeper' ) . '</h4>';

$table->prepare_items();

/**
 * Action hook to add something above listing page.
 *
 * Use this action hook to add custom filters and search
 * boxes to the listing table top section.
 *
 * @param object $this Listing page class object.
 *
 * @since 2.0.0
 */
do_action( 'ljk_log_list_above_form', $table );

echo '<form method="get">';
echo '<input type="hidden" name="page" value="link-juice-keeper-404-logs" />';
$table->display();
echo '</form>';

/**
 * Action hook to add something below the listing page.
 *
 * Use this action hook to add custom filters and search
 * boxes to the listing table bottom section.
 *
 * @param object $this Listing page class object.
 *
 * @since 2.0.0
 */
do_action( 'ljk_log_list_below_form', $table );
