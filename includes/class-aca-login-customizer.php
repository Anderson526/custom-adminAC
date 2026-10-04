<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Personaliza la pantalla de acceso (wp-login.php) con CSS generado dinámicamente.
 */
class ACA_Login_Customizer {

	public function __construct() {
		add_action( 'login_enqueue_scripts', array( $this, 'print_styles' ) );
		add_filter( 'login_headerurl', array( $this, 'logo_url' ) );
		add_filter( 'login_headertext', array( $this, 'logo_text' ) );
	}

	public function print_styles() {
		$s   = ACA_Plugin::get_settings();
		$css = '';

		$bg      = sanitize_hex_color( $s['login_bg_color'] );
		$form_bg = sanitize_hex_color( $s['login_form_bg'] );
		$accent  = sanitize_hex_color( $s['login_accent'] );

		$css .= sprintf( 'body.login{background-color:%s;}', $bg ? $bg : '#f0f0f1' );
		$css .= sprintf( '.login form{background:%s;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.08);border:none;}', $form_bg ? $form_bg : '#ffffff' );
		$css .= '.login .message,.login .success,.login #login_error{border-radius:6px;}';

		if ( $accent ) {
			$css .= sprintf(
				'.wp-core-ui .button-primary{background:%1$s;border-color:%1$s;}' .
				'.wp-core-ui .button-primary:hover,.wp-core-ui .button-primary:focus{background:%1$s;border-color:%1$s;filter:brightness(1.12);}' .
				'.login #backtoblog a:hover,.login #nav a:hover,.login h1 a:hover{color:%1$s;}' .
				'.login input:focus{border-color:%1$s;box-shadow:0 0 0 1px %1$s;}',
				$accent
			);
		}

		if ( ! empty( $s['login_logo'] ) ) {
			$css .= sprintf(
				'.login h1 a{background-image:url(%s);background-size:contain;background-position:center;width:220px;height:90px;}',
				esc_url( $s['login_logo'] )
			);
		}

		$css .= (string) $s['login_custom_css'];

		echo '<style id="anderc-aca-login">' . wp_strip_all_tags( $css ) . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	public function logo_url() {
		$s = ACA_Plugin::get_settings();
		return ! empty( $s['login_logo_url'] ) ? esc_url( $s['login_logo_url'] ) : home_url( '/' );
	}

	public function logo_text( $text ) {
		$s = ACA_Plugin::get_settings();
		return ! empty( $s['login_logo_text'] ) ? $s['login_logo_text'] : $text;
	}
}
