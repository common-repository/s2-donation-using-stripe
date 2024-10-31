<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Implements admin plugin panel features of S2 Donation
 *
 * @class   S2_Donation_Plugin_Panel
 * @package S2 Donation
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Donation_Plugin_Panel' ) ) {

	class S2_Donation_Plugin_Panel {

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Donation_Plugin_Panel
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since  1.0.0
		 * @return \S2_Donation_Plugin_Panel
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
		 * @param array $args
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			$this->create_menu_items();

			// Add action links.
			add_filter( 'plugin_action_links_' . plugin_basename( S2_DN_DIR . '/' . basename( S2_DN_FILE ) ), [ $this, 'action_links' ] );

		}

		/**
		 * Create Menu Items
		 *
		 * Print admin menu items
		 *
		 * @since  1.0.0
		 */
		private function create_menu_items() {

			// Add a panel or menu pages
			add_action( 'admin_menu', [ $this, 'register_panel' ], 5 );

		}

		/**
		 * Add a panel or menu pages
		 *
		 * @since  1.0.0
		 */
		public function register_panel() {

			global $submenu;

			$args = [
				'page_title'    => 'Home',
				'menu_title'    => 'S2 Plugins',
				'capability'    => 'manage_options',
				'menu_slug'     => 's2-admin',
				'function_name' => '',
				'icon_url'      => 'dashicons-heart',
				'position'      => null,
			];
			if( empty( $submenu['s2-admin'] ) ) $this->add_menu_page( $args );

			$args = [
				'parent_slug'   => 's2-admin',
				'page_title'    => 'Donation',
				'menu_title'    => 'Donation',
				'capability'    => 'manage_options',
				'menu_slug'     => 's2-donation',
				'function_name' => 'show_donation',
				'position'      => null,
			];
			$this->add_submenu_page( $args );

			unset( $submenu['s2-admin'][0] );

		}

		/**
		 * Add Menu page link
		 *
		 * @param array $args
		 *
		 * @since  1.0.0
		 */
		public function add_menu_page( $args ) {

			add_menu_page( $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], [ $this, $args['function_name'] ], $args['icon_url'], $args['position'] );

		}

		/**
		 * Add Menu page link
		 *
		 * @param array $args
		 *
		 * @since  1.0.0
		 */
		public function add_submenu_page( $args ) {

			add_submenu_page( $args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], [ $this, $args['function_name'] ], $args['position'] );

		}

		/**
		 * Show donation admin page
		 *
		 * @since  1.0.0
		 */
		public function show_donation() {

			$current_tab = 'report';
            if( ! empty( $_GET['tab'] ) ) {
            	$current_tab = sanitize_text_field( $_GET['tab'] );
        	}

        	$this->print_tabs_nav( $current_tab );

        	$current_section = '';
            if( ! empty( $_GET['section'] ) ) {
            	$current_section = sanitize_text_field( $_GET['section'] );
        	}

			$this->print_tabs_section_nav( $current_tab, $current_section );

			if( $current_tab == 'report' ) $this->show_donation_report_page();
			elseif( $current_tab == 'settings' ) $this->show_donation_setting_page();
			elseif( $current_tab == 'mail' ) $this->show_donation_mail_page();

		}

		/**
         * Print the tabs navigation
         *
         * @param array $args
         *
         * @since  1.0.0
         */
        public function print_tabs_nav( $current_tab ) {

        	$page = 's2-donation';
        	$tabs = '';

            $args = [
				'report'   => __( 'Report', 's2-donation' ),
				'settings' => __( 'Settings', 's2-donation' ),
				'mail' 	   => __( 'Mail', 's2-donation' ),
			];

            foreach ( $args as $tab => $tab_value ) {

                $active_class = ( $current_tab == $tab ) ? ' nav-tab-active' : '';

                $url = $this->get_nav_url( $page, $tab );

                $tabs .= '<a class="nav-tab' . $active_class . '" href="' . $url . '">' . $tab_value . '</a>';

            }

            echo '<h2 class="nav-tab-wrapper">' . $tabs .'</h2>';

        }

        /**
         * Print the tabs section navigation
         *
         * @param array $args
         *
         * @since  1.0.1
         */
        public function print_tabs_section_nav( $current_tab, $current_section = '' ) {

            $page 	  = 's2-donation';
        	$sections = [];

            $args = [
            			'report' => [
            							'live_data' => 'Live Data',
            							'test_data' => 'Test Data',
        							],
        			];

            foreach ( $args as $tab => $tab_value ) {

            	if( $tab != $current_tab ) continue;

            	if( empty( $current_section ) ) {
            		$current_section = array_keys( $tab_value );
					$current_section = $current_section[0];
				}

				foreach ( $tab_value as $section => $section_value ) {

	                $active_class = ( $section == $current_section ) ? 'current' : '';

	                $url = $this->get_nav_url( $page, $tab, $section );

	                $sections[] = '<li><a class="' . $active_class . '" href="' . $url . '">' . $section_value . '</a></li>';

            	}

            }

            echo '<ul class="subsubsub">' . implode( ' | ', $sections ) .'</ul>';

        }

        /**
         * Get tab nav url
         *
         * @param string $tab
         *
         * @since  1.0.0
         */
        public function get_nav_url( $page, $tab, $section = '' ) {

            $url = "?page={$page}&tab={$tab}";
            $url .= $section ? "&section={$section}" : "";
            $url = admin_url( "admin.php{$url}" );

            return $url;

        }

        /**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @since  1.0.0
		 *
		 * @return mixed | array
		 * @use    plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links = is_array( $links ) ? $links : [];

			$links[] = sprintf( '<a href="%s">%s</a>', admin_url( "admin.php?page=s2-donation&tab=settings" ), _x( 'Settings', 'Action links',  's2-donation' ) );

			return $links;

		}

		/**
		 * Show donation report admin page
		 *
		 * @since  1.0.0
		 */
		public function show_donation_report_page() {

			$report_list = new S2_Donation_Report_List();

			ob_start();
			include_once S2_DN_TEMPLATE_PATH . '/admin/report-page.php';
			$html = ob_get_clean();

			echo $html;

		}

		/**
		 * Show donation setting admin page
		 *
		 * @since  1.0.0
		 */
		public function show_donation_setting_page() {

			$plugin_setting = S2_Donation_Plugin_Setting();

			ob_start();
			include_once S2_DN_TEMPLATE_PATH . '/admin/settings-page.php';
			$html = ob_get_clean();

			echo $html;

		}

		/**
		 * Show donation mail admin page
		 *
		 * @since  1.0.3
		 */
		public function show_donation_mail_page() {

			$mail_setting = S2_Donation_Mail_Setting();

			ob_start();
			include_once S2_DN_TEMPLATE_PATH . '/admin/mail-page.php';
			$html = ob_get_clean();

			echo $html;

		}

	}

}

/**
 * Unique access to instance of S2_Donation_Plugin_Panel class
 *
 * @return \S2_Donation_Plugin_Panel
 */
if ( is_admin() ) {
	S2_Donation_Plugin_Panel::get_instance();
}
