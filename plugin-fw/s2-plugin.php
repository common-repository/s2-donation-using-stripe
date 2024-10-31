<?php
/**
 * This file belongs to the S2 Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main S2 Plugins class
 *
 * @class   S2_Plugin
 * @package S2 Plugin
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Plugin' ) ) {

	class S2_Plugin {

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Plugin
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \S2_Plugin
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

			$this->define_constants();

			include_once 's2-functions.php';
			include_once 'lib/abstracts/abstract.s2-settings-api.php';
			include_once 'lib/s2-assets.php';

		}

		/**
		 * Define Constants.
		 *
		 * @since  1.0.0
		 */
		private function define_constants() {

    		! defined( 'S2_CORE_PLUGIN_VERSION' )          && define( 'S2_CORE_PLUGIN_VERSION', '1.0.0' );
    		! defined( 'S2_CORE_PLUGIN_PATH' )             && define( 'S2_CORE_PLUGIN_PATH', dirname(__FILE__) );
			! defined( 'S2_CORE_PLUGIN_URL' )              && define( 'S2_CORE_PLUGIN_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );

		}

	}

}

/**
 * Unique access to instance of S2_Plugin class
 */
S2_Plugin::get_instance();
