<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Implements features of S2 Donation Frontend
 *
 * @class   S2_Donation_Frontend
 * @package S2 Donation
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Donation_Frontend' ) ) {

	class S2_Donation_Frontend {

		/**
		 * Plugin settings
		 */
		public $s2dn_settings;

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Donation_Frontend
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \S2_Donation_Frontend
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

			// custom styles and javascripts
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles_scripts' ], 11 );

			// donation form shortcode
			add_shortcode( 's2-donation-form', [ $this, 'get_donation_from' ] );

			/* Ajax create stripe session */
			add_action( 'wp_ajax_create_stripe_session', [ $this, 'create_stripe_session' ] );
			add_action( 'wp_ajax_nopriv_create_stripe_session', [ $this, 'create_stripe_session' ] );

		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @since  1.0.0
		 */
		public function enqueue_styles_scripts() {

			wp_enqueue_style( 's2_donation_frontend', S2_DN_ASSETS_URL . '/css/frontend' . S2_DN_SUFFIX . '.css', [], S2_DN_VERSION );

			wp_enqueue_script(
				's2_donation_frontend',
				S2_DN_ASSETS_URL . '/js/frontend' . S2_DN_SUFFIX . '.js',
				[ 'jquery' ],
				S2_DN_VERSION,
				true
			);

			$publishable_key = '';
			if ( isset( $this->s2dn_settings['testmode'], $this->s2dn_settings['publishable_key'], $this->s2dn_settings['test_publishable_key'] ) ) {
				$publishable_key = ( 'yes' === $this->s2dn_settings['testmode'] ? $this->s2dn_settings['test_publishable_key'] : $this->s2dn_settings['publishable_key'] );
			}

			wp_localize_script(
				's2_donation_frontend',
				's2_donation_frontend',
				[
					'ajaxurl'         => admin_url( 'admin-ajax.php' ),
					'donation_nonce'  => wp_create_nonce( 'donation-nonce' ),
					'publishable_key' => $publishable_key,
				]
			);

			wp_register_script(
				's2_donation_stripe',
				'https://js.stripe.com/v3/',
				[],
				3,
				true
			);

		}

		/**
		 * Create stripe session
		 *
		 * @since  1.0.0
		 */
		public function create_stripe_session() {

			check_ajax_referer( 'donation-nonce', 'security' );

			$error = [];

			// validate donation email
			$s2_donation_email = sanitize_email( $_REQUEST['s2-donation-email'] );
			if( empty( $s2_donation_email ) ) $error['s2-donation-email'] = 'Valid email required';

			// validate donation amount
			if( empty( $this->s2dn_settings['fixed_donation_amount'] ) || $this->s2dn_settings['fixed_donation_amount'] == 'no' ) {
				$s2_donation_amount = sanitize_text_field( $_REQUEST['s2-donation-amount'] );
			} else {
				$s2_donation_amount = sanitize_text_field( $this->s2dn_settings['donation_amount'] );
			}

			$s2_donation_amount = floatval( $s2_donation_amount );
			if( empty( $s2_donation_amount ) || $s2_donation_amount < 0 ) $error['s2-donation-amount'] = 'Valid amount required';

			// send error if not empty
			if( ! empty( $error ) ) {
				$error['s2_donation_error_message'] = 'Fields are not validated';
				echo json_encode( [ 'error' => $error ] );

				exit();
			}

			// add donation details in db
			$donation_id = S2_Donation_Db()->add_donation_details( $s2_donation_email, $s2_donation_amount, $this->s2dn_settings['donation_currency'], '' );
			if( empty( $donation_id ) ) {
				$error['s2_donation_error_message'] = 'Db Insert Error';
				echo json_encode( [ 'error' => $error ] );

				exit();
			}

			// create stripe session
			$args = [

			  	'payment_method_types' => ['card'],

			  	'line_items' => [
			  		[

				    	'price_data' => [

				      		'currency' => $this->s2dn_settings['donation_currency'],

				      		'unit_amount' => s2_get_stripe_amount( $s2_donation_amount, $this->s2dn_settings['donation_currency'] ),

				      		'product_data' => [

				        		'name' => 'Donate',

				      		],

				    	],

				    	'quantity' => 1,

				  	]
			  	],

			  	'mode' => 'payment',

			  	'success_url' => ( $this->s2dn_settings['donation_success_page'] ? get_the_permalink( $this->s2dn_settings['donation_success_page'] ) : get_home_url() ) . '?donation_id=' . wp_hash( $donation_id, 'nonce' ) . '&payment_status=paid',

			  	'cancel_url' => ( $this->s2dn_settings['donation_cancel_page'] ? get_the_permalink( $this->s2dn_settings['donation_cancel_page'] ) : get_home_url() ) . '?donation_id=' . wp_hash( $donation_id, 'nonce' ) . '&payment_status=unpaid',

			  	'customer_email' => $s2_donation_email,

			  	'submit_type' => 'donate',

			];

			// get recurring frequency
			$s2_recurring_donation = sanitize_text_field( $_REQUEST['s2-recurring-donation'] );
			if( ! empty( $s2_recurring_donation ) ) {
				$s2_recurring_donation = s2_get_recurring_frequency_options( $s2_recurring_donation );

				// add recurring donation option
				if( ! empty( $s2_recurring_donation ) ) {
					$args['line_items'][0]['price_data']['recurring'] = [

							        		'interval' => $s2_recurring_donation['time'],

						        			'interval_count' => $s2_recurring_donation['period'],

							      		];
					$args['mode'] = 'subscription';
					unset( $args['submit_type'] );
				}
			}

			$stripe_session = new S2_Donation_Stripe_Session();
			$session_id = $stripe_session->create_session( $args );
			if( empty( $session_id ) ) {
				$error['s2_donation_error_message'] = 'Stripe Session Error';
				echo json_encode( [ 'error' => $error ] );

				exit();
			}

			$data  = [ 'stripe_session_id' => $session_id ];
			$where = [ 'id' => $donation_id ];
			S2_Donation_Db()->update_donation_details( $data, $where );

    		echo json_encode( [ 'id' => $session_id ] );

			exit();			

		}

		/**
		 * Create donation from shortcode 
		 *
		 * @since  1.0.0
		 */
		function get_donation_from( $attrs = [] ) {

			ob_start();
			include_once S2_DN_TEMPLATE_PATH . '/frontend/donation-form.php';
			$html = ob_get_clean();

			wp_enqueue_script( 's2_donation_stripe' );

			return $html;

		}

	}

}

/**
 * Unique access to instance of S2_Donation_Frontend class
 */
S2_Donation_Frontend::get_instance();
