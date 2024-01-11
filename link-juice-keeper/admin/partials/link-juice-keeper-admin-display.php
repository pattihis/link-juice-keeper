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

$plugin_admin = new Link_Juice_Keeper_Admin( 'link-juice-keeper', LINK_JUICE_KEEPER_VERSION );
$statuses     = $plugin_admin->link_juice_keeper_statuses();

$hide = ' style="display: none;"';
?>

<div class="ljk_main_header">
	<h1><span class="dashicons dashicons-admin-links"></span>&nbsp;<?php esc_html_e( 'Link Juice Keeper', 'link-juice-keeper' ); ?></h1>
</div>
<h4><?php esc_html_e( 'Resolve 404 errors with automatic redirect', 'link-juice-keeper' ); ?></h4>
<div class="ljk_main_wrap">
	<div class="ljk_main_left">
		<form method="post" action="options.php">
			<?php settings_fields( 'ljk_main_settings' ); ?>
			<?php $options = get_option( 'ljk_main_settings' ); ?>
			<table class="form-table">
				<tbody>
					<?php if ( ! empty( $statuses ) ) : ?>
						<tr>
							<th><?php esc_html_e( 'Redirect type', 'link-juice-keeper' ); ?></th>
							<td>
								<select name='ljk_main_settings[redirect_type]' style="width: 100%;">
									<?php foreach ( $statuses as $re_status => $label ) : ?>
										<option value='<?php echo esc_attr( $re_status ); ?>' <?php selected( $options['redirect_type'], $re_status ); ?>><?php echo esc_attr( $label ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<th><?php esc_html_e( 'Redirect target', 'link-juice-keeper' ); ?></th>
						<td>
							<select name='ljk_main_settings[redirect_to]' id='ljk_redirect_to' style="width: 100%;">
								<option value='home' <?php selected( $options['redirect_to'], 'home' ); ?>><?php esc_html_e( 'Home Page', 'link-juice-keeper' ); ?></option>
								<option value='page' <?php selected( $options['redirect_to'], 'page' ); ?>><?php esc_html_e( 'Existing Page', 'link-juice-keeper' ); ?></option>
								<option value='post' <?php selected( $options['redirect_to'], 'post' ); ?>><?php esc_html_e( 'Existing Post', 'link-juice-keeper' ); ?></option>
								<option value='link' <?php selected( $options['redirect_to'], 'link' ); ?>><?php esc_html_e( 'Custom URL', 'link-juice-keeper' ); ?></option>
								<option value='0' <?php selected( $options['redirect_to'], 0 ); ?>><?php esc_html_e( 'No Redirect', 'link-juice-keeper' ); ?></option>
							</select>
						</td>
					</tr>
					<tr id="custom_page" <?php echo ( 'page' !== $options['redirect_to'] ) ? esc_attr( $hide ) : ''; ?>>
						<th><?php esc_html_e( 'Select a Page', 'link-juice-keeper' ); ?></th>
						<td>
							<?php
							wp_dropdown_pages(
								array(
									'name'     => 'ljk_main_settings[redirect_page]',
									'selected' => array_key_exists('redirect_page', $options) ? $options['redirect_page'] : 0, //phpcs:disable
								)
							);
							?>
							<p><?php esc_html_e('Select a Page from this list to redirect all 404 errors to', 'link-juice-keeper'); ?></p>

						</td>
					</tr>
					<tr id="custom_post" <?php echo ('post' !== $options['redirect_to']) ? esc_attr($hide) : ''; ?>>
						<th><?php esc_html_e('Select a Post', 'link-juice-keeper'); ?></th>
						<td>
							<?php
							$plugin_admin->link_juice_keeper_dropdown_posts(
								array(
									'select_name' => 'ljk_main_settings[redirect_post]',
									'selected'    => array_key_exists('redirect_post', $options) ? $options['redirect_post'] : 0,
								)
							);
							?>
							<p><?php esc_html_e('Select a Post from this list to redirect all 404 errors to', 'link-juice-keeper'); ?></p>

						</td>
					</tr>
					<tr id="custom_url" <?php echo ('link' !== $options['redirect_to']) ? esc_attr($hide) : ''; ?>>
						<th><?php esc_html_e('Custom URL', 'link-juice-keeper'); ?></th>
						<td>
							<input type="url" placeholder="<?php echo esc_attr(home_url()); ?>" name="ljk_main_settings[redirect_link]" value="<?php echo esc_url_raw(sanitize_text_field($options['redirect_link'])); ?>" style="width: 100%;">
							<p><?php esc_html_e('Redirect all 404 errors to the above.', 'link-juice-keeper'); ?></p>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e('Log 404 Errors', 'link-juice-keeper'); ?></th>
						<td>
							<label><input type="checkbox" name="ljk_main_settings[redirect_log]" value="1" <?php checked($plugin_admin->link_juice_keeper_get_option('redirect_log'), 1); ?> /><?php esc_html_e('Keep track of 404 errors', 'link-juice-keeper'); ?></label>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e('Enable notifications', 'link-juice-keeper'); ?></th>
						<td>
							<label><input type="checkbox" name="ljk_main_settings[email_notify]" value="1" <?php checked($plugin_admin->link_juice_keeper_get_option('email_notify'), 1); ?> />
								<?php esc_html_e('Get notified by email on every 404 error', 'link-juice-keeper'); ?></label>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e('Send notifications to', 'link-juice-keeper'); ?></th>
						<td>
							<?php $notify_address = (isset($options['notify_to'])) ? $options['notify_to'] : get_option('admin_email'); ?>
							<input type="email" placeholder="<?php echo esc_attr(get_option('admin_email')); ?>" name="ljk_main_settings[notify_to]" value="<?php echo esc_html(sanitize_text_field($notify_address)); ?>" style="width:100%;">
							<p><?php esc_html_e('The recipient for email notifications.', 'link-juice-keeper'); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button(__('Save options', 'link-juice-keeper')); ?>
		</form><!-- /.form -->
	</div>
	<div class="ljk_main_right">
		<h3><?php esc_html_e('Usage', 'link-juice-keeper'); ?></h3>
		<hr>
		<h4><?php esc_html_e('Select a redirect type', 'link-juice-keeper'); ?></h4>
		<p>
			<?php esc_html_e('The 301 redirect is the best method in most cases and recommended for your SEO', 'link-juice-keeper'); ?>.<br><a target="_blank" href="https://moz.com/learn/seo/redirection"><strong><?php esc_html_e('Learn more', 'link-juice-keeper'); ?></strong></a> <?php esc_html_e('about these redirect types', 'link-juice-keeper'); ?>
		</p>
		<h4><?php esc_html_e('Select a redirect target', 'link-juice-keeper'); ?></h4>
		<ol>
			<li><strong><?php esc_html_e('Home Page', 'link-juice-keeper'); ?>:</strong> <?php esc_html_e('Redirect any 404 not-found request to your homepage', 'link-juice-keeper'); ?>.</li>
			<li><strong><?php esc_html_e('Existing Page', 'link-juice-keeper'); ?>:</strong> <?php esc_html_e('Select any of your existing WordPress Pages as a 404 page', 'link-juice-keeper'); ?>.</li>
			<li><strong><?php esc_html_e('Existing Post', 'link-juice-keeper'); ?>:</strong> <?php esc_html_e('Select any of your existing WordPress Posts as a 404 page', 'link-juice-keeper'); ?>.</li>
			<li><strong><?php esc_html_e('Custom URL', 'link-juice-keeper'); ?>:</strong> <?php esc_html_e('Redirect 404 requests to any URL of your choice', 'link-juice-keeper'); ?>.</li>
			<li><strong><?php esc_html_e('No Redirect', 'link-juice-keeper'); ?>:</strong> <?php esc_html_e('Use this option to disable redirects on 404 errors', 'link-juice-keeper'); ?>.
			</ol>
	</div>
</div>
<p>
	<?php esc_html_e('If you find this free plugin useful then please', 'link-juice-keeper'); ?> <a target="_blank" href="https://wordpress.org/support/plugin/link-juice-keeper/reviews/?rate=5#new-post" title="Rate the plugin"><?php esc_html_e('rate the plugin', 'link-juice-keeper'); ?> ★★★★★</a> <?php esc_html_e('to support us. Thank you!', 'link-juice-keeper'); ?>
</p>
