<?php
/**
 * Custom roles & capabilities for SSM.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Capabilities {

    /**
     * All custom capabilities the plugin understands.
     */
    public static function all_caps() {
        return array(
            'ssm_view_dashboard',
            'ssm_manage_students',
            'ssm_manage_staff',
            'ssm_manage_admissions',
            'ssm_manage_classes',
            'ssm_manage_attendance',
            'ssm_manage_exams',
            'ssm_manage_results',
            'ssm_manage_fees',
            'ssm_collect_fees',
            'ssm_manage_library',
            'ssm_manage_hostel',
            'ssm_manage_transport',
            'ssm_manage_lectures',
            'ssm_manage_tickets',
            'ssm_manage_notifications',
            'ssm_view_reports',
            'ssm_manage_settings',
        );
    }

    /**
     * Register custom roles when the plugin activates.
     */
    public static function install_roles() {
        $all = array_fill_keys( self::all_caps(), true );

        // School-wide admin (almost everything).
        add_role( 'ssm_school_admin', 'School Admin', array_merge( array( 'read' => true ), $all ) );

        // Teacher: classes, attendance, exams, results, lectures, materials.
        add_role( 'ssm_teacher', 'Teacher', array(
            'read'                    => true,
            'ssm_view_dashboard'      => true,
            'ssm_manage_classes'      => true,
            'ssm_manage_attendance'   => true,
            'ssm_manage_exams'        => true,
            'ssm_manage_results'      => true,
            'ssm_manage_lectures'     => true,
            'ssm_manage_notifications'=> true,
        ) );

        // Accountant: fees, invoices, reports.
        add_role( 'ssm_accountant', 'Accountant', array(
            'read'               => true,
            'ssm_view_dashboard' => true,
            'ssm_manage_fees'    => true,
            'ssm_collect_fees'   => true,
            'ssm_view_reports'   => true,
        ) );

        // Librarian: books only.
        add_role( 'ssm_librarian', 'Librarian', array(
            'read'                => true,
            'ssm_view_dashboard'  => true,
            'ssm_manage_library'  => true,
        ) );

        // Student & Parent (frontend portal).
        add_role( 'ssm_student', 'Student', array( 'read' => true ) );
        add_role( 'ssm_parent',  'Parent',  array( 'read' => true ) );

        // Always grant Administrators every SSM cap.
        $admin = get_role( 'administrator' );
        if ( $admin ) {
            foreach ( self::all_caps() as $cap ) {
                $admin->add_cap( $cap );
            }
        }
    }

    /**
     * Map any SSM cap check to manage_options for safety in stock setups.
     */
    public static function user_can( $cap ) {
        return current_user_can( $cap ) || current_user_can( 'manage_options' );
    }
}
