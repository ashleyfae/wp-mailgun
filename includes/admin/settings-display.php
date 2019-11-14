<?php
/**
 * Display Settings
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
 * Render Options Page
 *
 * @since 1.0.0
 * @return void
 */
function wp_mailgun_display_settings() {

	do_action( 'wp-mailgun/settings/display/before' );

	$settings_tabs = wp_mailgun_get_settings_tabs();
	$settings_tabs = empty( $settings_tabs ) ? array() : $settings_tabs;
	$active_tab    = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $settings_tabs ) ? $_GET['tab'] : 'general';

	?>
	<div id="wp-mailgun-settings-wrap" class="wrap">
		<h1 class="nav-tab-wrapper">
			<?php
			foreach ( $settings_tabs as $tab_id => $tab_name ) {
				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab'              => $tab_id
				) );

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';
				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
				echo esc_html( $tab_name );
				echo '</a>';
			}
			?>
		</h1>

		<div id="tab_container">

			<form method="post" action="options.php">
				<table class="form-table">
					<?php
					settings_fields( 'wp_mg_settings' );
					do_action( 'wp-mailgun/settings/tab/top', $active_tab );
					do_settings_sections( 'wp_mg_settings_' . $active_tab );
					do_action( 'wp-mailgun/settings/tab/bottom/' . $active_tab );
					?>
				</table>

				<?php
				$text = ( $active_tab == 'test' ) ? __( 'Send Test Email', 'wp-mailgun' ) : __( 'Save Changes', 'wp-mailgun' );
				submit_button( $text );
				?>
			</form>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php

}