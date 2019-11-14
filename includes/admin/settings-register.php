<?php
/**
 * Register Settings
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
 * Get an Option
 *
 * Looks to see if the specified setting exists, returns the default if not.
 *
 * @param string $key     Key to retrieve
 * @param mixed  $default Default option
 *
 * @uses  wp_mailgun_get_settings()
 *
 * @since 1.0.0
 * @return mixed
 */
function wp_mailgun_get_option( $key = '', $default = false ) {
	$settings = wp_mailgun_get_settings();

	$value = ! empty( $settings[$key] ) ? $settings[$key] : $default;
	$value = apply_filters( 'wp-mailgun/options/get', $value, $key, $default );

	return apply_filters( 'wp-mailgun/options/get/' . $key, $value, $key, $default );
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 1.0.0
 * @return array WP MailGun settings
 */
function wp_mailgun_get_settings() {
	$settings = get_option( 'wp_mg_settings', array() );

	if ( ! is_array( $settings ) ) {
		$settings = array();
	}

	return apply_filters( 'wp-mailgun/get-settings', $settings );
}

/**
 * Register Settings
 *
 * Adds all setting sections and fields.
 *
 * @since 1.0.0
 * @return void
 */
function wp_mailgun_register_settings() {

	if ( false == get_option( 'wp_mg_settings' ) ) {
		add_option( 'wp_mg_settings' );
	}

	foreach ( wp_mailgun_get_registered_settings() as $tab => $settings ) {
		add_settings_section(
			'wp_mg_settings_' . $tab,
			__return_null(),
			'__return_false',
			'wp_mg_settings_' . $tab
		);

		foreach ( $settings as $option ) {
			$name = isset( $option['name'] ) ? $option['name'] : '';

			add_settings_field(
				'wp_mg_settings[' . $option['id'] . ']',
				$name,
				function_exists( 'wp_mailgun_' . $option['type'] . '_callback' ) ? 'wp_mailgun_' . $option['type'] . '_callback' : 'wp_mailgun_missing_callback',
				'wp_mg_settings_' . $tab,
				'wp_mg_settings_' . $tab,
				array(
					'id'          => isset( $option['id'] ) ? $option['id'] : null,
					'desc'        => ! empty( $option['desc'] ) ? $option['desc'] : '',
					'name'        => isset( $option['name'] ) ? $option['name'] : null,
					'size'        => isset( $option['size'] ) ? $option['size'] : null,
					'options'     => isset( $option['options'] ) ? $option['options'] : '',
					'std'         => isset( $option['std'] ) ? $option['std'] : '',
					'placeholder' => isset( $option['placeholder'] ) ? $option['placeholder'] : null
				)
			);
		}
	}

	// Creates our settings in the options table
	register_setting( 'wp_mg_settings', 'wp_mg_settings', 'wp_mg_settings_sanitize' );

}

add_action( 'admin_init', 'wp_mailgun_register_settings' );

/**
 * Registered Settings
 *
 * Sets and returns the array of all plugin settings.
 * Developers can use the following filters to add their own settings or
 * modify existing ones:
 *
 *  + wp-mailgun/settings/{key} - Where {key} is a specific tab. Used to modify a single tab.
 *  + wp-mailgun/settings/registered-settings - Includes the entire array of all settings.
 *
 * @since 1.0.0
 * @return array
 */
function wp_mailgun_get_registered_settings() {

	$sitename = strtolower( $_SERVER['SERVER_NAME'] );
	if ( substr( $sitename, 0, 4 ) == 'www.' ) {
		$sitename = substr( $sitename, 4 );
	}

	$settings = array(
		'general' => apply_filters( 'wp-mailgun/settings/general', array(
			'from_name'  => array(
				'id'   => 'from_name',
				'name' => __( 'From Name', 'wp-mailgun' ),
				'desc' => '',
				'type' => 'text',
			),
			'from_email' => array(
				'id'   => 'from_email',
				'name' => __( 'From Email', 'wp-mailgun' ),
				'desc' => '',
				'type' => 'text',
			),
			'domain'     => array(
				'id'   => 'domain',
				'name' => __( 'Domain Name', 'wp-mailgun' ),
				'desc' => '',
				'type' => 'text',
				'std'  => ''
			),
			'api_key'    => array(
				'id'      => 'api_key',
				'name'    => __( 'API Key', 'wp-mailgun' ),
				'desc'    => '',
				'type'    => 'text',
				'std'     => '',
				'options' => array(
					'type' => 'password'
				)
			),
			'tag'        => array(
				'id'   => 'tag',
				'name' => __( 'Tag', 'wp-mailgun' ),
				'desc' => __( 'This tag will exist on every outbound message. Separate multiple tags with a comma.', 'wp-mailgun' ),
				'type' => 'text',
				'std'  => $sitename
			),
		) ),
		'test'    => apply_filters( 'wp-mailgun/settings/test', array(
			'test_email' => array(
				'id'   => 'test_email',
				'name' => __( 'Send Test Email', 'wp-mailgun' ),
				'type' => 'test_email'
			)
		) )
	);

	return apply_filters( 'wp-mailgun/settings/registered-settings', $settings );

}

/**
 * Retrieve settings tabs
 *
 * @since 1.0.0
 * @return array $tabs
 */
function wp_mailgun_get_settings_tabs() {

	$tabs = array(
		'general' => __( 'General', 'wp-mailgun' ),
		'test'    => __( 'Test Email', 'wp-mailgun' )
	);

	return apply_filters( 'wp-mailgun/settings/tabs', $tabs );

}

/**
 * Sanitizes a string key for WP MailGun Settings
 *
 * Keys are used as internal identifiers. Alphanumeric characters, dashes, underscores, stops, colons and slashes are
 * allowed
 *
 * @param string $key String key
 *
 * @since 1.0.0
 * @return string Sanitized key
 */
function wp_mailgun_sanitize_key( $key ) {
	$raw_key = $key;
	$key     = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );

	return apply_filters( 'wp-mailgun/sanitize-key', $key, $raw_key );
}

/**
 * Sanitize Settings
 *
 * Adds a settings error for the updated message.
 *
 * @param array $input The value inputted in the field
 *
 * @since 1.0.0
 * @return array New, sanitized settings.
 */
function wp_mg_settings_sanitize( $input = array() ) {

	$saved_settings = wp_mailgun_get_settings();

	if ( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}

	parse_str( $_POST['_wp_http_referer'], $referrer );

	$settings = wp_mailgun_get_registered_settings();
	$tab      = ( isset( $referrer['tab'] ) ) ? $referrer['tab'] : 'general';

	$input = $input ? $input : array();
	$input = apply_filters( 'wp-mailgun/settings/sanitize/' . $tab, $input );

	// Loop through each setting being saved and pass it through a sanitization filter
	foreach ( $input as $key => $value ) {
		// Get the setting type (checkbox, select, etc)
		$type = isset( $settings[$tab][$key]['type'] ) ? $settings[$tab][$key]['type'] : false;
		if ( $type ) {
			// Field type specific filter
			$input[$key] = apply_filters( 'wp-mailgun/settings/sanitize/' . $type, $value, $key );
		}
		// General filter
		$input[$key] = apply_filters( 'wp-mailgun/settings/sanitize', $input[$key], $key );
	}

	// Merge our new settings with the existing
	$output = array_merge( $saved_settings, $input );

	if ( ! empty( $input ) ) {
		add_settings_error( 'wp-mailgun-notices', '', __( 'Settings updated.', 'wp-mailgun' ), 'updated' );
	}

	return $output;

}

/**
 * Sanitize Text Field
 *
 * @param string $input
 *
 * @since 1.0.0
 * @return string
 */
function wp_mailgun_settings_sanitize_text_field( $input ) {
	return sanitize_text_field( $input );
}

add_filter( 'wp-mailgun/settings/sanitize/text', 'wp_mailgun_settings_sanitize_text_field' );

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @param array $args Arguments passed by the setting
 *
 * @since 1.0.0
 * @return void
 */
function wp_mailgun_missing_callback( $args ) {
	printf(
		__( 'The callback function used for the %s setting is missing.', 'wp-mailgun' ),
		'<strong>' . $args['id'] . '</strong>'
	);
}

/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @param array $args Arguments passed by the setting
 *
 * @since 1.0.0
 * @return void
 */
function wp_mailgun_text_callback( $args ) {
	$saved_options = wp_mailgun_get_settings();

	if ( isset( $saved_options[$args['id']] ) ) {
		$value = $saved_options[$args['id']];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$args['readonly'] = true;
		$value            = isset( $args['std'] ) ? $args['std'] : '';
		$name             = '';
	} else {
		$name = 'name="wp_mg_settings[' . esc_attr( $args['id'] ) . ']"';
	}

	$type        = ( array_key_exists( 'options', $args ) && is_array( $args['options'] ) && array_key_exists( 'type', $args['options'] ) ) ? $args['options']['type'] : 'text';
	$readonly    = ( array_key_exists( 'readonly', $args ) && $args['readonly'] === true ) ? ' readonly="readonly"' : '';
	$size        = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$placeholder = ( $type == 'url' ) ? 'http://' : '';
	?>
	<input type="<?php echo esc_attr( $type ); ?>" class="<?php echo sanitize_html_class( $size ); ?>-text" id="wp_mg_settings[<?php echo wp_mailgun_sanitize_key( $args['id'] ); ?>]" <?php echo $name; ?> value="<?php echo esc_attr( stripslashes( $value ) ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"<?php echo $readonly; ?>>
	<p class="description"><?php echo wp_kses_post( $args['desc'] ); ?></p>
	<?php
}

function wp_mailgun_test_email_callback( $args ) {
	?>
	<p>
		<label for="wp_mg_to"><?php _e( 'Recipient(s)', 'wp-mailgun' ); ?></label> <br>
		<input type="email" class="regular-text" id="wp_mg_to" name="wp_mg_to" placeholder="<?php esc_attr_e( 'jane@janedoe.com', 'wp-mailgun' ); ?>">
	</p>

	<p>
		<label for="wp_mg_subject"><?php _e( 'Subject', 'wp-mailgun' ); ?></label> <br>
		<input type="text" class="regular-text" id="wp_mg_subject" name="wp_mg_subject" value="<?php esc_attr_e( 'WP MailGun Test Email', 'wp-mailgun' ); ?>">
	</p>

	<p>
		<label for="wp_mg_message"><?php _e( 'Message', 'wp-mailgun' ); ?></label> <br>
		<textarea class="large-text" id="wp_mg_message" name="wp_mg_message" rows="10" cols="50"></textarea>
	</p>

	<p>
		<label for="wp_mg_email_type"><?php _e( 'Email Type', 'wp-mailgun' ); ?></label> <br>
		<select id="wp_mg_email_type" name="wp_mg_email_type">
			<option value="text" selected><?php _e( 'Plain text', 'wp-mailgun' ); ?></option>
			<option value="html"><?php _e( 'HTML', 'wp-mailgun' ); ?></option>
		</select>
	</p>
	<?php
}