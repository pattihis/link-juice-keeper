<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/pattihis/
 * @since      2.0.0
 *
 * @package    Link_Juice_Keeper
 * @subpackage Link_Juice_Keeper/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Link_Juice_Keeper
 * @subpackage Link_Juice_Keeper/public
 * @author     George Pattihis <gpattihis@gmail.com>
 */
class Link_Juice_Keeper_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Execute 404 actions.
	 *
	 * Perform required actions when 404 is encountered.
	 * Log error details, Alert via email, Redirect.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function handle_404() {

		if ( ! is_404() || is_admin() ) {
			return;
		}

		$plugin_admin = new Link_Juice_Keeper_Admin( 'link-juice-keeper', LINK_JUICE_KEEPER_VERSION );

		// Get redirect target.
		$to = $plugin_admin->link_juice_keeper_get_option( 'redirect_to' );
		if ( 'home' === $to ) {
			$target = get_home_url();
		} elseif ( 'page' === $to ) {
			$target = get_permalink( $plugin_admin->link_juice_keeper_get_option( 'redirect_page' ) );
		} elseif ( 'post' === $to ) {
			$target = get_permalink( $plugin_admin->link_juice_keeper_get_option( 'redirect_post' ) );
		} elseif ( 'link' === $to ) {
			$target = $plugin_admin->link_juice_keeper_get_option( 'redirect_link' );
		}

		if ( '0' === $to || false === $to ) {
			return;
		} else {

			// Get redirect type.
			$options = get_option( 'ljk_main_settings' );
			$type    = $options['redirect_type'];

			// Get incident details.
			$this->get_data();

			// Log error details to database.
			$log = array_key_exists( 'redirect_log', $options ) ? (bool) $options['redirect_log'] : false;
			$this->log_error( $log );

			// Send email notification.
			$this->email_alert( (bool) $options['email_notify'] );

			// Redirect the user.
			$this->redirect( $type, $target );
		}
	}

	/**
	 * Collect data about the incident and save in array
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function get_data() {
		global $ljk_track_data;

		// Set visitor's IP address.
		$ips = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
		foreach ( $ips as $ip ) {
			if ( isset( $_SERVER[ $ip ] ) ) {
				$ip = esc_attr($_SERVER[$ip]); //phpcs:ignore
			}
		}

		// Set visitor's user agent/browser.
		$ua = isset($_SERVER['HTTP_USER_AGENT']) ? esc_attr($_SERVER['HTTP_USER_AGENT']) : ''; //phpcs:ignore

		// Set visitor's referring link.
		$ref = isset($_SERVER['HTTP_REFERER']) ? esc_url($_SERVER['HTTP_REFERER']) : ''; //phpcs:ignore

		// Set visitor's referring link.
		$url = isset($_SERVER['REQUEST_URI']) ? untrailingslashit(esc_url($_SERVER['REQUEST_URI'])) : ''; //phpcs:ignore

		// Set current time.
		$time = current_time( 'mysql' );

		$ljk_track_data = array(
			'date'   => $time,
			'ip'     => $ip,
			'url'    => $url,
			'ref'    => $ref,
			'ua'     => $ua,
			'status' => 1,
		);
	}

	/**
	 * Log details of error to the database.
	 *
	 * @param bool $enabled Whether to log or not.
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function log_error( $enabled ) {

		if ( ! $enabled ) {
			return;
		}

		global $wpdb;
		global $ljk_track_data;
		$save_data = array();
		$table     = $wpdb->prefix . 'link_juice_keeper';

		if ( is_array( $ljk_track_data ) ) {
			$save_data = array_map( 'sanitize_text_field', $ljk_track_data );

			// Insert data to database.
			$wpdb->insert($table, $save_data); //phpcs:ignore
		}
	}

	/**
	 * Send email about the error.
	 *
	 * @param bool $enabled Whether to send or not.
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function email_alert( $enabled ) {

		if ( ! $enabled ) {
			return;
		}

		global $ljk_track_data;

		// Recipient.
		$options   = get_option( 'ljk_main_settings' );
		$notify_to = ( ! empty( $options['notify_to'] ) ) ? $options['notify_to'] : get_option( 'admin_email' );
		// Subject.
		$subject = __( 'A 404 Not Found error at ', 'link-juice-keeper' ) . get_bloginfo( 'name' );
		// Email Body.
		$body  = '<p>' . __( 'Notice: One more 404 error has just happened on your website at ', 'link-juice-keeper' ) . get_home_url() . '</p>';
		$body .= '<p>' . __( 'If you have enabled 404 redirection then visitor was sent to your selected page and you can ignore this message', 'link-juice-keeper' ) . '</p>';
		$body .= '<table>';
		$body .= '<tr><th align="left">' . __( '404 Not Found', 'link-juice-keeper' ) . ': </th><td align="left">' . $ljk_track_data['url'] . '</td></tr>';
		$body .= '<tr><th align="left">' . __( 'Visitor IP', 'link-juice-keeper' ) . ': </th><td align="left">' . $ljk_track_data['ip'] . '</td></tr>';
		$body .= '<tr><th align="left">' . __( 'Time', 'link-juice-keeper' ) . ': </th><td align="left">' . $ljk_track_data['date'] . '</td></tr>';
		$body .= '<tr><th align="left">' . __( 'Referrer', 'link-juice-keeper' ) . ': </th><td align="left">' . $ljk_track_data['ref'] . '</td></tr>';
		$body .= '</table>';
		$body .= '<p>' . esc_html__( 'Alert sent by the Link Juice Keeper plugin for WordPress.' ) . '</p>';
		$body .= '<p>' . esc_html__( 'You can Turn off these notifications in your admin dashboard.', 'link-juice-keeper' ) . '</p>';

		// Send email notifcation.
		wp_mail( $notify_to, $subject, $body, 'Content-Type: text/html; charset=UTF-8' );
	}


	/**
	 * Redirect 404 errors.
	 *
	 * @param int    $type   Type of redirect.
	 * @param string $target Target of redirect.
	 * @since  2.0.0
	 * @return void
	 */
	public function redirect( $type, $target ) {

		if ( empty( $target ) ) {
			return;
		} else {
			wp_redirect($target, $type); //phpcs:ignore
			exit;
		}
	}
}
