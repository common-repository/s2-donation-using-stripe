<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Implements rest api features of S2 Donation
 *
 * @package S2 Donation
 * @since   1.0.3
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */

/**
 * Register rest route
 *
 * @since 1.0.3
 */
function s2_donation_rest_api_init() {

	$namespace = 's2-donation/v1';

	register_rest_route( $namespace,
		'/admin-settings',
		[
			'methods' 			  => WP_REST_Server::CREATABLE,
			'callback' 			  => 's2_donation_rest_save_admin_settings',
			'permission_callback' => function() {
				if ( current_user_can( 'manage_options' ) ) {
					return true;
				} else {
					return new WP_Error( 's2_forbidden', __( "You are not allowed to edit settings.", 's2-donation' ), [ 'status' => 403 ] );
				}
			},
		]
	);

}
add_action( 'rest_api_init', 's2_donation_rest_api_init', 10, 0 );

/**
 * Save admin settings
 *
 * @since 1.0.3
 */
function s2_donation_rest_save_admin_settings( WP_REST_Request $request ) {

	if ( $_POST['action'] == 'plugin-settings' ) {
		S2_Donation_Plugin_Setting()->process_admin_options();
	} elseif ( $_POST['action'] == 'mail-settings' ) {
		S2_Donation_Mail_Setting()->process_admin_options();
	}

	$response = [ 'success' => true ];

	return rest_ensure_response( $response );

}
