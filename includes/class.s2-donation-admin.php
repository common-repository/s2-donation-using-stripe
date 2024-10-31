<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Implements admin features of S2 Donation
 *
 * @class   S2_Donation_Admin
 * @package S2 Donation
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Donation_Admin' ) ) {

	class S2_Donation_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Donation_Admin
		 */

		protected static $instance;

		/**
		 * @var $_panel Panel Object
		 */
		protected $_panel;

		/**
		 * Returns single instance of the class
		 *
		 * @return \S2_Donation_Admin
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

			require_once S2_DN_INC . 'admin/class.s2-donation-plugin-panel.php';
			require_once S2_DN_INC . 'admin/class.s2-donation-report-list.php';
			require_once S2_DN_INC . 'admin/class.s2-donation-plugin-setting.php';
			require_once S2_DN_INC . 'admin/class.s2-donation-mail-setting.php';

			add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

		}

		/**
		 * Load admin scripts.
		 *
		 * @since 1.0.0
		 */
		public function admin_scripts() {
			if ( 's2-plugins_page_s2-donation' !== get_current_screen()->id ) {
				return;
			}

			wp_enqueue_style( 's2_donation_admin', S2_DN_ASSETS_URL . '/css/admin' . S2_DN_SUFFIX . '.css', [], S2_DN_VERSION );

			wp_enqueue_script(
				's2_donation_admin',
				S2_DN_ASSETS_URL . '/js/admin' . S2_DN_SUFFIX . '.js',
				[],
				S2_DN_VERSION,
				true
			);

			wp_localize_script(
				's2_donation_admin',
				's2_donation_admin',
				[
					'ajaxurl'      => admin_url( 'admin-ajax.php' ),
					'api_settings' => [
										'root' 		=> esc_url_raw( rest_url( 's2-donation/v1' ) ),
										'namespace' => 's2-donation/v1',
										'nonce' 	=> ( wp_installing() && ! is_multisite() ) ? '' : wp_create_nonce( 'wp_rest' ),
									],
				]
			);

		}

	}

}

/**
 * Unique access to instance of S2_Donation_Admin class
*/
if ( is_admin() || current_user_can( 'manage_options' ) ) {
	S2_Donation_Admin::get_instance();
}
