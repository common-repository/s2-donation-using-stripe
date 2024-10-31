<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * S2_Donation_Stripe_API class.
 *
 * Communicates with Stripe API.
 *
 * @class   S2_Donation_Stripe_API
 * @package S2 Donation
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Donation_Stripe_API' ) ) {

	class S2_Donation_Stripe_API {

		/**
		 * Stripe API Endpoint
		 */
		const ENDPOINT           = 'https://api.stripe.com/v1/';
		const STRIPE_API_VERSION = '2020-08-27';

		/**
		 * Secret API Key.
		 * @var string
		 */
		private static $secret_key = '';

		/**
		 * Set secret API Key.
		 * @param string $key
		 */
		public static function set_secret_key( $secret_key ) {
			self::$secret_key = $secret_key;
		}

		/**
		 * Get secret key.
		 * @return string
		 */
		public static function get_secret_key() {
			if ( ! self::$secret_key ) {
				$options = get_option('s2dn_settings');

				if ( isset( $options['testmode'], $options['secret_key'], $options['test_secret_key'] ) ) {
					self::set_secret_key( 'yes' === $options['testmode'] ? $options['test_secret_key'] : $options['secret_key'] );
				}
			}
			return self::$secret_key;
		}

		/**
		 * Generates the user agent we use to pass to API request so
		 * Stripe can identify our application.
		 *
		 * @since 1.0.0
		 */
		public static function get_user_agent() {
			return [
						'lang'         => 'php',
						'lang_version' => phpversion(),
						'uname'        => php_uname(),
					];
		}

		/**
		 * Generates the headers to pass to API request.
		 *
		 * @since 1.0.0
		 */
		public static function get_headers() {
			$user_agent = self::get_user_agent();

			return apply_filters(
				's2_donation_stripe_request_headers',
				[
					'Authorization'              => 'Basic ' . base64_encode( self::get_secret_key() . ':' ),
					'Stripe-Version'             => self::STRIPE_API_VERSION,
					'X-Stripe-Client-User-Agent' => json_encode( $user_agent ),
				]
			);
		}

		/**
		 * Send the request to Stripe's API
		 *
		 * @since 1.0.0
		 * @param array $request
		 * @param string $api
		 * @param string $method
		 * @param bool $with_headers To get the response with headers.
		 * @return stdClass|array
		 * @throws Exception
		 */
		public static function request( $request, $api = 'charges', $method = 'POST', $with_headers = false ) {
			$headers         = self::get_headers();
			$idempotency_key = '';

			if ( 'charges' === $api && 'POST' === $method ) {
				$customer        = ! empty( $request['customer'] ) ? $request['customer'] : '';
				$source          = ! empty( $request['source'] ) ? $request['source'] : $customer;
			}

			$response = wp_safe_remote_post(
				self::ENDPOINT . $api,
				[
					'method'  => $method,
					'headers' => $headers,
					'body'    => apply_filters( 's2_donation_stripe_request_body', $request, $api ),
					'timeout' => 70,
				]
			);

			if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
				S2_Donation_Logger::log( 'There was a problem connecting to the Stripe API endpoint.' . print_r( $response, true ) );
			}

			if ( $with_headers ) {
				return [
							'headers' => wp_remote_retrieve_headers( $response ),
							'body'    => json_decode( $response['body'] ),
						];
			}

			return json_decode( $response['body'] );
		}

	}

}
