<?php
/**
 * Admin menu registration. Builds all 12 module groups and sub-pages.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Menu {

    /**
     * The full menu tree as data — single source of truth.
     */
    public static function tree() {
        return array(
            // 1. Core / Admin Manager
            'core' => array(
                'label' => 'Smart School',
                'icon'  => 'dashicons-welcome-learn-more',
                'submenu' => array(
                    array( 'slug' => 'ssm-dashboard',          'label' => 'Dashboard',           'view' => 'core/dashboard.php' ),
                    array( 'slug' => 'ssm-schools',            'label' => 'Schools',             'view' => 'core/schools.php' ),
                    array( 'slug' => 'ssm-sessions',           'label' => 'Sessions',            'view' => 'core/sessions.php' ),
                    array( 'slug' => 'ssm-classes-categories', 'label' => 'Classes & Categories','view' => 'core/classes-categories.php' ),
                    array( 'slug' => 'ssm-settings',           'label' => 'Settings',            'view' => 'core/settings.php' ),
                ),
            ),

            // 2. General (School Admin)
            'general' => array(
                'label' => 'General Admin',
                'icon'  => 'dashicons-admin-users',
                'submenu' => array(
                    array( 'slug' => 'ssm-school-dashboard',   'label' => 'School Dashboard',     'view' => 'general/dashboard.php' ),
                    array( 'slug' => 'ssm-students',           'label' => 'Students',             'view' => 'general/students.php' ),
                    array( 'slug' => 'ssm-student-form',       'label' => 'New Student (Wizard)', 'view' => 'general/student-form.php' ),
                    array( 'slug' => 'ssm-admissions',         'label' => 'Admissions',           'view' => 'general/admissions.php' ),
                    array( 'slug' => 'ssm-inquiries',          'label' => 'Inquiries',            'view' => 'general/inquiries.php' ),
                    array( 'slug' => 'ssm-staff',              'label' => 'Employees / Staff',    'view' => 'general/staff.php' ),
                    array( 'slug' => 'ssm-roles',              'label' => 'Roles',                'view' => 'general/roles.php' ),
                    array( 'slug' => 'ssm-admins',             'label' => 'Admins',               'view' => 'general/admins.php' ),
                    array( 'slug' => 'ssm-certificates',       'label' => 'Certificates',         'view' => 'general/certificates.php' ),
                    array( 'slug' => 'ssm-id-cards',           'label' => 'Student ID Cards',     'view' => 'general/id-cards.php' ),
                    array( 'slug' => 'ssm-staff-id-cards',     'label' => 'Staff ID Cards',       'view' => 'general/staff-id-cards.php' ),
                    array( 'slug' => 'ssm-transfer-cert',      'label' => 'Transfer Certificates','view' => 'general/transfer-certs.php' ),
                    array( 'slug' => 'ssm-transfer-student',   'label' => 'Transfer Student',     'view' => 'general/transfer-student.php' ),
                    array( 'slug' => 'ssm-promote',            'label' => 'Promote',              'view' => 'general/promote.php' ),
                    array( 'slug' => 'ssm-unassign-class',     'label' => 'Unassign Class',       'view' => 'general/unassign.php' ),
                    array( 'slug' => 'ssm-notifications',      'label' => 'Notifications',       'view' => 'general/notifications.php' ),
                    array( 'slug' => 'ssm-staff-attendance',   'label' => 'Staff Attendance',     'view' => 'general/staff-attendance.php' ),
                    array( 'slug' => 'ssm-staff-leaves',       'label' => 'Staff Leaves',         'view' => 'general/staff-leaves.php' ),
                    array( 'slug' => 'ssm-staff-live-classes', 'label' => 'Staff Live Classes',   'view' => 'general/staff-live-classes.php' ),
                    array( 'slug' => 'ssm-birthdays',          'label' => 'Student Birthdays',    'view' => 'general/birthdays.php' ),
                    array( 'slug' => 'ssm-logs',               'label' => 'Logs',                 'view' => 'general/logs.php' ),
                    array( 'slug' => 'ssm-school-settings',    'label' => 'School Settings',      'view' => 'general/settings.php' ),
                    array( 'slug' => 'ssm-setup-wizard',       'label' => 'Setup Wizard',         'view' => 'general/setup-wizard.php' ),
                ),
            ),

            // 3. Class Management
            'class' => array(
                'label' => 'Class Management',
                'icon'  => 'dashicons-welcome-write-blog',
                'submenu' => array(
                    array( 'slug' => 'ssm-classes',         'label' => 'Classes',          'view' => 'class/classes.php' ),
                    array( 'slug' => 'ssm-subjects',        'label' => 'Subjects',         'view' => 'class/subjects.php' ),
                    array( 'slug' => 'ssm-attendance',      'label' => 'Attendance',       'view' => 'class/attendance.php' ),
                    array( 'slug' => 'ssm-routines',        'label' => 'Routines',         'view' => 'class/routines.php' ),
                    array( 'slug' => 'ssm-staff-timetable', 'label' => 'Staff Timetable',  'view' => 'class/staff-timetable.php' ),
                    array( 'slug' => 'ssm-homework',        'label' => 'Homework',         'view' => 'class/homework.php' ),
                    array( 'slug' => 'ssm-study-materials', 'label' => 'Study Materials',  'view' => 'class/study-materials.php' ),
                    array( 'slug' => 'ssm-notices',         'label' => 'Notices',          'view' => 'class/notices.php' ),
                    array( 'slug' => 'ssm-events',          'label' => 'Events',           'view' => 'class/events.php' ),
                    array( 'slug' => 'ssm-activities',      'label' => 'Activities',       'view' => 'class/activities.php' ),
                    array( 'slug' => 'ssm-meetings',        'label' => 'Meetings',         'view' => 'class/meetings.php' ),
                    array( 'slug' => 'ssm-student-leaves',  'label' => 'Student Leaves',   'view' => 'class/student-leaves.php' ),
                    array( 'slug' => 'ssm-student-types',   'label' => 'Student Types',    'view' => 'class/student-types.php' ),
                    array( 'slug' => 'ssm-mediums',         'label' => 'Medium',           'view' => 'class/mediums.php' ),
                    array( 'slug' => 'ssm-ratings',         'label' => 'Rating',           'view' => 'class/ratings.php' ),
                ),
            ),

            // 4. Examination
            'exam' => array(
                'label' => 'Examination',
                'icon'  => 'dashicons-welcome-write-blog',
                'submenu' => array(
                    array( 'slug' => 'ssm-exams',            'label' => 'Exams',             'view' => 'exam/exams.php' ),
                    array( 'slug' => 'ssm-exam-groups',      'label' => 'Exam Groups',       'view' => 'exam/exam-groups.php' ),
                    array( 'slug' => 'ssm-results',          'label' => 'Results',           'view' => 'exam/results.php' ),
                    array( 'slug' => 'ssm-admit-cards',      'label' => 'Admit Cards',       'view' => 'exam/admit-cards.php' ),
                    array( 'slug' => 'ssm-assessment',       'label' => 'Assessment',        'view' => 'exam/assessment.php' ),
                    array( 'slug' => 'ssm-academic-report',  'label' => 'Academic Report',   'view' => 'exam/academic-report.php' ),
                    array( 'slug' => 'ssm-bulk-print-results','label' => 'Bulk Print Results','view' => 'exam/bulk-print.php' ),
                ),
            ),

            // 5. Accounting
            'accounting' => array(
                'label' => 'Accounting',
                'icon'  => 'dashicons-money-alt',
                'submenu' => array(
                    array( 'slug' => 'ssm-fees',              'label' => 'Fees',             'view' => 'accounting/fees.php' ),
                    array( 'slug' => 'ssm-fee-generator',     'label' => 'Monthly Fee Generator', 'view' => 'accounting/fee-generator.php' ),
                    array( 'slug' => 'ssm-invoices',          'label' => 'Invoices',         'view' => 'accounting/invoices.php' ),
                    array( 'slug' => 'ssm-invoice-print',     'label' => 'Invoice Print',    'view' => 'accounting/invoice-print.php' ),
                    array( 'slug' => 'ssm-collect-payments',  'label' => 'Collect Payments', 'view' => 'accounting/collect.php' ),
                    array( 'slug' => 'ssm-payment-history',   'label' => 'Payment History',  'view' => 'accounting/history.php' ),
                    array( 'slug' => 'ssm-concession-types',  'label' => 'Concession Types', 'view' => 'accounting/concession-types.php' ),
                    array( 'slug' => 'ssm-student-concession','label' => 'Students Concession','view' => 'accounting/student-concessions.php' ),
                    array( 'slug' => 'ssm-income',            'label' => 'Income',           'view' => 'accounting/income.php' ),
                    array( 'slug' => 'ssm-expenses',          'label' => 'Expenses',         'view' => 'accounting/expenses.php' ),
                    array( 'slug' => 'ssm-finance-reports',   'label' => 'Financial Reports','view' => 'accounting/reports.php' ),
                ),
            ),

            // 6. Library
            'library' => array(
                'label' => 'Library',
                'icon'  => 'dashicons-book-alt',
                'submenu' => array(
                    array( 'slug' => 'ssm-books',         'label' => 'Books',          'view' => 'library/books.php' ),
                    array( 'slug' => 'ssm-books-issued',  'label' => 'Books Issued',   'view' => 'library/books-issued.php' ),
                    array( 'slug' => 'ssm-library-cards', 'label' => 'Library Cards',  'view' => 'library/cards.php' ),
                ),
            ),

            // 7. Hostel
            'hostel' => array(
                'label' => 'Hostel',
                'icon'  => 'dashicons-building',
                'submenu' => array(
                    array( 'slug' => 'ssm-hostels',      'label' => 'Hostels', 'view' => 'hostel/hostels.php' ),
                    array( 'slug' => 'ssm-hostel-rooms', 'label' => 'Rooms',   'view' => 'hostel/rooms.php' ),
                ),
            ),

            // 8. Transport
            'transport' => array(
                'label' => 'Transport',
                'icon'  => 'dashicons-car',
                'submenu' => array(
                    array( 'slug' => 'ssm-routes',           'label' => 'Routes',   'view' => 'transport/routes.php' ),
                    array( 'slug' => 'ssm-vehicles',         'label' => 'Vehicles', 'view' => 'transport/vehicles.php' ),
                    array( 'slug' => 'ssm-transport-reports','label' => 'Reports',  'view' => 'transport/reports.php' ),
                ),
            ),

            // 9. Lectures
            'lectures' => array(
                'label' => 'Live Lectures',
                'icon'  => 'dashicons-video-alt3',
                'submenu' => array(
                    array( 'slug' => 'ssm-lectures', 'label' => 'Live Lectures', 'view' => 'lectures/lectures.php' ),
                ),
            ),

            // 10. Chapters
            'chapters' => array(
                'label' => 'Chapters',
                'icon'  => 'dashicons-book',
                'submenu' => array(
                    array( 'slug' => 'ssm-chapters', 'label' => 'Chapters', 'view' => 'chapters/chapters.php' ),
                ),
            ),

            // 11. Tickets
            'tickets' => array(
                'label' => 'Support Tickets',
                'icon'  => 'dashicons-tickets-alt',
                'submenu' => array(
                    array( 'slug' => 'ssm-tickets', 'label' => 'Tickets', 'view' => 'tickets/tickets.php' ),
                ),
            ),

            // 12. Print / Export
            'print' => array(
                'label' => 'Print / Export',
                'icon'  => 'dashicons-printer',
                'submenu' => array(
                    array( 'slug' => 'ssm-print-center', 'label' => 'Print Center', 'view' => 'print/center.php' ),
                ),
            ),
        );
    }

    /**
     * Register all menus into WP admin.
     */
    public function register_menus() {
        $tree = self::tree();

        // Top-level core menu first.
        $core = $tree['core'];
        add_menu_page(
            'Smart School Manager',
            $core['label'],
            'manage_options',
            $core['submenu'][0]['slug'],
            array( $this, 'render' ),
            'dashicons-welcome-learn-more',
            3
        );

        foreach ( $core['submenu'] as $sub ) {
            add_submenu_page(
                $core['submenu'][0]['slug'],
                $sub['label'],
                $sub['label'],
                'manage_options',
                $sub['slug'],
                array( $this, 'render' )
            );
        }

        // Other top-level groups.
        foreach ( $tree as $key => $group ) {
            if ( 'core' === $key ) continue;
            $first = $group['submenu'][0]['slug'];
            add_menu_page(
                $group['label'],
                $group['label'],
                'manage_options',
                $first,
                array( $this, 'render' ),
                $group['icon'],
                3.1
            );
            foreach ( $group['submenu'] as $sub ) {
                add_submenu_page(
                    $first,
                    $sub['label'],
                    $sub['label'],
                    'manage_options',
                    $sub['slug'],
                    array( $this, 'render' )
                );
            }
        }
    }

    /**
     * Render the page based on current slug.
     */
    public function render() {
        $page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
        $tree = self::tree();
        $view = '';
        $title = '';

        foreach ( $tree as $group ) {
            foreach ( $group['submenu'] as $sub ) {
                if ( $sub['slug'] === $page ) {
                    $view  = $sub['view'];
                    $title = $sub['label'];
                    break 2;
                }
            }
        }

        // Wrap with layout shell.
        echo '<div class="ssm-app">';
        $this->render_topbar( $title );
        echo '<div class="ssm-content">';
        if ( $view && file_exists( SSM_PLUGIN_DIR . 'admin/views/' . $view ) ) {
            include SSM_PLUGIN_DIR . 'admin/views/' . $view;
        } else {
            SSM_Helper::render_placeholder(
                $title ? $title : 'Page',
                'This module is part of Smart School Manager and is ready for configuration.',
                'dashicons-admin-generic'
            );
        }
        echo '</div></div>';
    }

    /**
     * Top bar: school name, search, user, quick actions.
     */
    private function render_topbar( $title ) {
        $school = SSM_Helper::get_setting( 'school_name', get_bloginfo( 'name' ) );
        $current_user = wp_get_current_user();
        ?>
        <div class="ssm-topbar">
            <div class="ssm-topbar-left">
                <div class="ssm-brand">
                    <span class="ssm-brand-mark">SS</span>
                    <div>
                        <strong><?php echo esc_html( $school ); ?></strong>
                        <small>Smart School Manager</small>
                    </div>
                </div>
            </div>
            <div class="ssm-topbar-center">
                <div class="ssm-search">
                    <span class="dashicons dashicons-search"></span>
                    <input type="text" placeholder="Search students, staff, invoices…" />
                    <kbd>Ctrl K</kbd>
                </div>
            </div>
            <div class="ssm-topbar-right">
                <button class="ssm-icon-btn" title="Notifications"><span class="dashicons dashicons-bell"></span><span class="ssm-dot"></span></button>
                <button class="ssm-icon-btn" title="Help"><span class="dashicons dashicons-editor-help"></span></button>
                <div class="ssm-user">
                    <?php echo SSM_Helper::avatar( $current_user->display_name ); ?>
                    <div class="ssm-user-info">
                        <strong><?php echo esc_html( $current_user->display_name ); ?></strong>
                        <small>Administrator</small>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
