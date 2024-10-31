<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Implements admin features of S2 Donation Mail
 *
 * @class   S2_Donation_Mail
 * @package S2 Donation
 * @since   1.0.3
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Donation_Mail' ) ) {

	class S2_Donation_Mail {

		/**
		 * Plugin settings
		 */
		public $s2dn_settings;

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Donation_Mail
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \S2_Donation_Mail
		 * @since 1.0.3
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
		 * @since  1.0.3
		 */
		public function __construct() {

			// Plugin settings
			$this->s2dn_settings = get_option('s2dn_settings');

			/* Ajax send_mail */
			add_action( 'wp_ajax_send_mail', [ $this, 'send_mail' ] );
			add_action( 'wp_ajax_nopriv_send_mail', [ $this, 'send_mail' ] );

		}

		/**
		 * Send mail after stripe redirect to success page url
		 *
		 * @since 1.0.3
		 */
		public function send_mail() {

			check_ajax_referer( 'donation-nonce', 'security' );

			if( empty( $_POST['email'] ) ) return;

			if( $this->s2dn_settings['enable_mail'] == 'yes' ) {

				$subject = $this->s2dn_settings['mail_subject'];
				$body 	 = $this->s2dn_settings['mail_body'];
				$headers = "From: " . $this->s2dn_settings['mail_from'] . "\n";
				$headers .= "Content-Type: text/html\n";

				return wp_mail( $_POST['email'], $subject, $body, $headers );

			}

		}

	}

}

/**
 * Unique access to instance of S2_Rest_Mail class
 */
S2_Donation_Mail::get_instance();
