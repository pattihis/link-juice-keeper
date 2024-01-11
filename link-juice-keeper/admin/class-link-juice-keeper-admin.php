<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/pattihis/
 * @since      2.0.0
 *
 * @package    Link_Juice_Keeper
 * @subpackage Link_Juice_Keeper/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Link_Juice_Keeper
 * @subpackage Link_Juice_Keeper/admin
 * @author     George Pattihis <gpattihis@gmail.com>
 */
class Link_Juice_Keeper_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/link-juice-keeper-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/link-juice-keeper-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Register the admin menu
	 *
	 * @since    2.0.0
	 */
	public function link_juice_keeper_admin_menu() {

		add_menu_page(
			__( 'Link Juice Keeper', 'link-juice-keeper' ),
			__( 'Link Juice Keeper', 'link-juice-keeper' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'link_juice_keeper_admin_display' ),
			'dashicons-admin-links',
			25
		);

		add_submenu_page( $this->plugin_name, __( 'Link Juice Keeper Settings', 'link-juice-keeper' ), __( 'Settings', 'link-juice-keeper' ), 'manage_options', $this->plugin_name, array( $this, 'link_juice_keeper_admin_display' ), 0 );
		add_submenu_page( $this->plugin_name, __( '404 Logs', 'link-juice-keeper' ), __( '404 Logs', 'link-juice-keeper' ), 'manage_options', $this->plugin_name . '-404-logs', array( $this, 'link_juice_keeper_admin_display_logs' ), 1 );
	}

	/**
	 * Render the admin menu page content
	 *
	 * @since  2.0.0
	 */
	public function link_juice_keeper_admin_display() {
		include_once 'partials/link-juice-keeper-admin-display.php';
	}

	/**
	 * Render the logs page content
	 *
	 * @since  2.0.0
	 */
	public function link_juice_keeper_admin_display_logs() {
		include_once 'partials/link-juice-keeper-admin-display-logs.php';
	}

	/**
	 * Return the allowed status codes
	 *
	 * @since  2.0.0
	 * @return array Allowed HTTP status codes
	 */
	public function link_juice_keeper_statuses() {

		$statuses = array(
			301 => __( '301 Redirect (Permanent)', 'link-juice-keeper' ),
			302 => __( '302 Redirect (Found)', 'link-juice-keeper' ),
			307 => __( '307 Redirect (Temporary)', 'link-juice-keeper' ),
		);

		return (array) apply_filters( 'ljk_statuses', $statuses );
	}

	/**
	 * Get plugin settings value.
	 *
	 * @param mixed $option Option name.
	 * @param mixed $default Default value if not exist.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string|array
	 */
	public function link_juice_keeper_get_option( $option = false, $default = false ) {

		if ( ! $option ) {
			return $default;
		}

		// Get our plugin settings value.
		$settings = (array) get_option( 'ljk_main_settings', array() );

		// Return false, if not exist.
		if ( empty( $settings[ $option ] ) ) {
			return $default;
		}

		return $settings[ $option ];
	}

	/**
	 * Registering our settings options using WordPress settings API.
	 *
	 * @since  2.0.0
	 * @access public
	 * @uses   hooks  register_setting Hook to register options in db.
	 *
	 * @return void
	 */
	public function register_settings() {

		register_setting( 'ljk_main_settings', 'ljk_main_settings' );
	}

	/**
	 * Create dropdown HTML content of posts
	 *
	 * Supports all WP_Query arguments
	 *
	 * @see https://codex.wordpress.org/Class_Reference/WP_Query
	 *
	 * @since 2.0.0
	 *
	 * @param array|string $args Optional. Array or string of arguments to generate a drop-down of posts.
	 *
	 * @return string String of HTML content.
	 */
	public function link_juice_keeper_dropdown_posts( $args = '' ) {

		$defaults = array(
			'selected'              => false,
			'pagination'            => false,
			'posts_per_page'        => -1,
			'post_status'           => 'publish',
			'cache_results'         => true,
			'cache_post_meta_cache' => true,
			'echo'                  => 1,
			'select_name'           => 'post_id',
			'id'                    => '',
			'class'                 => '',
			'show'                  => 'post_title',
			'show_callback'         => null,
			'show_option_all'       => null,
			'show_option_none'      => null,
			'option_none_value'     => '',
			'multi'                 => false,
			'value_field'           => 'ID',
			'order'                 => 'ASC',
			'orderby'               => 'post_title',
		);

		$r = wp_parse_args( $args, $defaults );

		$posts  = get_posts( $r );
		$output = '';

		$show = $r['show'];

		if ( ! empty( $posts ) ) {

			$name = esc_attr( $r['select_name'] );

			if ( $r['multi'] && ! $r['id'] ) {
				$id = '';
			} else {
				$id = $r['id'] ? " id='" . esc_attr( $r['id'] ) . "'" : " id='$name'";
			}

			$output = "<select name='{$name}'{$id} class='" . esc_attr( $r['class'] ) . "'>\n";

			if ( $r['show_option_all'] ) {
				$output .= "\t<option value='0'>{$r['show_option_all']}</option>\n";
			}

			if ( $r['show_option_none'] ) {
				$_selected = selected( $r['show_option_none'], $r['selected'], false );
				$output   .= "\t<option value='" . esc_attr( $r['option_none_value'] ) . "'$_selected>{$r['show_option_none']}</option>\n";
			}

			foreach ( (array) $posts as $post ) {

				$value     = ! isset( $r['value_field'] ) || ! isset( $post->{$r['value_field']} ) ? $post->ID : $post->{$r['value_field']};
				$_selected = selected( $value, $r['selected'], false );

				// translators: %d: post ID.
				$display = ! empty( $post->$show ) ? $post->$show : sprintf( __( '#%d (no title)' ), $post->ID );

				if ( $r['show_callback'] ) {
					$display = call_user_func( $r['show_callback'], $display, $post->ID );
				}

				$output .= "\t<option value='{$value}'{$_selected}>" . esc_html( $display ) . "</option>\n";
			}

			$output .= '</select>';
		}

		/**
		 * Filter the HTML output of a list of pages as a drop down.
		 *
		 * @since 2.0.0
		 *
		 * @param string $output HTML output for drop down list of posts.
		 * @param array  $r      The parsed arguments array.
		 * @param array  $posts  List of WP_Post objects returned by `get_posts()`
		 */
		$html = apply_filters( 'link_juice_keeper_dropdown_posts', $output, $r, $posts );

		if ( $r['echo'] ) {
			echo $html; //phpcs:disable
		}

		return $html;
	}


	/**
	 * Output buffer function to avoid headers already sent issues.
	 *
	 * @link   https://tommcfarlin.com/wp_redirect-headers-already-sent/
	 * @since  2.1.4
	 * @access public
	 *
	 * @uses   ob_start() To load buffer.
	 *
	 * @return void
	 */
	public function add_buffer() {

		ob_start();
	}
}
