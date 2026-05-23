<?php
/**
 * Uninstall handler for Smart School Manager.
 *
 * Triggered when the plugin is *deleted* from the WordPress admin.
 * Cleans up DB tables and options when the user opts in via the
 * `ssm_delete_data_on_uninstall` setting.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Only purge data if the user explicitly opted in via Settings.
$opts = get_option( 'ssm_settings', array() );
$purge = ! empty( $opts['delete_data_on_uninstall'] );

if ( $purge ) {
    global $wpdb;
    $tables = array(
        'schools', 'sessions', 'categories', 'classes', 'subjects',
        'mediums', 'student_types', 'students', 'admissions', 'inquiries',
        'staff', 'roles', 'admins', 'staff_attendance', 'staff_leaves',
        'attendance', 'timetable', 'homework', 'study_materials',
        'notices', 'events', 'activities', 'meetings', 'student_leaves',
        'ratings', 'exams', 'exam_groups', 'exam_results', 'assessments',
        'fee_structures', 'invoices', 'payments', 'concession_types',
        'student_concessions', 'income', 'expenses', 'books', 'books_issued',
        'hostels', 'hostel_rooms', 'routes', 'vehicles', 'lectures',
        'chapters', 'tickets', 'logs', 'notifications', 'certificates',
    );
    foreach ( $tables as $t ) {
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ssm_{$t}" );
    }

    delete_option( 'ssm_settings' );
    delete_option( 'ssm_db_version' );

    // Clear scheduled events.
    wp_clear_scheduled_hook( 'ssm_daily_cron' );
    wp_clear_scheduled_hook( 'ssm_hourly_cron' );

    // Remove custom roles if any were added.
    if ( get_role( 'ssm_school_admin' ) ) remove_role( 'ssm_school_admin' );
    if ( get_role( 'ssm_teacher' ) )      remove_role( 'ssm_teacher' );
    if ( get_role( 'ssm_accountant' ) )   remove_role( 'ssm_accountant' );
    if ( get_role( 'ssm_librarian' ) )    remove_role( 'ssm_librarian' );
    if ( get_role( 'ssm_student' ) )      remove_role( 'ssm_student' );
    if ( get_role( 'ssm_parent' ) )       remove_role( 'ssm_parent' );
}
