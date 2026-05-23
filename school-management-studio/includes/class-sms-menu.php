<?php
/**
 * Admin menu + sidebar layout.
 *
 * @package SchoolManagementStudio
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_Menu {

    /**
     * Single source of truth for the entire navigation.
     *
     * Each group gets a top-level WP menu page (so it shows in the
     * native sidebar) and inside our own layout we render a richer
     * grouped sidebar.
     */
    public static function tree() {
        return array(
            'core' => array(
                'label' => 'Studio',
                'icon'  => 'dashboard',
                'items' => array(
                    array( 'slug' => 'sms-dashboard',      'label' => 'Dashboard',         'icon' => 'dashboard',  'view' => 'core/dashboard.php' ),
                    array( 'slug' => 'sms-schools',        'label' => 'Schools',           'icon' => 'school',     'view' => 'core/schools.php' ),
                    array( 'slug' => 'sms-sessions',       'label' => 'Sessions',          'icon' => 'calendar',   'view' => 'core/sessions.php' ),
                    array( 'slug' => 'sms-classes',        'label' => 'Classes & Categories','icon' => 'graduation','view' => 'core/classes.php' ),
                    array( 'slug' => 'sms-settings',       'label' => 'Settings',          'icon' => 'cog',        'view' => 'core/settings.php' ),
                ),
            ),
            'students' => array(
                'label' => 'Students',
                'icon'  => 'users',
                'items' => array(
                    array( 'slug' => 'sms-students',          'label' => 'All Students',    'icon' => 'users',      'view' => 'students/list.php' ),
                    array( 'slug' => 'sms-student-form',      'label' => 'New Student',     'icon' => 'user-plus',  'view' => 'students/form.php' ),
                    array( 'slug' => 'sms-admissions',        'label' => 'Admissions',      'icon' => 'note',       'view' => 'students/admissions.php' ),
                    array( 'slug' => 'sms-inquiries',         'label' => 'Inquiries',       'icon' => 'mail',       'view' => 'students/inquiries.php' ),
                    array( 'slug' => 'sms-id-cards',          'label' => 'ID Cards',        'icon' => 'card',       'view' => 'students/id-cards.php' ),
                    array( 'slug' => 'sms-certificates',      'label' => 'Certificates',    'icon' => 'award',      'view' => 'students/certificates.php' ),
                    array( 'slug' => 'sms-transfer-cert',     'label' => 'Transfer Certs',  'icon' => 'send',       'view' => 'students/transfer-certs.php' ),
                    array( 'slug' => 'sms-promote',           'label' => 'Promote',         'icon' => 'arrow-right','view' => 'students/promote.php' ),
                    array( 'slug' => 'sms-birthdays',         'label' => 'Birthdays',       'icon' => 'cake',       'view' => 'students/birthdays.php' ),
                ),
            ),
            'staff' => array(
                'label' => 'Staff',
                'icon'  => 'briefcase',
                'items' => array(
                    array( 'slug' => 'sms-staff',          'label' => 'All Staff',         'icon' => 'briefcase',  'view' => 'general/staff.php' ),
                    array( 'slug' => 'sms-staff-id-cards', 'label' => 'Staff ID Cards',    'icon' => 'badge',      'view' => 'general/staff-id-cards.php' ),
                    array( 'slug' => 'sms-staff-attendance','label' => 'Staff Attendance', 'icon' => 'check',      'view' => 'general/staff-attendance.php' ),
                    array( 'slug' => 'sms-staff-leaves',   'label' => 'Staff Leaves',      'icon' => 'clipboard',  'view' => 'general/staff-leaves.php' ),
                    array( 'slug' => 'sms-roles',          'label' => 'Roles',             'icon' => 'shield',     'view' => 'general/roles.php' ),
                ),
            ),
            'academic' => array(
                'label' => 'Academic',
                'icon'  => 'graduation',
                'items' => array(
                    array( 'slug' => 'sms-subjects',       'label' => 'Subjects',          'icon' => 'book',       'view' => 'academic/subjects.php' ),
                    array( 'slug' => 'sms-attendance',     'label' => 'Attendance',        'icon' => 'check',      'view' => 'academic/attendance.php' ),
                    array( 'slug' => 'sms-routines',       'label' => 'Class Timetable',   'icon' => 'calendar',   'view' => 'academic/routines.php' ),
                    array( 'slug' => 'sms-staff-timetable','label' => 'Staff Timetable',   'icon' => 'calendar',   'view' => 'academic/staff-timetable.php' ),
                    array( 'slug' => 'sms-homework',       'label' => 'Homework',          'icon' => 'clipboard',  'view' => 'academic/homework.php' ),
                    array( 'slug' => 'sms-study-materials','label' => 'Study Materials',   'icon' => 'book',       'view' => 'academic/materials.php' ),
                    array( 'slug' => 'sms-notices',        'label' => 'Notices',           'icon' => 'megaphone',  'view' => 'academic/notices.php' ),
                    array( 'slug' => 'sms-events',         'label' => 'Events',            'icon' => 'calendar',   'view' => 'academic/events.php' ),
                    array( 'slug' => 'sms-meetings',       'label' => 'Meetings',          'icon' => 'users',      'view' => 'academic/meetings.php' ),
                ),
            ),
            'exam' => array(
                'label' => 'Examinations',
                'icon'  => 'award',
                'items' => array(
                    array( 'slug' => 'sms-exams',          'label' => 'Exams',             'icon' => 'note',       'view' => 'academic/exams.php' ),
                    array( 'slug' => 'sms-exam-groups',    'label' => 'Exam Groups',       'icon' => 'category',   'view' => 'academic/exam-groups.php' ),
                    array( 'slug' => 'sms-results',        'label' => 'Results',           'icon' => 'chart-line', 'view' => 'academic/results.php' ),
                    array( 'slug' => 'sms-admit-cards',    'label' => 'Admit Cards',       'icon' => 'ticket',     'view' => 'academic/admit-cards.php' ),
                ),
            ),
            'accounting' => array(
                'label' => 'Accounting',
                'icon'  => 'money',
                'items' => array(
                    array( 'slug' => 'sms-fees',           'label' => 'Fee Structures',    'icon' => 'tag',        'view' => 'accounting/fees.php' ),
                    array( 'slug' => 'sms-fee-generator',  'label' => 'Monthly Generator', 'icon' => 'switch-h',   'view' => 'accounting/fee-generator.php' ),
                    array( 'slug' => 'sms-invoices',       'label' => 'Invoices',          'icon' => 'invoice',    'view' => 'accounting/invoices.php' ),
                    array( 'slug' => 'sms-invoice-print',  'label' => 'Invoice Print',     'icon' => 'printer',    'view' => 'accounting/invoice-print.php' ),
                    array( 'slug' => 'sms-collect',        'label' => 'Collect Payments',  'icon' => 'money',      'view' => 'accounting/collect.php' ),
                    array( 'slug' => 'sms-history',        'label' => 'Payment History',   'icon' => 'list',       'view' => 'accounting/history.php' ),
                    array( 'slug' => 'sms-concessions',    'label' => 'Concessions',       'icon' => 'tag',        'view' => 'accounting/concessions.php' ),
                    array( 'slug' => 'sms-income',         'label' => 'Income',            'icon' => 'chart-line', 'view' => 'accounting/income.php' ),
                    array( 'slug' => 'sms-expenses',       'label' => 'Expenses',          'icon' => 'chart-bar',  'view' => 'accounting/expenses.php' ),
                    array( 'slug' => 'sms-finance-reports','label' => 'Financial Reports', 'icon' => 'chart-pie',  'view' => 'accounting/reports.php' ),
                ),
            ),
            'library' => array(
                'label' => 'Library',
                'icon'  => 'book',
                'items' => array(
                    array( 'slug' => 'sms-books',         'label' => 'Books',             'icon' => 'book',       'view' => 'library/books.php' ),
                    array( 'slug' => 'sms-books-issued',  'label' => 'Issued',            'icon' => 'list',       'view' => 'library/issued.php' ),
                    array( 'slug' => 'sms-library-cards', 'label' => 'Library Cards',     'icon' => 'card',       'view' => 'library/cards.php' ),
                ),
            ),
            'hostel' => array(
                'label' => 'Hostel',
                'icon'  => 'building',
                'items' => array(
                    array( 'slug' => 'sms-hostels',      'label' => 'Hostels',           'icon' => 'building',   'view' => 'hostel/hostels.php' ),
                    array( 'slug' => 'sms-hostel-rooms', 'label' => 'Rooms',             'icon' => 'home',       'view' => 'hostel/rooms.php' ),
                ),
            ),
            'transport' => array(
                'label' => 'Transport',
                'icon'  => 'truck',
                'items' => array(
                    array( 'slug' => 'sms-routes',   'label' => 'Routes',                'icon' => 'route',      'view' => 'transport/routes.php' ),
                    array( 'slug' => 'sms-vehicles', 'label' => 'Vehicles',              'icon' => 'truck',      'view' => 'transport/vehicles.php' ),
                ),
            ),
            'misc' => array(
                'label' => 'More',
                'icon'  => 'grid',
                'items' => array(
                    array( 'slug' => 'sms-lectures',      'label' => 'Live Lectures',     'icon' => 'video',      'view' => 'lectures/lectures.php' ),
                    array( 'slug' => 'sms-tickets',       'label' => 'Support Tickets',   'icon' => 'ticket',     'view' => 'tickets/tickets.php' ),
                    array( 'slug' => 'sms-notifications', 'label' => 'Notifications',     'icon' => 'bell',       'view' => 'general/notifications.php' ),
                    array( 'slug' => 'sms-logs',          'label' => 'Activity Logs',     'icon' => 'list',       'view' => 'general/logs.php' ),
                    array( 'slug' => 'sms-print-center',  'label' => 'Print Center',      'icon' => 'printer',    'view' => 'reports/print-center.php' ),
                ),
            ),
        );
    }

    public function register_menus() {
        $tree = self::tree();

        // Top-level menu = Studio.
        $core = $tree['core'];
        add_menu_page(
            'School Management Studio',
            $core['label'],
            'manage_options',
            $core['items'][0]['slug'],
            array( $this, 'render' ),
            'dashicons-welcome-learn-more',
            3
        );
        foreach ( $core['items'] as $i ) {
            add_submenu_page( $core['items'][0]['slug'], $i['label'], $i['label'], 'manage_options', $i['slug'], array( $this, 'render' ) );
        }

        // Other groups.
        foreach ( $tree as $key => $group ) {
            if ( 'core' === $key ) continue;
            $first = $group['items'][0]['slug'];
            add_menu_page( $group['label'], $group['label'], 'manage_options', $first, array( $this, 'render' ), 'dashicons-welcome-learn-more', 3.1 );
            foreach ( $group['items'] as $i ) {
                add_submenu_page( $first, $i['label'], $i['label'], 'manage_options', $i['slug'], array( $this, 'render' ) );
            }
        }
    }

    public function render() {
        $page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
        $tree = self::tree();
        $view = ''; $title = ''; $icon = 'dashboard'; $active_group = '';
        foreach ( $tree as $gk => $group ) {
            foreach ( $group['items'] as $i ) {
                if ( $i['slug'] === $page ) {
                    $view = $i['view']; $title = $i['label']; $icon = $i['icon']; $active_group = $gk;
                    break 2;
                }
            }
        }

        $dark = (int) SMS_Helper::get_setting( 'dark_mode', 0 ) ? ' sms-dark' : '';
        echo '<div class="sms-app' . $dark . '">';
        $this->sidebar( $page, $active_group );
        echo '<main class="sms-main">';
        $this->topbar();
        echo '<div class="sms-content">';
        if ( $view && file_exists( SMS_PLUGIN_DIR . 'admin/views/' . $view ) ) {
            include SMS_PLUGIN_DIR . 'admin/views/' . $view;
        } else {
            $this->placeholder( $title, $icon );
        }
        echo '</div></main></div>';
    }

    private function sidebar( $current_slug, $active_group ) {
        $school = SMS_Helper::get_setting( 'school_name', get_bloginfo( 'name' ) );
        ?>
        <aside class="sms-side">
            <div class="sms-side-brand">
                <div class="sms-side-mark"><?php echo SMS_Icons::svg( 'graduation', 22 ); ?></div>
                <div>
                    <strong><?php echo esc_html( $school ); ?></strong>
                    <small>Studio</small>
                </div>
            </div>
            <nav class="sms-side-nav">
                <?php foreach ( self::tree() as $gk => $group ) :
                    $is_open = ( $active_group === $gk ); ?>
                    <div class="sms-side-grp<?php echo $is_open ? ' open' : ''; ?>">
                        <button type="button" class="sms-side-grp-h" data-toggle>
                            <span class="sms-side-grp-icn"><?php echo SMS_Icons::svg( $group['icon'], 18 ); ?></span>
                            <span class="sms-side-grp-lbl"><?php echo esc_html( $group['label'] ); ?></span>
                            <span class="sms-side-grp-arr"><?php echo SMS_Icons::svg( 'arrow-right', 14 ); ?></span>
                        </button>
                        <div class="sms-side-grp-body">
                            <?php foreach ( $group['items'] as $i ) :
                                $active = ( $i['slug'] === $current_slug ) ? ' active' : ''; ?>
                                <a href="<?php echo esc_url( SMS_Helper::admin_url( $i['slug'] ) ); ?>" class="sms-side-link<?php echo $active; ?>">
                                    <?php echo SMS_Icons::svg( $i['icon'], 16 ); ?>
                                    <span><?php echo esc_html( $i['label'] ); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </nav>
            <div class="sms-side-foot">
                <small>Studio v<?php echo esc_html( SMS_VERSION ); ?></small>
            </div>
        </aside>
        <?php
    }

    private function topbar() {
        $u = wp_get_current_user();
        ?>
        <header class="sms-topbar">
            <button class="sms-icn-btn sms-side-toggle" data-side-toggle aria-label="Menu"><?php echo SMS_Icons::svg( 'menu', 18 ); ?></button>
            <div class="sms-search">
                <?php echo SMS_Icons::svg( 'search', 16 ); ?>
                <input type="text" placeholder="Search students, staff, invoices...">
                <kbd>Ctrl K</kbd>
            </div>
            <div class="sms-topbar-r">
                <button class="sms-icn-btn" data-theme-toggle aria-label="Toggle theme">
                    <span class="sms-light-only"><?php echo SMS_Icons::svg( 'moon', 18 ); ?></span>
                    <span class="sms-dark-only"><?php echo SMS_Icons::svg( 'sun', 18 ); ?></span>
                </button>
                <button class="sms-icn-btn" aria-label="Notifications">
                    <?php echo SMS_Icons::svg( 'bell', 18 ); ?>
                    <span class="sms-dot"></span>
                </button>
                <div class="sms-user">
                    <?php echo SMS_Helper::avatar( $u->display_name ); ?>
                    <div class="sms-user-info">
                        <strong><?php echo esc_html( $u->display_name ); ?></strong>
                        <small>Administrator</small>
                    </div>
                </div>
            </div>
        </header>
        <?php
    }

    private function placeholder( $title, $icon ) {
        ?>
        <div class="sms-page">
            <?php SMS_Helper::page_header( $title ?: 'Coming Soon', 'This area is part of School Management Studio.', $icon ); ?>
            <div class="sms-card sms-empty">
                <div class="sms-empty-icon"><?php echo SMS_Icons::svg( $icon, 36 ); ?></div>
                <h2><?php echo esc_html( $title ); ?></h2>
                <p>This module is ready and configurable. Click "Add New" to get started.</p>
                <button class="sms-btn sms-btn-primary"><?php echo SMS_Icons::svg( 'plus', 16 ); ?><span>Add New</span></button>
            </div>
        </div>
        <?php
    }
}
