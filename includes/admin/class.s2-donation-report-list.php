<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Implements admin report list features of S2 Donation
 *
 * @class   S2_Donation_Report_List
 * @package S2 Donation
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Donation_Report_List' ) ) {

	if ( ! class_exists( 'WP_List_Table' ) ) require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

	class S2_Donation_Report_List extends WP_List_Table {

		/**
		 * @var string
		 */
		private $post_type;

		/**
		 * @var int
		 */
		private $per_page;

		/**
		 * Constructor.
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			parent::__construct(
				[
					'singular' => 'Report',
					'plural'   => 'Reports',
					'ajax'     => false,
				]
			);

			$this->post_type = 'product';
			$this->per_page  = 10;

		}

		/**
		 * No items found text.
		 *
		 * @since  1.0.0
		 */
		public function no_items() {
			esc_html_e( 'No Donation found.', 's2-donation' );
		}

		/**
		 * Get column value.
		 *
		 * @param WP_Post $post WP Post object.
		 * @param string  $column_name Column name.
		 *
		 * @since  1.0.0
		 *
		 * @return string
		 */
		public function column_default( $donation, $column_name ) {

			switch ( $column_name ) {

				case 'email':
					return $donation->email;

				case 'amount':
					return s2_get_currency_symbols( $donation->currency ) . $donation->amount;

			}

			return '';
		}

		/**
		 * Get columns.
		 *
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_columns() {
			$columns = [
							'email'  => __( 'Email', 's2-donation' ),
							'amount'  => __( 'Amount', 's2-donation' ),
						];

			return $columns;
		}

		/**
		 * Get sortable columns.
		 *
		 * @since  1.0.0
		 *
		 * @return array
		 */
		function get_sortable_columns() {
			$sortable_columns = [
				'amount'  => [ 'amount', false ],
			];

			return $sortable_columns;
		}

		/**
		 * Prepare customer list items.
		 *
		 * @since  1.0.0
		 */
		public function prepare_items() {
			global $wpdb;

			$table_name = $wpdb->prefix . 's2_donation';
			$current_page = absint( $this->get_pagenum() );

			/**
			 * Init column headers.
			 */
			$this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns() ];

			// Check if the request came from search form
			$where = "";
			if ( ! empty( $_REQUEST['s'] ) ) {
				$query_search = sanitize_text_field( $_REQUEST['s'] );

				$where .= $wpdb->prepare( " AND ( w_d.email LIKE CONCAT('%', %s, '%') )", [ $query_search ] );
			}

			// Check if the section is selected
			if ( empty( $_REQUEST['tab'] ) || $_REQUEST['tab'] == 'report' ) {
				$stripe_session_id = 'cs_live';
				if( ! empty( $_REQUEST['section'] ) && $_REQUEST['section'] == 'test_data' ) {
					$stripe_session_id = 'cs_test';
				}

				$where .= $wpdb->prepare( " AND ( w_d.stripe_session_id LIKE CONCAT('%', %s, '%') )", [ $stripe_session_id ] );
			}

			$orderby = 'payment_date';
	        if( ! empty( $_GET['orderby'] ) ) {
	        	$orderby = sanitize_text_field( $_GET['orderby'] );
	        }

			$order = 'DESC';
	        if( ! empty( $_GET['order'] ) ) {
	        	$order = sanitize_text_field( $_GET['order'] );
	        }

	        $where .= ' AND w_d.payment_status = "paid"';
	    	$query = "SELECT w_d.email, w_d.amount, w_d.currency FROM $table_name AS w_d 
		    		WHERE 1=1 $where 
		    		ORDER BY $orderby $order";

	    	$total_items = $wpdb->query( $query );

	    	// Page Number
			$paged = ! empty( $_GET['paged'] ) ? $_GET['paged'] : 1;
			if ( ! is_numeric( $paged ) || $paged <= 0 ) {
				$paged = 1;
			}
			$offset = ( $paged - 1 ) * $this->per_page;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $this->per_page;

			$this->items = $wpdb->get_results( $query );

			/**
			 * Pagination.
			 */
			$this->set_pagination_args(
				[
					'total_items' => $total_items,
					'per_page'    => $this->per_page,
					'total_pages' => ceil( $total_items / $this->per_page ),
				]
			);
		}

		/**
		 * Display the search box.
		 *
		 * @param string $text     The search button text
		 * @param string $input_id The search input id
		 *
		 * @since  1.0.0
		 */
		public function search_box( $text, $input_id ) {

			$input_id = $input_id . '-search-input';
			$input_id = esc_attr( $input_id );

			if ( ! empty( $_REQUEST['orderby'] ) ) {
				echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
			}
			if ( ! empty( $_REQUEST['order'] ) ) {
				echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
			}

			?>
			<p class="search-box">
				<label class="screen-reader-text" for="<?php echo $input_id; ?>"><?php echo $text; ?>:</label>
				<input type="search" id="<?php echo $input_id; ?>" name="s" value="<?php _admin_search_query(); ?>" placeholder="<?php _e( 'Search by email', 's2-donation' ); ?>" />
				<?php submit_button( $text, 'button', '', false, [ 'id' => 'search-submit' ] ); ?>
			</p>
			<?php

		}

	}

}
