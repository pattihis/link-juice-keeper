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

$plugin_admin = new Link_Juice_Keeper_Admin('link-juice-keeper', LINK_JUICE_KEEPER_VERSION );
$statuses = $plugin_admin->linkJuiceKeeper_statuses();

$hide = ' style="display: none;"';
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<h1><?php _e( 'Link Juice Keeper', 'link-juice-keeper' ); ?></h1>
<h4><?php _e( 'Resolve 404 errors with automatic redirect', 'link-juice-keeper' ); ?></h4>
<div class="ljk_main_wrap">
    <div class="ljk_main_left">
        <form method="post" action="options.php">
            <?php settings_fields( 'ljk_main_settings' ); ?>
            <?php $options = get_option( 'ljk_main_settings' ); ?>
            <table class="form-table">
                <tbody>
                    <?php if ( !empty( $statuses ) ) : ?>
                        <tr>
                            <th><?php _e( 'Redirect type', 'link-juice-keeper' ); ?></th>
                            <td>
                                <select name='ljk_main_settings[redirect_type]'>
                                    <?php foreach ( $statuses as $status => $label ) : ?>
                                        <option value='<?php echo $status; ?>' <?php selected( $options['redirect_type'], $status ); ?>><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <th><?php _e( 'Redirect target', 'link-juice-keeper' ); ?></th>
                        <td>
                            <select name='ljk_main_settings[redirect_to]' id='ljk_redirect_to'>
                                <option value='home' <?php selected( $options['redirect_to'], 'home' ); ?>><?php _e( 'Home Page', 'link-juice-keeper' ); ?></option>
                                <option value='page' <?php selected( $options['redirect_to'], 'page' ); ?>><?php _e( 'Existing Page', 'link-juice-keeper' ); ?></option>
                                <option value='post' <?php selected( $options['redirect_to'], 'post' ); ?>><?php _e( 'Existing Post', 'link-juice-keeper' ); ?></option>
                                <option value='link' <?php selected( $options['redirect_to'], 'link' ); ?>><?php _e( 'Custom URL', 'link-juice-keeper' ); ?></option>
                                <option value='0' <?php selected( $options['redirect_to'], 0 ); ?>><?php _e( 'No Redirect', 'link-juice-keeper' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr id="custom_page"<?php echo ( $options['redirect_to'] !== 'page' ) ? $hide : ''; ?>>
                        <th><?php _e( 'Select a Page', 'link-juice-keeper' ); ?></th>
                        <td>
                            <?php wp_dropdown_pages( array( 'name' => 'ljk_main_settings[redirect_page]', 'selected' => $options['redirect_page'] ) ); ?>
                            <p><?php _e( 'Select a <b>Page</b> from this list to redirect all 404 errors to', 'link-juice-keeper' ); ?></p>

                        </td>
                    </tr>
                    <tr id="custom_post"<?php echo ( $options['redirect_to'] !== 'post' ) ? $hide : ''; ?>>
                        <th><?php _e( 'Select a Post', 'link-juice-keeper' ); ?></th>
                        <td>
                            <?php $plugin_admin->wp_dropdown_posts( array( 'selected' => $options['redirect_post'], 'select_name' => 'ljk_main_settings[redirect_post]' ) ); ?>
                            <p><?php _e( 'Select a <b>Post</b> from this list to redirect all 404 errors to', 'link-juice-keeper' ); ?></p>

                        </td>
                    </tr>
                    <tr id="custom_url"<?php echo ( $options['redirect_to'] !== 'link' ) ? $hide : ''; ?>>
                        <th><?php _e( 'Custom URL', 'link-juice-keeper' ); ?></th>
                        <td>
                            <input type="url" placeholder="<?php echo home_url(); ?>" name="ljk_main_settings[redirect_link]" value="<?php echo $options['redirect_link']; ?>">
                            <p><?php _e( 'Enter any <b>custom link</b> to redirect all 404 errors to', 'link-juice-keeper' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Log 404 Errors', 'link-juice-keeper' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="ljk_main_settings[redirect_log]" value="1" <?php checked( $plugin_admin->linkJuiceKeeper_get_option( 'redirect_log' ), 1 ); ?> /><?php _e( 'Keep track of 404 errors', 'link-juice-keeper' ); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Enable notifications', 'link-juice-keeper' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="ljk_main_settings[email_notify]" value="1" <?php checked( $plugin_admin->linkJuiceKeeper_get_option( 'email_notify' ), 1 ); ?> />
                            <?php _e( 'Get notified by email on every 404 error', 'link-juice-keeper' ); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Send notifications to', 'link-juice-keeper' ); ?></th>
                        <td>
                            <?php $notify_address = ( isset( $options['notify_to'] ) ) ? $options['notify_to'] : get_option( 'admin_email' ); ?>
                            <input type="email" placeholder="<?php echo get_option( 'admin_email' ); ?>" name="ljk_main_settings[notify_to]" value="<?php echo $notify_address; ?>">
                            <p><?php _e( 'Set the recipient email address for error log notifications', 'link-juice-keeper' ); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button( __( 'Save options', 'link-juice-keeper' ) ); ?>
        </form><!-- /.form -->
    </div>
    <div class="ljk_main_right">
        <h3><?php _e( 'Usage', 'link-juice-keeper' ); ?></h3>
        <hr>
        <h4><?php _e( 'Select a redirect type', 'link-juice-keeper' ); ?></h4>
        <p>
            <?php _e( 'The 301 redirect is the best method in most cases and recommended for your SEO', 'link-juice-keeper' ); ?>.<br><a target="_blank" href="https://moz.com/learn/seo/redirection"><strong><?php _e( 'Learn more', 'link-juice-keeper' ); ?></strong></a> <?php _e( 'about these redirect types', 'link-juice-keeper' ); ?>
        </p>
        <h4><?php _e( 'Select a redirect target', 'link-juice-keeper' ); ?></h4>
        <p>
            1. <strong><?php _e( 'Home Page', 'link-juice-keeper' ); ?>:</strong> <?php _e( 'Redirect any 404 not-found request to your homepage', 'link-juice-keeper' ); ?>.<br>
            2. <strong><?php _e( 'Existing Page', 'link-juice-keeper' ); ?>:</strong> <?php _e( 'Select any of your existing WordPress Pages as a 404 page', 'link-juice-keeper' ); ?>.<br>
            3. <strong><?php _e( 'Existing Post', 'link-juice-keeper' ); ?>:</strong> <?php _e( 'Select any of your existing WordPress Posts as a 404 page', 'link-juice-keeper' ); ?>.<br>
            4. <strong><?php _e( 'Custom URL', 'link-juice-keeper' ); ?>:</strong> <?php _e( 'Redirect 404 requests to any URL of your choice', 'link-juice-keeper' ); ?>.<br>
            5. <strong><?php _e( 'No Redirect', 'link-juice-keeper' ); ?>:</strong> <?php _e( 'Use this option to disable redirects on 404 errors', 'link-juice-keeper' ); ?>.
        </p>
    </div>
</div>
<p>
    <?php _e( 'This is a free plugin so if you find it useful then please', 'link-juice-keeper' ); ?> <a target="_blank" href="https://wordpress.org/support/plugin/link-juice-keeper/reviews/?rate=5#new-post" title="Rate the plugin"><?php _e( 'rate the plugin', 'link-juice-keeper' ); ?> ★★★★★</a> <?php _e( 'to support us. Thank you!', 'link-juice-keeper' ); ?>
</p>
