<?php
/**
 * Plugin Name: WP MailGun
 * Plugin URI: https://www.nosegraze.com
 * Description: Use MailGun's API for all wp_mail().
 * Version: 1.0
 * Author: Ashley Gibson
 * Author URI: https://www.nosegraze.com
 * License: GPL2
 *
 * @package   wp-mailgun
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_MailGun' ) ) :

	class WP_MailGun {

		/**
		 * WP_MailGun object
		 *
		 * @var WP_MailGun
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * WP_MailGun Instance
		 *
		 * Insures that only one instance of WP_MailGun exists at any one time.
		 *
		 * @access public
		 * @since  1.0.0
		 * @return WP_MailGun
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! self::$instance instanceof WP_MailGun ) {
				self::$instance = new WP_MailGun;
				self::$instance->setup_constants();

				add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

				self::$instance->includes();
			}

			return self::$instance;

		}

		/**
		 * Throw error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @access protected
		 * @since  1.0.0
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-mailgun' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access protected
		 * @since  1.0.0
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-mailgun' ), '1.0.0' );
		}

		/**
		 * Setup plugin constants.
		 *
		 * @access private
		 * @since  1.0.0
		 * @return void
		 */
		private function setup_constants() {

			// Plugin version.
			if ( ! defined( 'WPMG_VERSION' ) ) {
				define( 'WPMG_VERSION', '0.9.0' );
			}

			// Plugin Folder Path.
			if ( ! defined( 'WPMG_PLUGIN_DIR' ) ) {
				define( 'WPMG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'WPMG_PLUGIN_URL' ) ) {
				define( 'WPMG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File.
			if ( ! defined( 'WPMG_PLUGIN_FILE' ) ) {
				define( 'WPMG_PLUGIN_FILE', __FILE__ );
			}

		}

		/**
		 * Include Required Files
		 *
		 * @access private
		 * @since  1.0.0
		 * @return void
		 */
		private function includes() {

			require_once WPMG_PLUGIN_DIR . 'includes/admin/settings-register.php';
			require_once WPMG_PLUGIN_DIR . 'includes/wp-mail.php';

			if ( is_admin() ) {
				require_once WPMG_PLUGIN_DIR . 'includes/admin/admin-pages.php';
				require_once WPMG_PLUGIN_DIR . 'includes/admin/settings-display.php';
				require_once WPMG_PLUGIN_DIR . 'includes/admin/test-email.php';
			}

		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since  1.0.0
		 * @return void
		 */
		public function load_textdomain() {

			$lang_dir = dirname( plugin_basename( WPMG_PLUGIN_FILE ) ) . '/languages/';
			$lang_dir = apply_filters( 'wp-mailgun/languages-directory', $lang_dir );
			load_plugin_textdomain( 'wp-mailgun', false, $lang_dir );

		}

	}

endif;

/**
 * Get WP MailGun up and running.
 *
 * @since 1.0.0
 * @return WP_MailGun
 */
function wp_mailgun() {
	return WP_MailGun::instance();
}

wp_mailgun();