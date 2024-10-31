<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Implements admin features of S2 Donation
 *
 * @class   S2_Donation_Db
 * @package S2 Donation
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Donation_Db' ) ) {

	class S2_Donation_Db {

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Donation_Db
		 */

		protected static $instance;

		/**
		 * @var $s2_donation_db_version
		 */
		protected $s2_donation_db_version = '1.0.0';

		/**
		 * Returns single instance of the class
		 *
		 * @return \S2_Donation_Db
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Install the table s2_donation.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function s2_donation_db_install() {
			global $wpdb;

			$installed_ver = get_option( 's2_donation_db_version' );

			if ( $installed_ver != $this->s2_donation_db_version ) {

				$table_name = $wpdb->prefix . 's2_donation';

				$charset_collate = $wpdb->get_charset_collate();

				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
							`id` bigint NOT NULL AUTO_INCREMENT,
							`email` varchar(100) NOT NULL,
							`amount` decimal(19,4) NOT NULL,
							`currency` varchar(3) NOT NULL,
							`stripe_session_id` varchar(255) NOT NULL,
							`payment_date` bigint NOT NULL,
							`payment_status` varchar(50) NULL,
							PRIMARY KEY (id)
						) $charset_collate;";

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );

				update_option( 's2_donation_db_version', $this->s2_donation_db_version );
			}
		}

		/**
		 * Check if the function s2_donation_db_install must be installed or updated.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function s2_donation_update_db_check() {
			if ( get_site_option( 's2_donation_db_version' ) != $this->s2_donation_db_version ) {
				$this->s2_donation_db_install();
			}
		}

		/**
		 * Add donation details
		 *
		 * @param string $email
		 * @param float  $amount
		 */
		public function add_donation_details( $email, $amount, $currency, $stripe_session_id ) {
			global $wpdb;

			$table_name = $wpdb->prefix . 's2_donation';

			$data = [
						'email'   			=> $email,
						'amount'        	=> $amount,
						'currency'        	=> $currency,
						'payment_date' 		=> time(),
						'stripe_session_id' => $stripe_session_id,
					];

			$result = $wpdb->insert( $table_name, $data );
			if( $result ) return $wpdb->insert_id;

			return 0;

		}

		/**
		 * Update donation details
		 *
		 * @param int 	$id
		 * @param float $amount
		 */
		public function update_donation_details( $data, $where ) {
			global $wpdb;

			$table_name = $wpdb->prefix . 's2_donation';

			$wpdb->update( $table_name, $data, $where );
		}

	}

}

/**
 * Unique access to instance of S2_Donation_Db class
 *
 * @return \S2_Donation_Db
 */
function S2_Donation_Db() {
	return S2_Donation_Db::get_instance();
}
