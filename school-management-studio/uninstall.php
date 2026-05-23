<?php
/**
 * Uninstall handler for School Management Studio.
 *
 * @package SchoolManagementStudio
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

$opts  = get_option( 'sms_settings', array() );
$purge = ! empty( $opts['delete_data_on_uninstall'] );

if ( $purge ) {
    global $wpdb;
    $tables = array(
        'schools', 'sessions', 'categories', 'classes', 'subjects',
        'mediums', 'student_types', 'students', 'admissions', 'inquiries',
        'staff', 'roles', 'staff_attendance', 'staff_leaves',
        'attendance', 'timetable', 'homework', 'study_materials',
        'notices', 'events', 'meetings', 'exams', 'exam_groups',
        'exam_results', 'fee_structures', 'invoices', 'payments',
        'concession_types', 'student_concessions', 'income', 'expenses',
        'books', 'books_issued', 'hostels', 'hostel_rooms', 'routes',
        'vehicles', 'lectures', 'tickets', 'logs', 'notifications',
    );
    foreach ( $tables as $t ) {
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}sms_{$t}" );
    }
    delete_option( 'sms_settings' );
    delete_option( 'sms_db_version' );
    wp_clear_scheduled_hook( 'sms_daily_cron' );
    wp_clear_scheduled_hook( 'sms_hourly_cron' );
}
