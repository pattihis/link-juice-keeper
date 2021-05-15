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

?>

<h1>404 Logs</h1>
    <div class="wrap">
        <h4><?php _e( 'All 404 errors recorded in the database', 'link-juice-keeper' ); ?></h4>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <?php $table->prepare_items();
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
                        ?>
                        <form method="get">
                            <input type="hidden" name="page" value="link-juice-keeper-404-logs"/>
                            <?php $table->display(); ?>
                        </form>
                        <?php
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
                        ?>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>
<?php
