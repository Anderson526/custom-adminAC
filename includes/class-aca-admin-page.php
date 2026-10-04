<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Página de ajustes "No-Code" con pestañas: Acceso, Marca blanca y Menús.
 */
class ACA_Admin_Page {

	const SLUG = 'anderc-custom-admin';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'admin_post_anderc_aca_save', array( $this, 'save' ) );
	}

	public function register_menu() {
		add_menu_page(
			__( 'AC Admin', 'anderc-custom-admin' ),
			__( 'AC Admin', 'anderc-custom-admin' ),
			'manage_options',
			self::SLUG,
			array( $this, 'render' ),
			'dashicons-admin-customizer',
			61
		);
	}

	public function assets( $hook ) {
		if ( 'toplevel_page_' . self::SLUG !== $hook ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'anderc-aca-admin', ANDERC_ACA_URL . 'assets/css/admin.css', array(), ANDERC_ACA_VERSION );
		wp_enqueue_script( 'anderc-aca-admin', ANDERC_ACA_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), ANDERC_ACA_VERSION, true );
	}

	public function save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'No tienes permisos suficientes.', 'anderc-custom-admin' ) );
		}
		check_admin_referer( 'anderc_aca_save' );

		$settings = ACA_Plugin::get_settings();

		$settings['login_logo']             = isset( $_POST['login_logo'] ) ? esc_url_raw( wp_unslash( $_POST['login_logo'] ) ) : '';
		$settings['login_logo_url']         = isset( $_POST['login_logo_url'] ) ? esc_url_raw( wp_unslash( $_POST['login_logo_url'] ) ) : '';
		$settings['login_logo_text']        = isset( $_POST['login_logo_text'] ) ? sanitize_text_field( wp_unslash( $_POST['login_logo_text'] ) ) : '';
		$settings['login_bg_color']         = isset( $_POST['login_bg_color'] ) ? (string) sanitize_hex_color( wp_unslash( $_POST['login_bg_color'] ) ) : '';
		$settings['login_form_bg']          = isset( $_POST['login_form_bg'] ) ? (string) sanitize_hex_color( wp_unslash( $_POST['login_form_bg'] ) ) : '';
		$settings['login_accent']           = isset( $_POST['login_accent'] ) ? (string) sanitize_hex_color( wp_unslash( $_POST['login_accent'] ) ) : '';
		$settings['login_custom_css']       = isset( $_POST['login_custom_css'] ) ? wp_strip_all_tags( wp_unslash( $_POST['login_custom_css'] ) ) : '';
		$settings['footer_text']            = isset( $_POST['footer_text'] ) ? wp_kses_post( wp_unslash( $_POST['footer_text'] ) ) : '';
		$settings['hide_wp_logo']           = empty( $_POST['hide_wp_logo'] ) ? 0 : 1;
		$settings['hide_wp_version']        = empty( $_POST['hide_wp_version'] ) ? 0 : 1;
		$settings['hide_dashboard_widgets'] = empty( $_POST['hide_dashboard_widgets'] ) ? 0 : 1;

		$hidden = array();
		if ( isset( $_POST['hidden_menus'] ) && is_array( $_POST['hidden_menus'] ) ) {
			foreach ( wp_unslash( $_POST['hidden_menus'] ) as $role => $slugs ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
				$role = sanitize_key( $role );
				if ( ! is_array( $slugs ) ) {
					continue;
				}
				$hidden[ $role ] = array_values( array_filter( array_map( 'sanitize_text_field', $slugs ) ) );
			}
		}
		$settings['hidden_menus'] = $hidden;

		ACA_Plugin::update_settings( $settings );

		wp_safe_redirect( add_query_arg( array( 'page' => self::SLUG, 'updated' => '1' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$s = ACA_Plugin::get_settings();
		?>
		<div class="wrap anderc-wrap">
			<h1><span class="anderc-badge">AC</span> <?php esc_html_e( 'Custom Admin & White Label', 'anderc-custom-admin' ); ?></h1>

			<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Ajustes guardados correctamente.', 'anderc-custom-admin' ); ?></p></div>
			<?php endif; ?>

			<h2 class="nav-tab-wrapper anderc-tabs">
				<a href="#tab-login" class="nav-tab nav-tab-active"><?php esc_html_e( 'Pantalla de acceso', 'anderc-custom-admin' ); ?></a>
				<a href="#tab-brand" class="nav-tab"><?php esc_html_e( 'Marca blanca', 'anderc-custom-admin' ); ?></a>
				<a href="#tab-menus" class="nav-tab"><?php esc_html_e( 'Menús por rol', 'anderc-custom-admin' ); ?></a>
			</h2>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="anderc_aca_save" />
				<?php wp_nonce_field( 'anderc_aca_save' ); ?>

				<div id="tab-login" class="anderc-tab-panel is-active">
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="login_logo"><?php esc_html_e( 'Logo de acceso', 'anderc-custom-admin' ); ?></label></th>
							<td>
								<input type="url" class="regular-text" id="login_logo" name="login_logo" value="<?php echo esc_attr( $s['login_logo'] ); ?>" />
								<button type="button" class="button anderc-media-upload" data-target="login_logo"><?php esc_html_e( 'Elegir imagen', 'anderc-custom-admin' ); ?></button>
								<p class="description"><?php esc_html_e( 'Sustituye el logo de WordPress en la pantalla de acceso.', 'anderc-custom-admin' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="login_logo_url"><?php esc_html_e( 'Enlace del logo', 'anderc-custom-admin' ); ?></label></th>
							<td><input type="url" class="regular-text" id="login_logo_url" name="login_logo_url" value="<?php echo esc_attr( $s['login_logo_url'] ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="login_logo_text"><?php esc_html_e( 'Texto del logo', 'anderc-custom-admin' ); ?></label></th>
							<td><input type="text" class="regular-text" id="login_logo_text" name="login_logo_text" value="<?php echo esc_attr( $s['login_logo_text'] ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Colores', 'anderc-custom-admin' ); ?></th>
							<td>
								<p><input type="text" class="anderc-color" name="login_bg_color" value="<?php echo esc_attr( $s['login_bg_color'] ); ?>" /> <span class="description"><?php esc_html_e( 'Fondo de la página', 'anderc-custom-admin' ); ?></span></p>
								<p><input type="text" class="anderc-color" name="login_form_bg" value="<?php echo esc_attr( $s['login_form_bg'] ); ?>" /> <span class="description"><?php esc_html_e( 'Fondo del formulario', 'anderc-custom-admin' ); ?></span></p>
								<p><input type="text" class="anderc-color" name="login_accent" value="<?php echo esc_attr( $s['login_accent'] ); ?>" /> <span class="description"><?php esc_html_e( 'Color de acento (botones y enlaces)', 'anderc-custom-admin' ); ?></span></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="login_custom_css"><?php esc_html_e( 'CSS adicional', 'anderc-custom-admin' ); ?></label></th>
							<td><textarea id="login_custom_css" name="login_custom_css" rows="6" class="large-text code"><?php echo esc_textarea( $s['login_custom_css'] ); ?></textarea></td>
						</tr>
					</table>
				</div>

				<div id="tab-brand" class="anderc-tab-panel">
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="footer_text"><?php esc_html_e( 'Texto del pie del admin', 'anderc-custom-admin' ); ?></label></th>
							<td>
								<input type="text" class="large-text" id="footer_text" name="footer_text" value="<?php echo esc_attr( $s['footer_text'] ); ?>" placeholder="<?php esc_attr_e( 'Desarrollado por AC', 'anderc-custom-admin' ); ?>" />
								<p class="description"><?php esc_html_e( 'Sustituye el texto "Gracias por crear con WordPress".', 'anderc-custom-admin' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Ocultar elementos', 'anderc-custom-admin' ); ?></th>
							<td>
								<label><input type="checkbox" name="hide_wp_logo" value="1" <?php checked( $s['hide_wp_logo'] ); ?> /> <?php esc_html_e( 'Logo de WordPress en la barra superior', 'anderc-custom-admin' ); ?></label><br />
								<label><input type="checkbox" name="hide_wp_version" value="1" <?php checked( $s['hide_wp_version'] ); ?> /> <?php esc_html_e( 'Versión de WordPress en el pie', 'anderc-custom-admin' ); ?></label><br />
								<label><input type="checkbox" name="hide_dashboard_widgets" value="1" <?php checked( $s['hide_dashboard_widgets'] ); ?> /> <?php esc_html_e( 'Widgets por defecto del escritorio (noticias, actividad, salud del sitio…)', 'anderc-custom-admin' ); ?></label>
							</td>
						</tr>
					</table>
				</div>

				<div id="tab-menus" class="anderc-tab-panel">
					<p class="description"><?php esc_html_e( 'Marca los menús que quieras ocultar para cada rol. El menú de AC Admin nunca se oculta a los administradores.', 'anderc-custom-admin' ); ?></p>
					<?php $this->render_menu_matrix( $s ); ?>
				</div>

				<?php submit_button( __( 'Guardar cambios', 'anderc-custom-admin' ) ); ?>
			</form>
		</div>
		<?php
	}

	private function render_menu_matrix( array $s ) {
		global $menu;

		$items = array();
		foreach ( (array) $menu as $item ) {
			if ( empty( $item[2] ) || false !== strpos( $item[4] ?? '', 'wp-menu-separator' ) ) {
				continue;
			}
			$label = trim( wp_strip_all_tags( preg_replace( '/<span.*<\/span>/s', '', (string) $item[0] ) ) );
			if ( '' === $label ) {
				continue;
			}
			$items[ $item[2] ] = $label;
		}

		$roles  = wp_roles()->roles;
		$hidden = isset( $s['hidden_menus'] ) && is_array( $s['hidden_menus'] ) ? $s['hidden_menus'] : array();
		?>
		<table class="widefat striped anderc-menu-matrix">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Menú', 'anderc-custom-admin' ); ?></th>
					<?php foreach ( $roles as $role_key => $role ) : ?>
						<th><?php echo esc_html( translate_user_role( $role['name'] ) ); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $items as $slug => $label ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $label ); ?></strong><br /><code><?php echo esc_html( $slug ); ?></code></td>
						<?php foreach ( $roles as $role_key => $role ) : ?>
							<td>
								<input type="checkbox"
									name="hidden_menus[<?php echo esc_attr( $role_key ); ?>][]"
									value="<?php echo esc_attr( $slug ); ?>"
									<?php checked( ! empty( $hidden[ $role_key ] ) && in_array( $slug, (array) $hidden[ $role_key ], true ) ); ?> />
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}
