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
 * @class   S2_Assets
 * @package S2 Plugin
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( !class_exists( 'S2_Assets' ) ) {

    class S2_Assets {

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

            add_action( 'admin_enqueue_scripts', [ $this, 'register_styles_and_scripts' ] );

        }

        /**
         * Register styles and scripts
         */
        public function register_styles_and_scripts() {
    
            wp_enqueue_style( 's2-plugin-admin', S2_CORE_PLUGIN_URL . '/assets/css/admin.css', [], S2_CORE_PLUGIN_VERSION );
    
        }

    }

}

/**
 * Unique access to instance of S2_Assets class
 */
S2_Assets::get_instance();
