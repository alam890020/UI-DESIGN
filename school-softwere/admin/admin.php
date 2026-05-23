<?php
/**
 * Admin bootstrap — enqueues, AJAX handlers, and menu init.
 *
 * @package School_Softwere
 */

defined( 'ABSPATH' ) || die();

// Initialise menus
SS_Menu::init();

// Load all admin module classes
$admin_modules = array(
	'admin/inc/manager/SS_School.php',
	'admin/inc/manager/SS_Class.php',
	'admin/inc/manager/SS_Session.php',
	'admin/inc/manager/SS_Category.php',
	'admin/inc/manager/SS_Setting.php',
	'admin/inc/school/SS_Student.php',
	'admin/inc/school/SS_Staff_School.php',
	'admin/inc/school/SS_Exam.php',
	'admin/inc/school/SS_Fee.php',
	'admin/inc/school/SS_Attendance.php',
	'admin/inc/school/SS_Library.php',
	'admin/inc/school/SS_Transport.php',
	'admin/inc/school/SS_Hostel.php',
	'admin/inc/school/SS_Notice.php',
	'admin/inc/school/SS_Event.php',
	'admin/inc/school/SS_Homework.php',
	'admin/inc/school/SS_Leave.php',
	'admin/inc/school/SS_Report.php',
	'admin/inc/school/SS_Ticket.php',
	'admin/inc/school/SS_Lecture.php',
	'admin/inc/school/SS_Wizard.php',
);

foreach ( $admin_modules as $module ) {
	$file = SS_PLUGIN_DIR . $module;
	if ( file_exists( $file ) ) {
		require_once $file;
	}
}

// ── Asset enqueue ─────────────────────────────────────────────────────────────
add_action( 'admin_enqueue_scripts', 'ss_admin_enqueue_assets' );

function ss_admin_enqueue_assets( $hook ) {
	// Only enqueue on plugin pages
	if ( false === strpos( $hook, 'school-softwere' ) ) {
		return;
	}

	// ── Google Fonts
	wp_enqueue_style(
		'ss-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Nunito:wght@600;700;800&display=swap',
		array(),
		null
	);

	// ── CDN Libraries
	wp_enqueue_style(  'ss-datatables',    'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css', array(), '1.13.6' );
	wp_enqueue_style(  'ss-select2',       'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0' );
	wp_enqueue_style(  'ss-flatpickr',     'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.13' );
	wp_enqueue_style(  'ss-sweetalert2',   'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css', array(), '11' );
	wp_enqueue_style(  'ss-toastify',      'https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.css', array(), '1.12' );
	wp_enqueue_style(  'ss-phosphor',      'https://unpkg.com/phosphor-icons@1.4.2/src/css/icons.css', array(), '1.4.2' );

	// ── Plugin CSS
	wp_enqueue_style(
		'ss-admin',
		SS_PLUGIN_URL . 'assets/css/ss-admin.css',
		array( 'ss-google-fonts' ),
		SS_VERSION
	);

	// ── CDN Scripts
	wp_enqueue_script( 'ss-chartjs',       'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', array(), '4.4.0', true );
	wp_enqueue_script( 'ss-datatables',    'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', array( 'jquery' ), '1.13.6', true );
	wp_enqueue_script( 'ss-datatables-bs', 'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js', array( 'ss-datatables' ), '1.13.6', true );
	wp_enqueue_script( 'ss-select2',       'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), '4.1.0', true );
	wp_enqueue_script( 'ss-flatpickr',     'https://cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.13', true );
	wp_enqueue_script( 'ss-sweetalert2',   'https://cdn.jsdelivr.net/npm/sweetalert2@11', array(), '11', true );
	wp_enqueue_script( 'ss-toastify',      'https://cdn.jsdelivr.net/npm/toastify-js', array(), '1.12', true );

	// ── Plugin JS
	wp_enqueue_script(
		'ss-admin',
		SS_PLUGIN_URL . 'assets/js/ss-admin.js',
		array( 'jquery', 'ss-select2', 'ss-flatpickr', 'ss-sweetalert2', 'ss-toastify' ),
		SS_VERSION,
		true
	);

	// Localise script
	wp_localize_script( 'ss-admin', 'SS', array(
		'ajax_url'   => admin_url( 'admin-ajax.php' ),
		'nonce'      => wp_create_nonce( 'ss_nonce' ),
		'plugin_url' => SS_PLUGIN_URL,
		'school_id'  => SS_Helper::get_current_school_id(),
		'i18n'       => array(
			'confirm_delete'  => __( 'Are you sure you want to delete this?', 'school-softwere' ),
			'confirm_yes'     => __( 'Yes, Delete', 'school-softwere' ),
			'confirm_cancel'  => __( 'Cancel', 'school-softwere' ),
			'saved'           => __( 'Saved successfully!', 'school-softwere' ),
			'error'           => __( 'Something went wrong. Please try again.', 'school-softwere' ),
			'loading'         => __( 'Loading…', 'school-softwere' ),
		),
	) );
}

// ── Admin body class for plugin pages ─────────────────────────────────────────
add_filter( 'admin_body_class', function( $classes ) {
	$screen = get_current_screen();
	if ( $screen && false !== strpos( $screen->id, 'school-softwere' ) ) {
		$classes .= ' ss-admin-page';
	}
	return $classes;
} );
