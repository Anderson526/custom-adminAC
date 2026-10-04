<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Marca blanca: pie del administrador, logo de WP en la barra y widgets del escritorio.
 */
class ACA_White_Label {

	public function __construct() {
		add_filter( 'admin_footer_text', array( $this, 'footer_text' ) );
		add_filter( 'update_footer', array( $this, 'footer_version' ), 999 );
		add_action( 'admin_bar_menu', array( $this, 'admin_bar' ), 999 );
		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_widgets' ), 999 );
	}

	public function footer_text( $text ) {
		$s = ACA_Plugin::get_settings();
		if ( ! empty( $s['footer_text'] ) ) {
			return wp_kses_post( $s['footer_text'] );
		}
		return $text;
	}

	public function footer_version( $version ) {
		$s = ACA_Plugin::get_settings();
		return ! empty( $s['hide_wp_version'] ) ? '' : $version;
	}

	public function admin_bar( $wp_admin_bar ) {
		$s = ACA_Plugin::get_settings();
		if ( ! empty( $s['hide_wp_logo'] ) ) {
			$wp_admin_bar->remove_node( 'wp-logo' );
		}
	}

	public function dashboard_widgets() {
		$s = ACA_Plugin::get_settings();
		if ( empty( $s['hide_dashboard_widgets'] ) ) {
			return;
		}
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
		remove_action( 'welcome_panel', 'wp_welcome_panel' );
	}
}
