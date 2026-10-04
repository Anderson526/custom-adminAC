<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Núcleo del plugin: carga módulos y centraliza los ajustes.
 */
final class ACA_Plugin {

	const OPTION = 'anderc_aca_settings';

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		new ACA_Login_Customizer();
		new ACA_Menu_Manager();
		new ACA_White_Label();

		if ( is_admin() ) {
			new ACA_Admin_Page();
		}
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'anderc-custom-admin', false, dirname( plugin_basename( ANDERC_ACA_FILE ) ) . '/languages' );
	}

	public static function defaults() {
		return array(
			'login_logo'             => '',
			'login_logo_url'         => home_url( '/' ),
			'login_logo_text'        => get_bloginfo( 'name' ),
			'login_bg_color'         => '#f0f0f1',
			'login_form_bg'          => '#ffffff',
			'login_accent'           => '#4f46e5',
			'login_custom_css'       => '',
			'footer_text'            => '',
			'hide_wp_logo'           => 0,
			'hide_wp_version'        => 0,
			'hide_dashboard_widgets' => 0,
			'hidden_menus'           => array(),
		);
	}

	public static function get_settings() {
		$settings = get_option( self::OPTION, array() );
		return wp_parse_args( is_array( $settings ) ? $settings : array(), self::defaults() );
	}

	public static function update_settings( array $settings ) {
		update_option( self::OPTION, $settings );
	}
}
