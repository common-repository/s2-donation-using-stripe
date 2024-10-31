<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Implements features of S2 Donation Log
 *
 * @class   S2_Donation_Logger
 * @package S2 Donation
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
class S2_Donation_Logger {

	const LOG_FILENAME = WP_CONTENT_DIR . '/debug.log';

	/**
	 * Log message
	 *
	 * @since 1.0.0
	 */
	public static function log( $message ) {

		$settings = get_option( 's2dn_settings' );

		if ( empty( $settings ) || empty( $settings['enable_log'] ) || isset( $settings['enable_log'] ) && 'yes' !== $settings['enable_log'] ) {
			return;
		}

		$log_entry  = "\n" . '====S2 Donation Version: ' . S2_DN_VERSION . '====' . "\n";
		$log_entry .= '====Start Log====' . "\n" . $message . "\n" . '====End Log====' . "\n\n";

		error_log( $log_entry, 3, self::LOG_FILENAME );

	}

}
