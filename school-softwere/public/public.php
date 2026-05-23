<?php
/**
 * Frontend (public) bootstrap — shortcodes, REST API, widgets.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

require_once SS_PLUGIN_DIR . 'public/inc/SS_Login.php';
require_once SS_PLUGIN_DIR . 'public/inc/SS_Noticeboard.php';
require_once SS_PLUGIN_DIR . 'public/inc/SS_Student_Portal.php';

// Enqueue frontend CSS/JS
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'ss-public', SS_PLUGIN_URL . 'assets/css/ss-public.css', array(), SS_VERSION );
	wp_enqueue_script( 'ss-public', SS_PLUGIN_URL . 'assets/js/ss-public.js', array( 'jquery' ), SS_VERSION, true );
	wp_localize_script( 'ss-public', 'SS_Public', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'ss_nonce' ),
	) );
} );

// Register shortcodes
add_shortcode( 'ss_login',            array( 'SS_Login', 'render' ) );
add_shortcode( 'ss_noticeboard',      array( 'SS_Noticeboard', 'render' ) );
add_shortcode( 'ss_events',           array( 'SS_Noticeboard', 'render_events' ) );
add_shortcode( 'ss_student_dashboard',array( 'SS_Student_Portal', 'render_dashboard' ) );
add_shortcode( 'ss_fee_status',       array( 'SS_Student_Portal', 'render_fee_status' ) );
add_shortcode( 'ss_results',          array( 'SS_Student_Portal', 'render_results' ) );
add_shortcode( 'ss_attendance',       array( 'SS_Student_Portal', 'render_attendance' ) );
add_shortcode( 'ss_homework',         array( 'SS_Student_Portal', 'render_homework' ) );

// REST API
add_action( 'rest_api_init', function () {
	$ns = 'school-softwere/v1';

	register_rest_route( $ns, '/notices', array(
		'methods'             => 'GET',
		'callback'            => function ( $req ) {
			global $wpdb;
			$school_id = (int) $req->get_param( 'school_id' );
			$notices   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_notices WHERE school_id=%d ORDER BY date DESC LIMIT 10", $school_id ) );
			return rest_ensure_response( $notices );
		},
		'permission_callback' => '__return_true',
	) );

	register_rest_route( $ns, '/events', array(
		'methods'             => 'GET',
		'callback'            => function ( $req ) {
			global $wpdb;
			$school_id = (int) $req->get_param( 'school_id' );
			$events    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_events WHERE school_id=%d AND start_date >= %s ORDER BY start_date ASC LIMIT 10", $school_id, current_time( 'Y-m-d' ) ) );
			return rest_ensure_response( $events );
		},
		'permission_callback' => '__return_true',
	) );

	register_rest_route( $ns, '/student/dashboard', array(
		'methods'             => 'GET',
		'callback'            => function () {
			if ( ! is_user_logged_in() ) return new WP_Error( 'rest_forbidden', 'Login required', array( 'status' => 403 ) );
			global $wpdb;
			$user_id = get_current_user_id();
			$student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_student_records WHERE user_id=%d LIMIT 1", $user_id ) );
			if ( ! $student ) return new WP_Error( 'not_found', 'Student not found', array( 'status' => 404 ) );
			return rest_ensure_response( array( 'student' => $student ) );
		},
		'permission_callback' => '__return_true',
	) );

	register_rest_route( $ns, '/student/attendance', array(
		'methods'             => 'GET',
		'callback'            => function () {
			if ( ! is_user_logged_in() ) return new WP_Error( 'rest_forbidden', 'Login required', array( 'status' => 403 ) );
			global $wpdb;
			$user_id = get_current_user_id();
			$student = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}ss_student_records WHERE user_id=%d LIMIT 1", $user_id ) );
			if ( ! $student ) return new WP_Error( 'not_found', 'Student not found', array( 'status' => 404 ) );
			$att = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_attendance WHERE student_record_id=%d ORDER BY date DESC LIMIT 30", $student->ID ) );
			return rest_ensure_response( $att );
		},
		'permission_callback' => '__return_true',
	) );

	register_rest_route( $ns, '/student/fees', array(
		'methods'             => 'GET',
		'callback'            => function () {
			if ( ! is_user_logged_in() ) return new WP_Error( 'rest_forbidden', 'Login required', array( 'status' => 403 ) );
			global $wpdb;
			$user_id = get_current_user_id();
			$student = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}ss_student_records WHERE user_id=%d LIMIT 1", $user_id ) );
			if ( ! $student ) return new WP_Error( 'not_found', 'Student not found', array( 'status' => 404 ) );
			$invoices = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_invoices WHERE student_record_id=%d ORDER BY created_at DESC LIMIT 10", $student->ID ) );
			return rest_ensure_response( $invoices );
		},
		'permission_callback' => '__return_true',
	) );

	register_rest_route( $ns, '/student/results', array(
		'methods'             => 'GET',
		'callback'            => function () {
			if ( ! is_user_logged_in() ) return new WP_Error( 'rest_forbidden', 'Login required', array( 'status' => 403 ) );
			global $wpdb;
			$user_id = get_current_user_id();
			$student = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}ss_student_records WHERE user_id=%d LIMIT 1", $user_id ) );
			if ( ! $student ) return new WP_Error( 'not_found', 'Student not found', array( 'status' => 404 ) );
			$results = $wpdb->get_results( $wpdb->prepare( "SELECT er.*, ep.total_marks, ep.pass_marks, s.label AS subject_label FROM {$wpdb->prefix}ss_exam_results er INNER JOIN {$wpdb->prefix}ss_exam_papers ep ON er.exam_paper_id=ep.ID INNER JOIN {$wpdb->prefix}ss_subjects s ON ep.subject_id=s.ID WHERE er.student_record_id=%d ORDER BY er.created_at DESC", $student->ID ) );
			return rest_ensure_response( $results );
		},
		'permission_callback' => '__return_true',
	) );
} );
