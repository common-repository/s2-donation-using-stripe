<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Mail settings
 *
 * @package S2 Donation
 * @since   1.0.1
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */

$settings = [

	'enable_mail'            => [
									'title'       => __( 'Enable mail sending', 's2-donation' ),
									'description' => __( 'Enable mail sending, so mail will be sent to person who made a donation.', 's2-donation' ),
									'type'        => 'checkbox',
									'default'     => 'no',
								],
	'mail_from'	 			=> [
									'title'       => __( 'Mail from', 's2-donation' ),
									'type'        => 'text',
									'description' => __( 'Donation mail from' ),
									'default'     => get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>',
									'desc_tip'    => false,
								],
	'mail_subject'	 		=> [
									'title'       => __( 'Subject', 's2-donation' ),
									'type'        => 'text',
									'description' => __( 'Donation mail subject' ),
									'default'     => 'Thank you for your donation',
									'desc_tip'    => false,
								],
	'mail_body' 			=> [
									'title'       => __( 'Message', 's2-donation' ),
									'type'        => 'textarea',
									'description' => __( 'Donation mail body' ),
									'default'     => 'Message Body: Thank you very much for your recent donation.',
									'desc_tip'    => false,
									'custom_attributes' => [ 'cols' => '20', 'rows' => '15' ],
								],

];

return $settings;
