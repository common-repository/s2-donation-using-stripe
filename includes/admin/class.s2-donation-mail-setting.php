<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Implements admin mail setting of S2 Donation
 *
 * @class   S2_Donation_Mail_Setting
 * @package S2 Donation
 * @since   1.0.1
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Donation_Mail_Setting' ) ) {

	class S2_Donation_Mail_Setting extends S2_Settings_API {

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Donation_Mail_Setting
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since  1.0.1
		 * @return \S2_Donation_Mail_Setting
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
		 * @param array $args
		 *
		 * @since  1.0.1
		 */
		public function __construct() {

			$this->plugin_id = 's2dn';

			// Load the settings.
			$this->init_form_fields();

			// $this->settings field has values from db
			$this->init_settings();

			if( ! empty( $_POST ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 's2dn-mail-setting' ) ) {
				// $this->process_admin_options();
			}

		}

		/**
		 * Initialize form fields for the admin
		 *
		 * @since  1.0.1
		 */
		public function init_form_fields() {
			$this->form_fields = include 'settings/mail-settings.php';
		}

		/**
		 * Output the admin options table.
		 *
		 * @since  1.0.1
		 */
		public function admin_options() {
		?>

			<div class="wrap">
				<form method="post" id="s2-donation-admin-form" action="" enctype="multipart/form-data">
					<?php echo '<table class="form-table">' . $this->generate_settings_html( $this->get_form_fields(), false ) . '</table>'; ?>
					<p class="submit">
						<button name="save" class="button-primary" type="submit" value="<?php esc_attr_e( 'Save changes', 's2-donation' ); ?>"><?php esc_html_e( 'Save changes', 's2-donation' ); ?></button>
					</p>
					<input type="hidden" name="action" value="mail-settings">
					<div class="response-output" aria-hidden="true">
						Settings has been saved successfully
					</div>
				</form>
			</div>

		<?php
		}

	}

}

/**
 * Unique access to instance of S2_Donation_Mail_Setting class
 *
 * @return \S2_Donation_Mail_Setting
 */
function S2_Donation_Mail_Setting() {
	return S2_Donation_Mail_Setting::get_instance();
}
