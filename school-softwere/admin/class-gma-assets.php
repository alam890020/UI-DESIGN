<?php
/**
 * GMA_Assets — Enqueue admin CSS and JS.
 *
 * @package GMA_School
 */
defined( 'ABSPATH' ) || exit;

class GMA_Assets {

	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_filter( 'admin_body_class',      array( __CLASS__, 'body_class' ) );
	}

	public static function enqueue( $hook ) {
		if ( false === strpos( $hook, 'gma-' ) && $hook !== 'toplevel_page_gma-school' ) return;

		// ── Google Fonts
		wp_enqueue_style( 'gma-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap', array(), null );

		// ── Bootstrap 5
		wp_enqueue_style(  'gma-bs5',         'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',                array(), '5.3.0' );
		wp_enqueue_script( 'gma-bs5',         'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',           array(), '5.3.0', true );

		// ── CDN Libraries
		wp_enqueue_style(  'gma-datatables',  'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css',                    array( 'gma-bs5' ), '1.13.6' );
		wp_enqueue_style(  'gma-select2',     'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',               array(), '4.1.0' );
		wp_enqueue_style(  'gma-flatpickr',   'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',                          array(), '4.6' );
		wp_enqueue_style(  'gma-sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',                   array(), '11' );
		wp_enqueue_style(  'gma-toastify',    'https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.css',                              array(), '1.12' );
		wp_enqueue_style(  'gma-phosphor',    'https://unpkg.com/phosphor-icons@1.4.2/src/css/icons.css',                              array(), '1.4.2' );

		// ── Plugin CSS
		wp_enqueue_style( 'gma-admin', GMA_PLUGIN_URL . 'assets/css/gma-admin.css', array( 'gma-fonts', 'gma-bs5' ), GMA_VERSION );

		// ── JS Libraries
		wp_enqueue_script( 'gma-chartjs',      'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',                     array(), '4.4.0', true );
		wp_enqueue_script( 'gma-datatables',   'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',                         array( 'jquery' ), '1.13.6', true );
		wp_enqueue_script( 'gma-datatables-bs','https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js',                     array( 'gma-datatables' ), '1.13.6', true );
		wp_enqueue_script( 'gma-select2',      'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',                array( 'jquery' ), '4.1.0', true );
		wp_enqueue_script( 'gma-flatpickr',    'https://cdn.jsdelivr.net/npm/flatpickr',                                                array(), '4.6', true );
		wp_enqueue_script( 'gma-sweetalert2',  'https://cdn.jsdelivr.net/npm/sweetalert2@11',                                           array(), '11', true );
		wp_enqueue_script( 'gma-toastify',     'https://cdn.jsdelivr.net/npm/toastify-js',                                              array(), '1.12', true );

		// ── Plugin JS
		wp_enqueue_script( 'gma-admin', GMA_PLUGIN_URL . 'assets/js/gma-admin.js',
			array( 'jquery', 'gma-select2', 'gma-flatpickr', 'gma-sweetalert2', 'gma-toastify' ),
			GMA_VERSION, true
		);

		wp_localize_script( 'gma-admin', 'GMA', array(
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'gma_nonce' ),
			'plugin_url' => GMA_PLUGIN_URL,
			'school_id'  => GMA_Helper::get_current_school_id(),
			'i18n'       => array(
				'confirm_delete' => __( 'Are you sure you want to delete this?', 'gma-school' ),
				'confirm_yes'    => __( 'Yes, Delete', 'gma-school' ),
				'confirm_cancel' => __( 'Cancel', 'gma-school' ),
				'saved'          => __( 'Saved successfully!', 'gma-school' ),
				'error'          => __( 'Something went wrong. Please try again.', 'gma-school' ),
				'loading'        => __( 'Loading…', 'gma-school' ),
				'no_selection'   => __( 'Please select at least one item.', 'gma-school' ),
			),
		) );
	}

	public static function body_class( $classes ) {
		$screen = get_current_screen();
		if ( $screen && false !== strpos( $screen->id, 'gma-' ) ) {
			$classes .= ' gma-admin-page';
		}
		return $classes;
	}
}
