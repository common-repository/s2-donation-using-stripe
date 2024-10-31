<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * General settings
 *
 * @package S2 Donation
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */

$settings = [

	'api_credentials'      	=> [
									'title'       => __( 'Stripe Account Keys', 's2-donation' ),
									'type'        => 'title',
									'description' => __( 'Manually enter Stripe keys below.', 's2-donation' ),
								],
	'testmode'        	   	=> [
									'title'       => __( 'Test mode', 's2-donation' ),
									'label'       => __( 'Enable Test Mode', 's2-donation' ),
									'type'        => 'checkbox',
									'description' => __( 'Place the donation in test mode using test API keys.', 's2-donation' ),
									'default'     => 'yes',
									'desc_tip'    => false,
								],
	'test_publishable_key' 	=> [
									'title'       => __( 'Test Publishable Key', 's2-donation' ),
									'type'        => 'text',
									'description' => __( 'Test Publishable keys starting with "pk_test_".', 's2-donation' ),
									'default'     => '',
									'desc_tip'    => false,
								],
	'test_secret_key'      	=> [
									'title'       => __( 'Test Secret Key', 's2-donation' ),
									'type'        => 'password',
									'description' => __( 'Test Secret keys starting with "sk_test_" or "rk_test_".', 's2-donation' ),
									'default'     => '',
									'desc_tip'    => false,
								],
	'publishable_key'      	=> [
									'title'       => __( 'Live Publishable Key', 's2-donation' ),
									'type'        => 'text',
									'description' => __( 'Live Publishable keys starting with "pk_live_".', 's2-donation' ),
									'default'     => '',
									'desc_tip'    => false,
								],
	'secret_key'           	=> [
									'title'       => __( 'Live Secret Key', 's2-donation' ),
									'type'        => 'password',
									'description' => __( 'Live Secret keys starting with "sk_live_" or "rk_live_".', 's2-donation' ),
									'default'     => '',
									'desc_tip'    => false,
								],
	'donation_form'        	=> [
									'title'       => __( 'Donation Form Settings', 's2-donation' ),
									'type'        => 'title',
									'description' => __( 'Add donation form settings<br /> Copy this shortcode and paste it into your post, page, or text widget content:', 's2-donation' ),
								],
	'donation_shortcode'    => [
									'title'       => __( 'Donation Shortcode', 's2-donation' ),
									'type'        => 'readonly',
									'description' => __( '[s2-donation-form]', 's2-donation' ),
								],
	'donation_title'	 	=> [
									'title'       => __( 'Title', 's2-donation' ),
									'type'        => 'text',
									'description' => __( 'Donation form title' ),
									'default'     => '',
									'desc_tip'    => false,
								],
	'donation_description' 	=> [
									'title'       => __( 'Description', 's2-donation' ),
									'type'        => 'textarea',
									'description' => __( 'Donation form description' ),
									'default'     => '',
									'desc_tip'    => false,
								],
	'donation_amount'      	=> [
									'title'       => __( 'Donation Amount', 's2-donation' ),
									'type'        => 'price',
									'description' => __( 'Add donation amount. This amount will be disaply on form as default amount, user can change this amount', 's2-donation' ),
									'default'     => '1',
									'desc_tip'    => false,
									'custom_attributes' => [ 'min' => 1 ],
								],
	'fixed_donation_amount' => [
									// 'title'       => __( 'Fixed Donation Amount', 's2-donation' ),
									'label'       => __( 'Fixed Donation Amount', 's2-donation' ),
									'type'        => 'checkbox',
									'description' => __( 'Enable / Disable fixed donation amount. If fixed donation amount enabled, user will not allow to edit donation amount in form', 's2-donation' ),
									'default'     => 'no',
									'desc_tip'    => false,
								],
	'recurring_donation' 	=> [
									'title'       => __( 'Recurring Donation', 's2-donation' ),
									'label'       => __( 'Recurring Donation', 's2-donation' ),
									'type'        => 'checkbox',
									'description' => __( 'Enable / Disable recurring donation. To receive donation amount every day / week / month / year etc. untill cancel from stripe dashboard. This will show dropdown on form, if user select it then only recurring donation will start', 's2-donation' ),
									'default'     => 'no',
									'desc_tip'    => false,
								],
    'donation_currency'    	=> [
									'title'       => __( 'Donation Currency', 's2-donation' ),
									'type'        => 'select',
									'description' => __( 'Select donation currency. To receive donation in other country currency, you should register as business account on stripe', 's2-donation' ),
									'options'     => s2_get_currencies(),
									'desc_tip'    => false,
								],
	'donation_success_page' => [
									'title'       => __( 'Donation Success Page', 's2-donation' ),
									'type'        => 'select',
									'description' => __( 'Select donation success page. Once donation successfully done by user stripe redirect to this page', 's2-donation' ),
									'options'     => s2_get_pages(),
									'desc_tip'    => false,
								],
	'donation_cancel_page' 	=> [
									'title'       => __( 'Donation Cancel Page', 's2-donation' ),
									'type'        => 'select',
									'description' => __( 'Select donation cancel page. Once donation cancel by user stripe redirect to this page', 's2-donation' ),
									'options'     => s2_get_pages(),
									'desc_tip'    => false,
								],
	'general_settings'   	=> [
									'title' 	  => __( 'General settings', 's2-donation' ),
									'type'  	  => 'title',
								],
	'enable_log'            => [
									'title'       => __( 'Log debug messages', 's2-donation' ),
									'description' => __( 'Save debug messages to the log.', 's2-donation' ),
									'type'        => 'checkbox',
									'default'     => 'no',
								],

];

return $settings;
