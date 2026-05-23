<?php
/**
 * Uninstall School Softwere — drop all plugin tables and options.
 *
 * @package School_Softwere
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || die();

global $wpdb;

$tables = array(
	'ss_logs',
	'ss_reminder',
	'ss_academic_reports',
	'ss_ticket_history',
	'ss_tickets',
	'ss_activities',
	'ss_meetings',
	'ss_lecture',
	'ss_chapter',
	'ss_homework_submission',
	'ss_homework',
	'ss_study_materials',
	'ss_event_responses',
	'ss_events',
	'ss_class_school_notice',
	'ss_notices',
	'ss_library_cards',
	'ss_books_issued',
	'ss_books',
	'ss_rooms',
	'ss_hostels',
	'ss_route_vehicle',
	'ss_routes',
	'ss_vehicles',
	'ss_leaves',
	'ss_transfer_certificates',
	'ss_certificate_student',
	'ss_certificates',
	'ss_inquiries',
	'ss_staff_attendance',
	'ss_attendance',
	'ss_exam_results',
	'ss_admit_cards',
	'ss_exam_papers',
	'ss_exams_group',
	'ss_exams',
	'ss_pending_payments',
	'ss_payments',
	'ss_invoices',
	'ss_student_fees',
	'ss_fees',
	'ss_student_concession',
	'ss_concession_fee_mappings',
	'ss_concession_types',
	'ss_expense_categories',
	'ss_expenses',
	'ss_income_categories',
	'ss_income',
	'ss_routines',
	'ss_subjects',
	'ss_subject_types',
	'ss_transfers',
	'ss_promotions',
	'ss_student_records',
	'ss_staff',
	'ss_admins',
	'ss_roles',
	'ss_sections',
	'ss_class_school',
	'ss_sessions',
	'ss_classes',
	'ss_category',
	'ss_medium',
	'ss_student_type',
	'ss_settings',
	'ss_schools',
);

foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}{$table}`" ); // phpcs:ignore
}

// Delete plugin options
$options = array(
	'ss_db_version',
	'ss_setup_complete',
	'ss_activation_redirect',
	'ss_settings',
);
foreach ( $options as $option ) {
	delete_option( $option );
}
