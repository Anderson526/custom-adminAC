<?php
/*
Plugin Name:  Custom Admin & White Label - toolkitAC
Plugin URI: https://anderson526.github.io/portfolio-profesional/
Description: Personaliza la pantalla de acceso, aplica marca blanca al escritorio y oculta menús del administrador según el rol. Parte de la suite AnderC Essential.
Version: 1.0.0
Author: Anderson Chila
Author URI: https://anderson526.github.io/portfolio-profesional/
Text Domain: anderc-custom-admin
Domain Path: /languages
Requires at least: 6.0
Requires PHP: 7.4
License: GPL-2.0-or-later
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANDERC_ACA_VERSION', '1.0.0' );
define( 'ANDERC_ACA_FILE', __FILE__ );
define( 'ANDERC_ACA_DIR', plugin_dir_path( __FILE__ ) );
define( 'ANDERC_ACA_URL', plugin_dir_url( __FILE__ ) );

// Autoloader estilo PSR-4 (compatible con Composer si se añade vendor/).
spl_autoload_register(
	function ( $class ) {
		if ( 0 !== strpos( $class, 'ACA_' ) ) {
			return;
		}
		$file = ANDERC_ACA_DIR . 'includes/class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);

if ( file_exists( ANDERC_ACA_DIR . 'vendor/autoload.php' ) ) {
	require_once ANDERC_ACA_DIR . 'vendor/autoload.php';
}

ACA_Plugin::instance();
