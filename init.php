<?php
/**
 * Plugin Name: S2 Donation using Stripe
 * Plugin URI: 
 * Description: <code><strong>S2 Donation using Stripe</strong></code> allows you to receive donation using stripe. User can donate amount using cards(VISA, Master etc.).
 * Version: 1.0.7
 * Author: Shuban Studio <shuban.studio@gmail.com>
 * Author URI: https://shubanstudio.github.io/
 * Text Domain: s2-donation
 * Domain Path: /languages/
 */

/**
 * @package S2 Donation
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Define constants __
! defined( 'S2_DN_DIR' ) 			&& define( 'S2_DN_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'S2_DN_VERSION' ) 		&& define( 'S2_DN_VERSION', '1.0.7' );
! defined( 'S2_DN_FILE' ) 			&& define( 'S2_DN_FILE', __FILE__ );
! defined( 'S2_DN_URL' ) 			&& define( 'S2_DN_URL', plugins_url( '/', __FILE__ ) );
! defined( 'S2_DN_ASSETS_URL' ) 	&& define( 'S2_DN_ASSETS_URL', S2_DN_URL . 'assets' );
! defined( 'S2_DN_TEMPLATE_PATH' ) 	&& define( 'S2_DN_TEMPLATE_PATH', S2_DN_DIR . 'templates' );
! defined( 'S2_DN_INC' ) 			&& define( 'S2_DN_INC', S2_DN_DIR . '/includes/' );
! defined( 'S2_DN_TEST_ON' ) 		&& define( 'S2_DN_TEST_ON', ( defined( 'WP_DEBUG' ) && WP_DEBUG ) );
if ( ! defined( 'S2_DN_SUFFIX' ) ) {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	define( 'S2_DN_SUFFIX', $suffix );
}

/* Plugin Framework Check */
if ( ! function_exists( 's2_maybe_plugin_fw_loader' ) && file_exists( S2_DN_DIR . 'plugin-fw/init.php' ) ) {
	require_once S2_DN_DIR . 'plugin-fw/init.php';
}
s2_maybe_plugin_fw_loader( S2_DN_DIR );

/**
 * Load plugin
 *
 * @since 1.0.0
 */
function s2_donation_install() {
	load_plugin_textdomain( 's2-donation', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
	require_once S2_DN_INC . 'class.s2-donation.php';
	S2_Donation();
}
add_action( 'plugins_loaded', 's2_donation_install', 11 );
