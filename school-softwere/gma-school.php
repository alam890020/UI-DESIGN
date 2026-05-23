<?php
/**
 * Plugin Name:       GMA School Management
 * Plugin URI:        https://guidancemodernacademy.in
 * Description:       Complete School Management System for Guidance Modern Academy (English Medium) — Students, Staff, Fees, Exams, Library, Transport, Hostel & more.
 * Version:           1.0.0
 * Author:            Guidance Modern Academy
 * Author URI:        https://guidancemodernacademy.in
 * Text Domain:       gma-school
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPL v2 or later
 *
 * @package GMA_School
 */

defined( 'ABSPATH' ) || exit;

// ── Constants ─────────────────────────────────────────────────────────────────
define( 'GMA_VERSION',    '1.0.0' );
define( 'GMA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GMA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GMA_PLUGIN_FILE', __FILE__ );
define( 'GMA_TEXT_DOMAIN', 'gma-school' );

// ── Table name constants ───────────────────────────────────────────────────────
global $wpdb;
define( 'GMA_TABLE_SCHOOLS',              $wpdb->prefix . 'gma_schools' );
define( 'GMA_TABLE_SETTINGS',             $wpdb->prefix . 'gma_settings' );
define( 'GMA_TABLE_CATEGORY',             $wpdb->prefix . 'gma_category' );
define( 'GMA_TABLE_CLASSES',              $wpdb->prefix . 'gma_classes' );
define( 'GMA_TABLE_SESSIONS',             $wpdb->prefix . 'gma_sessions' );
define( 'GMA_TABLE_MEDIUM',               $wpdb->prefix . 'gma_medium' );
define( 'GMA_TABLE_CLASS_SCHOOL',         $wpdb->prefix . 'gma_class_school' );
define( 'GMA_TABLE_SECTIONS',             $wpdb->prefix . 'gma_sections' );
define( 'GMA_TABLE_STUDENT_TYPE',         $wpdb->prefix . 'gma_student_type' );
define( 'GMA_TABLE_STUDENT_RECORDS',      $wpdb->prefix . 'gma_student_records' );
define( 'GMA_TABLE_PROMOTIONS',           $wpdb->prefix . 'gma_promotions' );
define( 'GMA_TABLE_TRANSFERS',            $wpdb->prefix . 'gma_transfers' );
define( 'GMA_TABLE_ROLES',                $wpdb->prefix . 'gma_roles' );
define( 'GMA_TABLE_STAFF',                $wpdb->prefix . 'gma_staff' );
define( 'GMA_TABLE_ADMINS',               $wpdb->prefix . 'gma_admins' );
define( 'GMA_TABLE_SUBJECTS',             $wpdb->prefix . 'gma_subjects' );
define( 'GMA_TABLE_SUBJECT_TYPES',        $wpdb->prefix . 'gma_subject_types' );
define( 'GMA_TABLE_ROUTINES',             $wpdb->prefix . 'gma_routines' );
define( 'GMA_TABLE_FEES',                 $wpdb->prefix . 'gma_fees' );
define( 'GMA_TABLE_STUDENT_FEES',         $wpdb->prefix . 'gma_student_fees' );
define( 'GMA_TABLE_INVOICES',             $wpdb->prefix . 'gma_invoices' );
define( 'GMA_TABLE_PAYMENTS',             $wpdb->prefix . 'gma_payments' );
define( 'GMA_TABLE_CONCESSION_TYPES',     $wpdb->prefix . 'gma_concession_types' );
define( 'GMA_TABLE_CONCESSION_FEE_MAP',   $wpdb->prefix . 'gma_concession_fee_mappings' );
define( 'GMA_TABLE_STUDENT_CONCESSION',   $wpdb->prefix . 'gma_student_concession' );
define( 'GMA_TABLE_EXPENSE_CATEGORIES',   $wpdb->prefix . 'gma_expense_categories' );
define( 'GMA_TABLE_EXPENSES',             $wpdb->prefix . 'gma_expenses' );
define( 'GMA_TABLE_INCOME_CATEGORIES',    $wpdb->prefix . 'gma_income_categories' );
define( 'GMA_TABLE_INCOME',               $wpdb->prefix . 'gma_income' );
define( 'GMA_TABLE_ATTENDANCE',           $wpdb->prefix . 'gma_attendance' );
define( 'GMA_TABLE_STAFF_ATTENDANCE',     $wpdb->prefix . 'gma_staff_attendance' );
define( 'GMA_TABLE_EXAMS_GROUP',          $wpdb->prefix . 'gma_exams_group' );
define( 'GMA_TABLE_EXAMS',                $wpdb->prefix . 'gma_exams' );
define( 'GMA_TABLE_EXAM_PAPERS',          $wpdb->prefix . 'gma_exam_papers' );
define( 'GMA_TABLE_EXAM_RESULTS',         $wpdb->prefix . 'gma_exam_results' );
define( 'GMA_TABLE_ADMIT_CARDS',          $wpdb->prefix . 'gma_admit_cards' );
define( 'GMA_TABLE_NOTICES',              $wpdb->prefix . 'gma_notices' );
define( 'GMA_TABLE_EVENTS',               $wpdb->prefix . 'gma_events' );
define( 'GMA_TABLE_HOMEWORK',             $wpdb->prefix . 'gma_homework' );
define( 'GMA_TABLE_HOMEWORK_SUB',         $wpdb->prefix . 'gma_homework_submission' );
define( 'GMA_TABLE_STUDY_MATERIALS',      $wpdb->prefix . 'gma_study_materials' );
define( 'GMA_TABLE_BOOKS',                $wpdb->prefix . 'gma_books' );
define( 'GMA_TABLE_BOOKS_ISSUED',         $wpdb->prefix . 'gma_books_issued' );
define( 'GMA_TABLE_LIBRARY_CARDS',        $wpdb->prefix . 'gma_library_cards' );
define( 'GMA_TABLE_VEHICLES',             $wpdb->prefix . 'gma_vehicles' );
define( 'GMA_TABLE_ROUTES',               $wpdb->prefix . 'gma_routes' );
define( 'GMA_TABLE_ROUTE_VEHICLE',        $wpdb->prefix . 'gma_route_vehicle' );
define( 'GMA_TABLE_HOSTELS',              $wpdb->prefix . 'gma_hostels' );
define( 'GMA_TABLE_ROOMS',                $wpdb->prefix . 'gma_rooms' );
define( 'GMA_TABLE_LEAVES',               $wpdb->prefix . 'gma_leaves' );
define( 'GMA_TABLE_STUDENT_LEAVES',       $wpdb->prefix . 'gma_student_leaves' );
define( 'GMA_TABLE_CERTIFICATES',         $wpdb->prefix . 'gma_certificates' );
define( 'GMA_TABLE_CERTIFICATE_STUDENT',  $wpdb->prefix . 'gma_certificate_student' );
define( 'GMA_TABLE_TRANSFER_CERTS',       $wpdb->prefix . 'gma_transfer_certificates' );
define( 'GMA_TABLE_INQUIRIES',            $wpdb->prefix . 'gma_inquiries' );
define( 'GMA_TABLE_ADMISSIONS',           $wpdb->prefix . 'gma_admissions' );
define( 'GMA_TABLE_CHAPTERS',             $wpdb->prefix . 'gma_chapters' );
define( 'GMA_TABLE_LECTURES',             $wpdb->prefix . 'gma_lectures' );
define( 'GMA_TABLE_MEETINGS',             $wpdb->prefix . 'gma_meetings' );
define( 'GMA_TABLE_ACTIVITIES',           $wpdb->prefix . 'gma_activities' );
define( 'GMA_TABLE_RATINGS',              $wpdb->prefix . 'gma_ratings' );
define( 'GMA_TABLE_TICKETS',              $wpdb->prefix . 'gma_tickets' );
define( 'GMA_TABLE_TICKET_REPLIES',       $wpdb->prefix . 'gma_ticket_replies' );
define( 'GMA_TABLE_NOTIFICATIONS',        $wpdb->prefix . 'gma_notifications' );
define( 'GMA_TABLE_LOGS',                 $wpdb->prefix . 'gma_logs' );
define( 'GMA_TABLE_ASSESSMENTS',          $wpdb->prefix . 'gma_assessments' );
define( 'GMA_TABLE_ASSESSMENT_GRADES',    $wpdb->prefix . 'gma_assessment_grades' );

// ── Menu slugs ────────────────────────────────────────────────────────────────
define( 'GMA_MENU_DASHBOARD',       'gma-school' );
define( 'GMA_MENU_SCHOOLS',         'gma-schools' );
define( 'GMA_MENU_SESSIONS',        'gma-sessions' );
define( 'GMA_MENU_CLASSES',         'gma-classes' );
define( 'GMA_MENU_SETTINGS',        'gma-settings' );
define( 'GMA_MENU_STUDENTS',        'gma-students' );
define( 'GMA_MENU_ADMISSIONS',      'gma-admissions' );
define( 'GMA_MENU_STAFF',           'gma-staff' );
define( 'GMA_MENU_ROLES',           'gma-roles' );
define( 'GMA_MENU_ADMINS',          'gma-admins' );
define( 'GMA_MENU_CERTIFICATES',    'gma-certificates' );
define( 'GMA_MENU_ID_CARDS',        'gma-id-cards' );
define( 'GMA_MENU_TRANSFER_CERTS',  'gma-transfer-certificates' );
define( 'GMA_MENU_PROMOTE',         'gma-promote' );
define( 'GMA_MENU_INQUIRIES',       'gma-inquiries' );
define( 'GMA_MENU_NOTIFICATIONS',   'gma-notifications' );
define( 'GMA_MENU_STAFF_ATT',       'gma-staff-attendance' );
define( 'GMA_MENU_STAFF_LEAVES',    'gma-staff-leaves' );
define( 'GMA_MENU_SUBJECTS',        'gma-subjects' );
define( 'GMA_MENU_ATTENDANCE',      'gma-attendance' );
define( 'GMA_MENU_ROUTINES',        'gma-routines' );
define( 'GMA_MENU_HOMEWORK',        'gma-homework' );
define( 'GMA_MENU_STUDY_MATERIALS', 'gma-study-materials' );
define( 'GMA_MENU_NOTICES',         'gma-notices' );
define( 'GMA_MENU_EVENTS',          'gma-events' );
define( 'GMA_MENU_ACTIVITIES',      'gma-activities' );
define( 'GMA_MENU_MEETINGS',        'gma-meetings' );
define( 'GMA_MENU_STUDENT_LEAVES',  'gma-student-leaves' );
define( 'GMA_MENU_EXAMS',           'gma-exams' );
define( 'GMA_MENU_EXAM_GROUPS',     'gma-exam-groups' );
define( 'GMA_MENU_RESULTS',         'gma-results' );
define( 'GMA_MENU_ADMIT_CARDS',     'gma-admit-cards' );
define( 'GMA_MENU_ASSESSMENTS',     'gma-assessments' );
define( 'GMA_MENU_ACADEMIC_REPORT', 'gma-academic-report' );
define( 'GMA_MENU_FEES',            'gma-fees' );
define( 'GMA_MENU_INVOICES',        'gma-invoices' );
define( 'GMA_MENU_PAYMENTS',        'gma-payments' );
define( 'GMA_MENU_CONCESSIONS',     'gma-concessions' );
define( 'GMA_MENU_INCOME',          'gma-income' );
define( 'GMA_MENU_EXPENSES',        'gma-expenses' );
define( 'GMA_MENU_FIN_REPORTS',     'gma-finance-reports' );
define( 'GMA_MENU_LIBRARY',         'gma-library' );
define( 'GMA_MENU_HOSTEL',          'gma-hostel' );
define( 'GMA_MENU_TRANSPORT',       'gma-transport' );
define( 'GMA_MENU_LECTURES',        'gma-lectures' );
define( 'GMA_MENU_CHAPTERS',        'gma-chapters' );
define( 'GMA_MENU_TICKETS',         'gma-tickets' );
define( 'GMA_MENU_REPORTS',         'gma-reports' );
define( 'GMA_MENU_LOGS',            'gma-logs' );
define( 'GMA_MENU_WIZARD',          'gma-wizard' );
define( 'GMA_MENU_PRINT',           'gma-print' );

// ── Autoloader ────────────────────────────────────────────────────────────────
spl_autoload_register( function( $class ) {
	if ( strpos( $class, 'GMA_' ) !== 0 ) return;
	$file = GMA_PLUGIN_DIR . 'includes/class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';
	if ( file_exists( $file ) ) {
		require_once $file;
	}
} );

// ── Core includes ─────────────────────────────────────────────────────────────
require_once GMA_PLUGIN_DIR . 'includes/class-gma-helper.php';
require_once GMA_PLUGIN_DIR . 'includes/class-gma-database.php';
require_once GMA_PLUGIN_DIR . 'admin/class-gma-menu.php';
require_once GMA_PLUGIN_DIR . 'admin/class-gma-assets.php';

// ── Module includes ───────────────────────────────────────────────────────────
$gma_modules = array(
	// Core / Manager
	'admin/modules/manager/class-gma-module-schools.php',
	'admin/modules/manager/class-gma-module-sessions.php',
	'admin/modules/manager/class-gma-module-classes.php',
	'admin/modules/manager/class-gma-module-settings.php',
	// General / School Admin
	'admin/modules/school/class-gma-module-students.php',
	'admin/modules/school/class-gma-module-admissions.php',
	'admin/modules/school/class-gma-module-staff.php',
	'admin/modules/school/class-gma-module-roles.php',
	'admin/modules/school/class-gma-module-certificates.php',
	'admin/modules/school/class-gma-module-id-cards.php',
	'admin/modules/school/class-gma-module-notifications.php',
	'admin/modules/school/class-gma-module-inquiries.php',
	// Class Management
	'admin/modules/classes/class-gma-module-subjects.php',
	'admin/modules/classes/class-gma-module-attendance.php',
	'admin/modules/classes/class-gma-module-routines.php',
	'admin/modules/classes/class-gma-module-homework.php',
	'admin/modules/classes/class-gma-module-study-materials.php',
	'admin/modules/classes/class-gma-module-notices.php',
	'admin/modules/classes/class-gma-module-events.php',
	'admin/modules/classes/class-gma-module-leaves.php',
	// Examination
	'admin/modules/exams/class-gma-module-exams.php',
	'admin/modules/exams/class-gma-module-results.php',
	'admin/modules/exams/class-gma-module-admit-cards.php',
	'admin/modules/exams/class-gma-module-assessments.php',
	// Accounting
	'admin/modules/accounting/class-gma-module-fees.php',
	'admin/modules/accounting/class-gma-module-invoices.php',
	'admin/modules/accounting/class-gma-module-payments.php',
	'admin/modules/accounting/class-gma-module-concessions.php',
	'admin/modules/accounting/class-gma-module-income.php',
	'admin/modules/accounting/class-gma-module-expenses.php',
	// Library
	'admin/modules/library/class-gma-module-library.php',
	// Hostel
	'admin/modules/hostel/class-gma-module-hostel.php',
	// Transport
	'admin/modules/transport/class-gma-module-transport.php',
	// Lectures & Chapters
	'admin/modules/lectures/class-gma-module-lectures.php',
	'admin/modules/lectures/class-gma-module-chapters.php',
	// Support
	'admin/modules/support/class-gma-module-tickets.php',
	// Print
	'admin/modules/print/class-gma-module-print.php',
);

foreach ( $gma_modules as $module ) {
	$path = GMA_PLUGIN_DIR . $module;
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}

// ── Activation / Deactivation ─────────────────────────────────────────────────
register_activation_hook( __FILE__, array( 'GMA_Database', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'GMA_Database', 'deactivate' ) );

// ── Init ──────────────────────────────────────────────────────────────────────
add_action( 'plugins_loaded', array( 'GMA_Menu', 'init' ) );
add_action( 'plugins_loaded', array( 'GMA_Assets', 'init' ) );
add_action( 'plugins_loaded', 'gma_load_textdomain' );

function gma_load_textdomain() {
	load_plugin_textdomain( GMA_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
