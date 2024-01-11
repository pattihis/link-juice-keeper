<?php
/**
 * The class responsible for displaying the logs in a WP table
 *
 * @link       https://profiles.wordpress.org/pattihis/
 * @since      2.0.0
 *
 * @package    Link_Juice_Keeper
 * @subpackage Link_Juice_Keeper/logs
 * @author     George Pattihis <gpattihis@gmail.com>
 */

// Load base class if it doesn't exist.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}


/**
 * The class responsible for displaying the logs in a WP table in the admin area.
 */
class Link_Juice_Keeper_Logs extends WP_List_Table {

	/**
	 * Group by column name.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    string
	 */
	private $group_by = '';

	/**
	 * Initialize the class and set properties.
	 *
	 * @since  2.0.0
	 * @access public
	 */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => __( '404 Error Log', 'link-juice-keeper' ),
				'plural'   => __( '404 Error Logs', 'link-juice-keeper' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 * Prepare listing table using WP_List_Table class.
	 *
	 * This function extends the listing table class and uses our data
	 * to list in the table.Here we set pagination, columns, sorting etc.
	 *
	 * $this->items - Push our custom log data to the listing table.
	 * Registering filter - "ljk_logs_list_per_page".
	 *
	 * @since  2.0.0
	 * @access public
	 */
	public function prepare_items() {

		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
		);

		// Execute bulk actions.
		$actions = $this->process_actions();

		// Redirect after actions, or after security check.
		$this->safe_redirect( $actions );

		// Set group by column.
		$this->set_groupby();

		/**
		 * Filter to alter no. of items per page.
		 *
		 * Change no. of items listed on a page. This value can be changed from
		 * error listing page screen options.
		 *
		 * @since 2.0.0
		 */
		$per_page = apply_filters( 'ljk_logs_list_per_page', $this->get_items_per_page( 'logs_per_page', 100 ) );

		// Current page number.
		$current_page = $this->get_pagenum();

		// Total error logs.
		$total_items = $this->total_logs();

		// Set pagination.
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);

		$data = $this->get_error_logs();

		usort( $data, array( &$this, 'sort_data' ) );

		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->items = $data;
	}

	/**
	 * Get all error logs data from our custom database table.
	 * Registering filter - "ljk_logs_list_result".
	 *
	 * @global object $wpdb WP DB object
	 * @since  2.0.0
	 * @access public
	 *
	 * @return array
	 */
	private function get_error_logs() {

		global $wpdb;
		$table = $wpdb->prefix . 'link_juice_keeper';

		// Sort by column.
		$orderby = $this->get_order_by();

		// Sort order.
		$order = $this->get_order();

		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $table . ' WHERE status != 0  ORDER BY %s %s', array($orderby, $order)), 'ARRAY_A'); //phpcs:ignore

		/**
		 * Filter to alter the error logs listing data result.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'ljk_logs_list_result', $result );
	}

	/**
	 * Get sort by column name.
	 *
	 * This is used to filter the sorting parameters in order
	 * to prevent SQL injection atacks. We will accept only our
	 * required values. Else we will assign a default value.
	 * Registering filter - "ljk_log_list_orderby".
	 *
	 * @since  2.0.3
	 * @access public
	 * @uses   esc_sql() To escape string for SQL.
	 *
	 * @return string Filtered column name.
	 */
	private function get_order_by() {

		/**
		 * Filter to alter the log listing orderby param.
		 *
		 * Only a valid column name will be accepted.
		 *
		 * @since 2.0.0
		 */
		$orderby = apply_filters( 'ljk_log_list_orderby', $this->link_juice_keeper_from_request( 'orderby', 'date' ) );

		/**
		 * Filter to alter the allowed order by values.
		 *
		 * @param array array of allowed column names.
		 *
		 * @since 2.0.0
		 */
		$allowed_columns = apply_filters( 'ljk_log_list_orderby_allowed', array( 'date', 'url', 'ref', 'ip' ) );

		// Make sure only valid columns are considered.
		$allowed_columns = array_intersect( $allowed_columns, array_keys( $this->linkJuiceKeeper_log_columns() ) );

		// Check if given column is allowed.
		if ( in_array( $orderby, $allowed_columns, true ) ) {
			return esc_sql( $orderby );
		}

		return 'date';
	}

	/**
	 * Filter the sorting parameters.
	 *
	 * This is used to filter the sorting parameters in order
	 * to prevent SQL injection attacks. We will accept only our
	 * required values. Else we will assign a default value.
	 * Registering filter - "ljk_log_list_order".
	 *
	 * @since  2.0.3
	 * @access private
	 *
	 * @return string Filtered column name.
	 */
	private function get_order() {

		// Get order column name from request.
		$order = $this->link_juice_keeper_from_request( 'order', 'DESC' ) === 'asc' ? 'ASC' : 'DESC';

		/**
		 * Filter to alter the log listing order param.
		 *
		 * Only ASC and DESC will be accepted.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'ljk_log_list_order', $order );
	}

	/**
	 * Set groupby value for grouping results.
	 *
	 * Groupby filter to avoid duplicate values in error log
	 * listing table. If a groupby column is set, it will show
	 * the count along with the logs.
	 * Registering filter - "ljk_log_list_groupby_allowed".
	 * Registering filter - "ljk_log_list_groupby".
	 *
	 * @since  2.0.0
	 * @access private
	 */
	private function set_groupby() {

		/**
		 * Filter to alter the allowed group by values.
		 *
		 * Only these columns will be allowed. It is a security
		 * measure too.
		 *
		 * @param array array of allowed column names.
		 *
		 * @since 2.0.0
		 */
		$allowed_values = apply_filters( 'ljk_log_list_groupby_allowed', array( 'url', 'ref', 'ip', 'ua' ) );

		// Make sure only valid columns are considered.
		$allowed_values = array_intersect( $allowed_values, array_keys( $this->linkJuiceKeeper_log_columns() ) );

		// Get group by value from request.
		$group_by = $this->link_juice_keeper_from_request( 'group_by_top', '' );

		// Verify if the group by value is allowed.
		if ( ! in_array( $group_by, $allowed_values, true ) ) {
			return;
		}

		/**
		 * Filter to alter the log listing groupby param.
		 *
		 * Only allowed column names are accepted.
		 *
		 * @since 2.0.0
		 */
		$this->group_by = apply_filters( 'ljk_log_list_groupby', $group_by );
	}

	/**
	 * Get the count of total logs in table.
	 *
	 * Since we are using a custom table for data in
	 * listing, we need to get count of total items for proper pagination.
	 * Registering filter - "ljk_log_list_count".
	 *
	 * @global object $wpdb WP DB object
	 * @since  2.0.3
	 * @access private
	 *
	 * @return int Total count.
	 */
	private function total_logs() {

		global $wpdb;
		$table = $wpdb->prefix . 'link_juice_keeper';

		if ( empty( $this->group_by ) ) {
			$total = $wpdb->get_var('SELECT COUNT(id) FROM ' . $table); //phpcs:ignore
		} else {
			$total = $total = $wpdb->get_var('SELECT COUNT(DISTINCT ' . $this->group_by . ') FROM ' . $table); //phpcs:ignore
		}

		/**
		 * Filter to alter total logs count.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'ljk_log_list_count', $total );
	}

	/**
	 * Listing table column titles.
	 *
	 * Custom column titles to be displayed in listing table.
	 * Registering filter - "ljk_log_list_column_names".
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return array $columns Array of column titles.
	 */
	public function get_columns() {

		$columns = array(
			'cb'   => '<input type="checkbox" style="width: 5%;" />',
			'date' => __( 'Date', 'link-juice-keeper' ),
			'url'  => __( '404 Path', 'link-juice-keeper' ),
			'ref'  => __( 'Referrer', 'link-juice-keeper' ),
			'ip'   => __( 'User IP', 'link-juice-keeper' ),
			'ua'   => __( 'User Agent', 'link-juice-keeper' ),
		);

		/**
		 * Filter hook to change column titles.
		 *
		 * If you are adding custom columns, remember to add
		 * those to "ljk_log_list_column_default" filter too.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'ljk_log_list_column_names', $columns );
	}

	/**
	 * Make columns sortable.
	 *
	 * To make our custom columns in list table sortable.
	 * Do not enable sorting for ua columns.
	 * Registering filter - "ljk_log_list_sortable_columns".
	 *
	 * @since  2.0.0
	 * @access protected
	 *
	 * @return array Array of columns to enable sorting.
	 */
	protected function get_sortable_columns() {

		$columns = array(
			'date' => array( 'date', true ),
			'url'  => array( 'url', false ),
			'ref'  => array( 'ref', false ),
			'ip'   => array( 'ip', false ),
		);

		/**
		 * Filter hook to change column titles.
		 *
		 * @note DO NOT add extra columns.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'ljk_log_list_sortable_columns', $columns );
	}

	/**
	 * Message to be displayed when there are no items.
	 *
	 * If there are no errors logged yet, show custom error message
	 * instead of default one.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function no_items() {

		/**
		 * Filter hook to change no items message.
		 *
		 * @since 2.0.0
		 */
		esc_html_e( 'It seems there are no 404 errors logged yet.', 'link-juice-keeper' );
	}

	/**
	 * Default columns in list table.
	 *
	 * To show columns in error log list table. If there is nothing
	 * for switch, printing the whole array.
	 * Registering filter - "ljk_log_list_column_default".
	 *
	 * @param array  $item Column data.
	 * @param string $column_name Column name.
	 *
	 * @since  2.0.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function column_default( $item, $column_name ) {

		$columns = array_keys( $this->linkJuiceKeeper_log_columns() );

		/**
		 * Filter hook to change column names.
		 *
		 * @note DO NOT add extra columns.
		 *
		 * @since 2.0.0
		 */
		$columns = apply_filters( 'ljk_log_list_column_default', $columns );

		// If current column is allowed.
		if ( in_array( $column_name, $columns, true ) ) {
			return $item[ $column_name ];
		}

		// Show the whole array for troubleshooting purposes.
		return print_r($item, true); //phpcs:ignore
	}

	/**
	 * To output checkbox for bulk actions.
	 *
	 * This function is used to add new checkbox for all entries in
	 * the listing table. We use this checkbox to perform bulk actions.
	 *
	 * @param array $item Column data.
	 *
	 * @since  2.1.0
	 * @access public
	 *
	 * @return string
	 */
	protected function column_cb( $item ) {

		return sprintf( '<input type="checkbox" name="bulk-delete[]" value="%s"/>', $item['id'] );
	}

	/**
	 * Date column content.
	 *
	 * This function is used to modify the column data for date in listing table.
	 * We can change styles, texts etc. using this function.
	 * Registering filter - "ljk_log_list_date_column".
	 *
	 * @param array $item Column data.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_date( $item ) {

		$delete_nonce = wp_create_nonce( 'bulk-' . $this->_args['plural'] );

		$title = mysql2date( 'j M Y, g:i a', $item['date'] );

		$confirm = __( 'Are you sure you want to delete this item?', 'link-juice-keeper' );

		$actions = array( 'delete' => sprintf( '<a href="?page=link-juice-keeper-404-logs&action=%s&bulk-delete=%s&_wpnonce=%s" onclick="return confirm(\'%s\');">' . __( 'Delete', 'link-juice-keeper' ) . '</a>', 'delete', absint( $item['id'] ), $delete_nonce, $confirm ) );

		/**
		 * Filter to change date column html content.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'ljk_log_list_date_column', $title . $this->row_actions( $actions ) );
	}

	/**
	 * URL column content.
	 *
	 * This function is used to modify the column data for url in listing table.
	 * We can change styles, texts etc. using this function.
	 * Registering filter - "ljk_log_list_url_column".
	 *
	 * @param array $item Column data.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string URL column html content
	 */
	public function column_url( $item ) {

		// Get default text if empty value.
		$url = $this->get_empty_content( $item['url'] );
		if ( ! $url ) {
			$url = esc_url( $item['url'] );
		}

		/**
		 * Filter to change url column content
		 *
		 * Remember this filter value is a partial url field
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'ljk_log_list_url_column', $this->get_group_content( $url, 'url', $item ) );
	}

	/**
	 * Referrer column content.
	 *
	 * This function is used to modify the column data for ref in listing table.
	 * We can change styles, texts etc. using this function.
	 * Registering filter - "ljk_log_list_ref_column".
	 *
	 * @param array $item Column data.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string Ref column html content.
	 */
	public function column_ref( $item ) {

		// Get default text if empty value.
		$ref = $this->get_empty_content( $item['ref'] );
		if ( ! $ref ) {
			$ref = '<a href="' . esc_url( $item['ref'] ) . '" target="_blank">' . esc_url( $item['ref'] ) . '</a>';
		}

		/**
		 * Filter to change referer url column content
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'ljk_log_list_ref_column', $this->get_group_content( $ref, 'ref', $item ) );
	}

	/**
	 * User agent column content.
	 *
	 * This function is used to modify the column data for user agent in listing table.
	 * We can change styles, texts etc. using this function.
	 * Registering filter - "ljk_log_list_ua_column".
	 *
	 * @param array $item Column data.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string User Agent column html content
	 */
	public function column_ua( $item ) {

		// Sanitize text content.
		$ua = sanitize_text_field( $item['ua'] );

		/**
		 * Filter to change user agent column content.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'ljk_log_list_ua_column', $this->get_group_content( $ua, 'ua', $item ) );
	}

	/**
	 * IP column content.
	 *
	 * This function is used to modify the column data for ip in listing table.
	 * We can change styles, texts etc. using this function.
	 * Registering filter - "ljk_log_list_ip_column".
	 *
	 * @param array $item Column data.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string IP column html content.
	 */
	public function column_ip( $item ) {

		// Get default text if empty value.
		$ip = $this->get_empty_content( $item['ip'] );
		if ( ! $ip ) {
			$ip = sanitize_text_field( $item['ip'] );
		}

		/**
		 * Filter to change IP column content.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'ljk_log_list_ip_column', $this->get_group_content( $ip, 'ip', $item ) );
	}

	/**
	 * Check if current column is grouped and add count number
	 *
	 * @param string $content Content to display.
	 * @param string $column  Column name.
	 * @param array  $item    Items array.
	 *
	 * @since  2.0.0
	 * @access private
	 *
	 * @return string|boolean
	 */
	private function get_group_content( $content, $column, $item ) {

		$count_text = '';

		if ( ! empty( $item['count'] ) && $item['count'] > 1 && $column === $this->group_by ) {
			$count_text = ' (<strong>' . $item['count'] . '</strong>)';
		}

		return $content . $count_text;
	}

	/**
	 * Get default text if empty.
	 *
	 * Get an error text with custom class to show if the
	 * current column value is empty or n/a.
	 *
	 * @param string $value Field value.
	 *
	 * @since  2.0.0
	 * @access private
	 *
	 * @return string|boolean
	 */
	private function get_empty_content( $value ) {

		// Get default error text.
		if ( strtolower( $value ) === 'n/a' || empty( $value ) ) {
			return '<i>Not available</i>';
		}

		return false;
	}

	/**
	 * Bulk actions drop down.
	 *
	 * Options to be added to the bulk actions drop down for users
	 * to select. We have added 'Delete' actions.
	 * Registering filter - "ljk_log_list_bulk_actions".
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return array $actions Options to be added to the action select box.
	 */
	public function get_bulk_actions() {

		$actions = array(
			'bulk_delete' => __( 'Delete Selected', 'link-juice-keeper' ),
			'bulk_clean'  => __( 'Delete All', 'link-juice-keeper' ),
		);

		/**
		 * Filter hook to change actions.
		 *
		 * @note If you are adding extra actions
		 *    Make sure it's actions are properly added.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'ljk_log_list_bulk_actions', $actions );
	}

	/**
	 * Add extra action dropdown for grouping the error logs.
	 *
	 * @param string $which Top or Bottom.
	 *
	 * @access protected
	 * @since  2.0.0
	 *
	 * @return void
	 */
	public function extra_tablenav( $which ) {

		if ( $this->has_items() && 'top' === $which ) {

			// This filter is already documented above.
			$allowed_values = apply_filters( 'ljk_log_list_groupby_allowed', array( 'url', 'ref', 'ip', 'ua' ) );
			// Allowed/available columns.
			$available_columns = $this->linkJuiceKeeper_log_columns();
			// Consider only available columns.
			$column_names = array_intersect( $allowed_values, array_keys( $available_columns ) );
			// Add dropdown.
			echo '<div class="alignleft actions bulkactions">';
			echo '<select name="group_by_top" class="404_group_by">';
			echo '<option value="">' . esc_html__( 'Group by', 'link-juice-keeper' ) . '</option>';
			foreach ( $column_names as $column ) {
				echo '<option value="' . esc_attr( $column ) . '" ' . selected( $column, $this->group_by ) . '>' . esc_attr( $available_columns[ $column ] ) . '</option>';
			}
			echo '</select>';
			submit_button( __( 'Apply', 'link-juice-keeper' ), 'button', 'filter_action', false, array( 'id' => 'post-query' ) );
			echo '</div>';

			/**
			 * Action hook to add extra items in actions area.
			 *
			 * @param object $this Class instance.
			 * @param string $which Current location (top or bottom).
			 */
			do_action( 'ljk_log_list_extra_tablenav', $this, $which );
		}
	}

	/**
	 * To perform bulk actions.
	 *
	 * After security check, perform bulk actions selected by
	 * the user. Only allowed actions will be performed.
	 *
	 * @since  2.1.0
	 * @access private
	 * @uses   check_admin_referer() For security check.
	 *
	 * @return void|boolean
	 */
	private function process_actions() {

		// Get current action.
		$action = $this->current_action();

		// Get allowed actions array.
		$allowed_actions = array_keys( $this->get_bulk_actions() );

		// Verify only allowed actions are passed.
		if ( ! in_array( $action, $allowed_actions, true ) && 'delete' !== $action ) {
			return false;
		}

		// IDs of log entires to process.
		$ids = $this->link_juice_keeper_from_request( 'bulk-delete', true );

		// Run custom bulk actions.
		// Add other custom actions in switch..
		switch ( $action ) {
				// Normal selected deletes.
			case 'delete':
			case 'bulk_delete':
			case 'bulk_clean':
				$this->delete_logs( $ids, $action );
				break;
				// Add custom actions here.
		}

		return true;
	}

	/**
	 * Remove sensitive values from the URL.
	 *
	 * If WordPress nonce or admin referrer is found in url
	 * remove that and redirect to same page.
	 *
	 * @param boolean $action_performed If any actions performed.
	 *
	 * @access private
	 * @since  2.0.0
	 *
	 * @return void
	 */
	private function safe_redirect( $action_performed = false ) {

		// If sensitive data found, remove those and redirect.
		if (!empty($_GET['_wp_http_referer']) || !empty($_GET['_wpnonce'])) { //phpcs:ignore
			// Redirect to current page.
			wp_safe_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI']))); //phpcs:ignore
			exit();
		}

		// If bulk actions performed, redirect.
		if ( true === $action_performed ) {
			// Redirect to current page.
			wp_safe_redirect(remove_query_arg(array('action', 'action2'), wp_unslash($_SERVER['REQUEST_URI']))); //phpcs:ignore
			exit();
		}
	}

	/**
	 * Delete error logs.
	 *
	 * Bulk action processor to delete error logs according to
	 * the user selection. We are using IF ELSE loop instead of
	 * switch to easily handle conditions.
	 *
	 * @param mixed  $ids ID(s) of the log(s).
	 * @param string $action Current bulk action.
	 *
	 * @since  2.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function delete_logs( $ids, $action ) {

		global $wpdb;
		$table = $wpdb->prefix . 'link_juice_keeper';

		if ( is_numeric( $ids ) && 'delete' === $action ) {
			// If a single log is being deleted.
			$query = 'DELETE FROM ' . $table . ' WHERE id = ' . absint( $ids );
		} elseif ( is_array( $ids ) && 'bulk_delete' === $action ) {
			// If multiple selected logs are being deleted.
			$ids   = implode( ',', array_map( 'absint', $ids ) );
			$query = 'DELETE FROM ' . $table . " WHERE id IN($ids)";
		} elseif ( 'bulk_clean' === $action ) {
			// If deleting all logs.
			$query = 'DELETE FROM ' . $table;
		} else {
			// Incase if invalid log ids.
			return;
		}

		// Run query to delete logs.
		$wpdb->query($query); //phpcs:ignore
	}


	/**
	 * Available columns in error logs table.
	 * Registering filter - "ljk_redirect_statuses".
	 *
	 * @since  2.0.0
	 * @access private
	 *
	 * @return array Allowed HTTP status codes.
	 */
	public function linkJuiceKeeper_log_columns() {

		$columns = array(
			'date' => __( 'Date', 'link-juice-keeper' ),
			'url'  => __( '404 Path', 'link-juice-keeper' ),
			'ref'  => __( 'Referrer', 'link-juice-keeper' ),
			'ip'   => __( 'User IP', 'link-juice-keeper' ),
			'ua'   => __( 'User Agent', 'link-juice-keeper' ),
		);

		/**
		 * Filter for available columns.
		 * Registering filter - "ljk_log_columns".
		 *
		 * @param array columns name and slug.
		 *
		 * @since 2.0.0
		 */
		return (array) apply_filters( 'ljk_log_columns', $columns );
	}

	/**
	 * Retrieve data from $_REQUEST and sanitize
	 *
	 * @param string $key Key to get from request.
	 * @param mixed  $default Default value.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return array|string
	 */
	public function link_juice_keeper_from_request( $key = '', $default = '' ) {

		// Return default value if key is not given.
		if ( empty( $key ) || ! is_string( $key ) ) {
			return $default;
		}

		//phpcs:disable
		// Return default value if key not set.
		if (!isset($_REQUEST[$key])) {
			return $default;
		}

		// Trim output.
		if (is_string($_REQUEST[$key])) {
			return sanitize_text_field(wp_unslash($_REQUEST[$key]));
		} elseif (is_array($_REQUEST[$key])) {
			return array_map('sanitize_text_field', wp_unslash($_REQUEST[$key]));
		}
		//phpcs:enable

		return $default;
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @param  Mixed $a First item to compare.
	 * @param  Mixed $b Second item to compare.
	 *
	 * @since  2.0.0
	 * @return Mixed
	 */
	private function sort_data( $a, $b ) {
		// Set defaults.
		$orderby = 'date';
		$order   = 'desc';

		//phpcs:disable
		// If orderby is set, use this as the sort column.
		if (!empty($_GET['orderby'])) {
			$orderby = sanitize_title(wp_unslash($_GET['orderby']));
		}

		// If order is set use this as the order.
		if (!empty($_GET['order'])) {
			$order = sanitize_title(wp_unslash($_GET['order']));
		}
		//phpcs:enable

		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		if ( 'asc' === $order ) {
			return $result;
		}

		return -$result;
	}
}
