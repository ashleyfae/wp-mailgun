<?php
/**
 * admin-pages.php
 *
 * @package   wp-mailgun
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create Admin Page
 *
 * @since 1.0.0
 * @return void
 */
function wp_mailgun_admin_page() {
	add_options_page( __( 'WP MailGun Settings', 'wp-mailgun' ), __( 'WP MailGun', 'wp-mailgun' ), 'manage_options', 'wp-mailgun', 'wp_mailgun_display_settings' );
}

add_action( 'admin_menu', 'wp_mailgun_admin_page' );

/**
 * WP MailGun Settings Link
 *
 * Add a link to the settings page in the plugin list.
 *
 * @param array $links
 *
 * @since 1.0.0
 * @return array
 */
function wp_mailgun_settings_link( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=wp-mailgun' ) ) . '">' . __( 'Settings', 'wp-mailgun' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( WPMG_PLUGIN_FILE ), 'wp_mailgun_settings_link' );