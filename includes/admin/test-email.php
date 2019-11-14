<?php
/**
 * Send Test Email
 *
 * @package   wp-mailgun
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Send Test Email
 *
 * @since 1.0.0
 * @return void
 */
function wp_mailgun_send_test_email() {
	if ( ! isset( $_POST['wp_mg_message'] ) ) {
		return;
	}

	$to      = isset( $_POST['wp_mg_to'] ) ? wp_strip_all_tags( $_POST['wp_mg_to'] ) : false;
	$subject = isset( $_POST['wp_mg_subject'] ) ? wp_strip_all_tags( stripslashes( $_POST['wp_mg_subject'] ) ) : __( 'WP MailGun Test Email', 'wp-mailgun' );
	$message = wp_kses_post( stripslashes( $_POST['wp_mg_message'] ) );
	$type    = ( isset( $_POST['wp_mg_email_type'] ) && $_POST['wp_mg_email_type'] == 'html' ) ? 'html' : 'text';

	if ( empty( $to ) ) {
		add_settings_error( 'wp-mailgun-notices', '', __( 'Missing recipient email address.', 'wp-mailgun' ) );

		return;
	}

	if ( empty( $subject ) ) {
		add_settings_error( 'wp-mailgun-notices', '', __( 'Missing subject.', 'wp-mailgun' ) );

		return;
	}

	$headers = ( $type == 'html' ) ? array( 'Content-Type: text/html; charset=UTF-8' ) : array();

	$result = wp_mail( $to, $subject, $message, $headers );

	if ( $result ) {
		add_settings_error( 'wp-mailgun-notices', '', sprintf( __( 'Email sent successfully to: %s', 'wp-mailgun' ), esc_html( $to ) ), 'updated' );
	} else {
		add_settings_error( 'wp-mailgun-notices', '', __( 'Email failed to send.', 'wp-mailgun' ) );
	}
}

add_action( 'admin_init', 'wp_mailgun_send_test_email' );