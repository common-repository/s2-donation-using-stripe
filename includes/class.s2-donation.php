<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Implements features of S2 Donation
 *
 * @class   S2_Donation
 * @package S2 Donation
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Donation' ) ) {

	class S2_Donation {

		/**
		 * Plugin settings
		 */
		public $s2dn_settings;

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Donation
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \S2_Donation
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			// Plugin settings
			$this->s2dn_settings = get_option('s2dn_settings');

			require_once S2_DN_INC . 'functions.s2-donation.php';
			require_once S2_DN_INC . 'class.s2-donation-logger.php';
			require_once S2_DN_INC . 'class.s2-donation-db.php';
			require_once S2_DN_INC . 'stripe/class.s2-donation-stripe-api.php';
			require_once S2_DN_INC . 'stripe/class.s2-donation-stripe-session.php';
			require_once S2_DN_INC . 'class.s2-donation-admin.php';
			require_once S2_DN_INC . 'class.s2-donation-frontend.php';
			require_once S2_DN_INC . 'class.s2-donation-mail.php';
			require_once S2_DN_INC . 'class.s2-donation-rest-api.php';

			// check db and create table s2_donation
			S2_Donation_Db()->s2_donation_update_db_check();

			// update donation payment status after stripe redirect to success page url
			$this->update_payment_status();

		}

		/**
		 * Update donation payment status after stripe redirect to success page url
		 *
		 * @since  1.0.0
		 */
		public function update_payment_status() {

			global $wpdb;

			if( empty( $_REQUEST['donation_id'] ) || empty( $_REQUEST['payment_status'] ) ) return;

			$donation_id 	= sanitize_text_field( $_REQUEST['donation_id'] );
			$payment_status = sanitize_text_field( $_REQUEST['payment_status'] );

			$table_name = $wpdb->prefix . 's2_donation';
			$query = "SELECT s2_d.id FROM $table_name AS s2_d 
					    		WHERE 1=1 AND s2_d.payment_status IS NULL 
					    		ORDER BY s2_d.id DESC";

			$donations = $wpdb->get_results( $query );
			foreach ( $donations as $donation ) {

				if( $donation_id == wp_hash( $donation->id, 'nonce' ) ) {

					$data  = [ 'payment_status' => $payment_status ];
					$where = [ 'id' => $donation->id, 'payment_status' => NULL ];
					S2_Donation_Db()->update_donation_details( $data, $where );

					$query = "SELECT s2_d.email FROM $table_name AS s2_d 
					    		WHERE 1=1 AND s2_d.id = " . $donation->id;

					$donation = $wpdb->get_results( $query );

					echo "<script>window.onload = function( e ) { send_mail( '" . $donation[0]->email . "' ); }</script>";

					break;

				}

			}

		}

	}

}

/**
 * Unique access to instance of S2_Donation class
 *
 * @return \S2_Donation
 */
function S2_Donation() {
	return S2_Donation::get_instance();
}
