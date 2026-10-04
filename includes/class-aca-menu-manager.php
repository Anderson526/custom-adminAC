<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Oculta elementos del menú de administración según el rol del usuario actual.
 */
class ACA_Menu_Manager {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'hide_menus' ), 999 );
	}

	public function hide_menus() {
		$settings = ACA_Plugin::get_settings();
		$hidden   = isset( $settings['hidden_menus'] ) && is_array( $settings['hidden_menus'] ) ? $settings['hidden_menus'] : array();

		if ( empty( $hidden ) ) {
			return;
		}

		$user = wp_get_current_user();
		if ( ! $user || ! $user->exists() ) {
			return;
		}

		$slugs = array();
		foreach ( (array) $user->roles as $role ) {
			if ( ! empty( $hidden[ $role ] ) && is_array( $hidden[ $role ] ) ) {
				$slugs = array_merge( $slugs, $hidden[ $role ] );
			}
		}

		foreach ( array_unique( $slugs ) as $slug ) {
			// Nunca ocultar los ajustes del propio plugin: evita perder el acceso a la configuración.
			if ( 'anderc-custom-admin' === $slug && current_user_can( 'manage_options' ) ) {
				continue;
			}
			remove_menu_page( $slug );
		}
	}
}
