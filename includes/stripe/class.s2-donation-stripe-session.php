<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Represents a Stripe Session.
 *
 * @class S2_Donation_Stripe_Session
 * @package S2 Donation
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Donation_Stripe_Session' ) ) {

	class S2_Donation_Stripe_Session {

		/**
		 * Stripe Session ID
		 * @var string
		 */
		private $id = '';

		/**
		 * Constructor
		 *
		 * @param string $stripe_ses_id
		 * @since  1.0.0
		 */
		public function __construct( $stripe_ses_id = 0 ) {
			if ( $stripe_ses_id ) {
				$this->set_id( $stripe_ses_id );
			}
		}

		/**
		 * Get Stripe Session ID
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Set Stripe Session ID.
		 *
		 * @param string $id
		 * @since  1.0.0
		 */
		public function set_id( $id ) {
			// plugin-fw function s2_clean
			$this->id = s2_clean( $id );
		}

		/**
		 * Create Stripe Session via API.
		 *
		 * @param array $args
		 *
		 * @since  1.0.0
		 * @return String
		 */
		public function create_session( $args = [] ) {
			$response = S2_Donation_Stripe_API::request( $args, 'checkout/sessions' );

			if ( ! empty( $response->error ) ) {
				S2_Donation_Logger::log( $response->error->message . print_r( $response, true ) );

				return 0;
			}

			$this->set_id( $response->id );

			return $response->id;
		}

		/**
		 * Retrieve the Stripe Session through the API.
		 *
		 * @param array $args
		 *
		 * @since  1.0.0
		 * @return Object
		 */
		public function retrieve_session( $args = [] ) {
			$response = S2_Donation_Stripe_API::request( $args, 'checkout/sessions/' . $this->get_id(), 'GET' );

			if ( ! empty( $response->error ) ) {
				S2_Donation_Logger::log( $response->error->message . print_r( $response, true ) );

				return 0;
			}

			return $response;
		}

	}

}
