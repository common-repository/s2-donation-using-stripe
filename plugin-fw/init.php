<?php
/**
 * Framework Name: S2 Plugin Framework
 * Version: 1.0.0
 * Author: Shuban Studio <shuban.studio@gmail.com>
 * Text Domain: s2-plugin
 * Domain Path: /languages/
 */

/**
 * This file belongs to the S2 Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

// Exit if accessed directly
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists ( 's2_maybe_plugin_fw_loader' ) ) {
    /**
     * s2_maybe_plugin_fw_loader
     *
     * @since 1.0.0
     */
    function s2_maybe_plugin_fw_loader ( $plugin_path ) {

        include_once $plugin_path . 'plugin-fw/s2-plugin.php';

    }
}
