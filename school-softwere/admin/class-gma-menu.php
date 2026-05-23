<?php
/**
 * GMA_Menu — Register all WordPress admin menus.
 *
 * @package GMA_School
 */
defined( 'ABSPATH' ) || exit;

class GMA_Menu {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_redirect' ) );
	}

	public static function handle_redirect() {
		if ( get_option( 'gma_activation_redirect' ) ) {
			delete_option( 'gma_activation_redirect' );
			if ( ! isset( $_GET['activate-multi'] ) ) {
				wp_safe_redirect( admin_url( 'admin.php?page=' . GMA_MENU_WIZARD ) );
				exit;
			}
		}
	}

	public static function register_menus() {
		$icon = 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>' );

		add_menu_page(
			__( 'GMA School', 'gma-school' ),
			__( 'GMA School', 'gma-school' ),
			'read', GMA_MENU_DASHBOARD,
			array( __CLASS__, 'page_dashboard' ),
			$icon, 2
		);

		// ── Dashboard
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Dashboard','gma-school' ), __( '🏠 Dashboard','gma-school' ), 'read', GMA_MENU_DASHBOARD, array( __CLASS__, 'page_dashboard' ) );

		// ── CORE MANAGER ─────────────────────────────────────────────
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Schools','gma-school' ),       __( '🏫 Schools','gma-school' ),        'manage_options', GMA_MENU_SCHOOLS,        array( __CLASS__, 'page_schools' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Sessions','gma-school' ),      __( '📅 Sessions','gma-school' ),       'manage_options', GMA_MENU_SESSIONS,       array( __CLASS__, 'page_sessions' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Classes','gma-school' ),       __( '📚 Classes','gma-school' ),        'manage_options', GMA_MENU_CLASSES,        array( __CLASS__, 'page_classes' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Settings','gma-school' ),      __( '⚙️ Settings','gma-school' ),       'manage_options', GMA_MENU_SETTINGS,       array( __CLASS__, 'page_settings' ) );

		// ── GENERAL / SCHOOL ADMIN ────────────────────────────────────
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Students','gma-school' ),       __( '🎓 Students','gma-school' ),          'read', GMA_MENU_STUDENTS,        array( __CLASS__, 'page_students' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Admissions','gma-school' ),     __( '📋 Admissions','gma-school' ),        'read', GMA_MENU_ADMISSIONS,      array( __CLASS__, 'page_admissions' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Staff','gma-school' ),          __( '👩‍🏫 Staff','gma-school' ),             'read', GMA_MENU_STAFF,           array( __CLASS__, 'page_staff' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Roles','gma-school' ),          __( '🔑 Roles','gma-school' ),             'manage_options', GMA_MENU_ROLES, array( __CLASS__, 'page_roles' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Admins','gma-school' ),         __( '👤 Admins','gma-school' ),            'manage_options', GMA_MENU_ADMINS, array( __CLASS__, 'page_admins' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Certificates','gma-school' ),   __( '📜 Certificates','gma-school' ),      'read', GMA_MENU_CERTIFICATES,    array( __CLASS__, 'page_certificates' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'ID Cards','gma-school' ),       __( '🪪 ID Cards','gma-school' ),          'read', GMA_MENU_ID_CARDS,        array( __CLASS__, 'page_id_cards' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Transfer Certs','gma-school' ), __( '📄 Transfer Certs','gma-school' ),    'read', GMA_MENU_TRANSFER_CERTS,  array( __CLASS__, 'page_transfer_certs' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Promote','gma-school' ),        __( '⬆️ Promote','gma-school' ),           'read', GMA_MENU_PROMOTE,         array( __CLASS__, 'page_promote' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Inquiries','gma-school' ),      __( '💬 Inquiries','gma-school' ),         'read', GMA_MENU_INQUIRIES,       array( __CLASS__, 'page_inquiries' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Notifications','gma-school' ),  __( '🔔 Notifications','gma-school' ),     'read', GMA_MENU_NOTIFICATIONS,   array( __CLASS__, 'page_notifications' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Staff Attendance','gma-school' ),__( '✅ Staff Attendance','gma-school' ), 'read', GMA_MENU_STAFF_ATT,       array( __CLASS__, 'page_staff_attendance' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Staff Leaves','gma-school' ),   __( '🗓️ Staff Leaves','gma-school' ),      'read', GMA_MENU_STAFF_LEAVES,    array( __CLASS__, 'page_staff_leaves' ) );

		// ── CLASS MANAGEMENT ─────────────────────────────────────────
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Subjects','gma-school' ),        __( '📖 Subjects','gma-school' ),         'read', GMA_MENU_SUBJECTS,        array( __CLASS__, 'page_subjects' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Attendance','gma-school' ),      __( '📌 Attendance','gma-school' ),       'read', GMA_MENU_ATTENDANCE,      array( __CLASS__, 'page_attendance' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Timetable','gma-school' ),       __( '🕐 Timetable','gma-school' ),        'read', GMA_MENU_ROUTINES,        array( __CLASS__, 'page_routines' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Homework','gma-school' ),        __( '📓 Homework','gma-school' ),         'read', GMA_MENU_HOMEWORK,        array( __CLASS__, 'page_homework' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Study Materials','gma-school' ), __( '📁 Study Materials','gma-school' ),  'read', GMA_MENU_STUDY_MATERIALS, array( __CLASS__, 'page_study_materials' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Notices','gma-school' ),         __( '📢 Notices','gma-school' ),          'read', GMA_MENU_NOTICES,         array( __CLASS__, 'page_notices' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Events','gma-school' ),          __( '🗓️ Events','gma-school' ),           'read', GMA_MENU_EVENTS,          array( __CLASS__, 'page_events' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Activities','gma-school' ),      __( '🏃 Activities','gma-school' ),       'read', GMA_MENU_ACTIVITIES,      array( __CLASS__, 'page_activities' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Meetings','gma-school' ),        __( '🤝 Meetings','gma-school' ),         'read', GMA_MENU_MEETINGS,        array( __CLASS__, 'page_meetings' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Student Leaves','gma-school' ),  __( '🙋 Student Leaves','gma-school' ),   'read', GMA_MENU_STUDENT_LEAVES,  array( __CLASS__, 'page_student_leaves' ) );

		// ── EXAMINATIONS ─────────────────────────────────────────────
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Exams','gma-school' ),           __( '📝 Exams','gma-school' ),            'read', GMA_MENU_EXAMS,           array( __CLASS__, 'page_exams' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Exam Groups','gma-school' ),     __( '📦 Exam Groups','gma-school' ),      'read', GMA_MENU_EXAM_GROUPS,     array( __CLASS__, 'page_exam_groups' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Results','gma-school' ),         __( '🏆 Results','gma-school' ),          'read', GMA_MENU_RESULTS,         array( __CLASS__, 'page_results' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Admit Cards','gma-school' ),     __( '🪪 Admit Cards','gma-school' ),      'read', GMA_MENU_ADMIT_CARDS,     array( __CLASS__, 'page_admit_cards' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Assessments','gma-school' ),     __( '📊 Assessments','gma-school' ),      'read', GMA_MENU_ASSESSMENTS,     array( __CLASS__, 'page_assessments' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Academic Report','gma-school' ), __( '📈 Academic Report','gma-school' ),  'read', GMA_MENU_ACADEMIC_REPORT, array( __CLASS__, 'page_academic_report' ) );

		// ── ACCOUNTING ───────────────────────────────────────────────
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Fees','gma-school' ),          __( '💰 Fees','gma-school' ),              'read', GMA_MENU_FEES,         array( __CLASS__, 'page_fees' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Invoices','gma-school' ),      __( '🧾 Invoices','gma-school' ),          'read', GMA_MENU_INVOICES,     array( __CLASS__, 'page_invoices' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Collect Payment','gma-school' ),__( '💳 Collect Payment','gma-school' ),  'read', GMA_MENU_PAYMENTS,     array( __CLASS__, 'page_payments' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Concessions','gma-school' ),   __( '🎟️ Concessions','gma-school' ),       'read', GMA_MENU_CONCESSIONS,  array( __CLASS__, 'page_concessions' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Income','gma-school' ),        __( '📥 Income','gma-school' ),            'read', GMA_MENU_INCOME,       array( __CLASS__, 'page_income' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Expenses','gma-school' ),      __( '📤 Expenses','gma-school' ),          'read', GMA_MENU_EXPENSES,     array( __CLASS__, 'page_expenses' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Finance Reports','gma-school' ),__( '📊 Finance Reports','gma-school' ),  'read', GMA_MENU_FIN_REPORTS,  array( __CLASS__, 'page_finance_reports' ) );

		// ── LIBRARY ──────────────────────────────────────────────────
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Library','gma-school' ),  __( '📚 Library','gma-school' ),   'read', GMA_MENU_LIBRARY,  array( __CLASS__, 'page_library' ) );

		// ── HOSTEL ───────────────────────────────────────────────────
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Hostel','gma-school' ),   __( '🏠 Hostel','gma-school' ),    'read', GMA_MENU_HOSTEL,   array( __CLASS__, 'page_hostel' ) );

		// ── TRANSPORT ────────────────────────────────────────────────
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Transport','gma-school' ),__( '🚌 Transport','gma-school' ),  'read', GMA_MENU_TRANSPORT,array( __CLASS__, 'page_transport' ) );

		// ── LECTURES & CHAPTERS ──────────────────────────────────────
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Chapters','gma-school' ), __( '📑 Chapters','gma-school' ),   'read', GMA_MENU_CHAPTERS, array( __CLASS__, 'page_chapters' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Lectures','gma-school' ), __( '🎥 Lectures','gma-school' ),   'read', GMA_MENU_LECTURES, array( __CLASS__, 'page_lectures' ) );

		// ── SUPPORT ──────────────────────────────────────────────────
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Support Tickets','gma-school' ), __( '🎫 Support Tickets','gma-school' ), 'read', GMA_MENU_TICKETS, array( __CLASS__, 'page_tickets' ) );

		// ── REPORTS & LOGS ───────────────────────────────────────────
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Reports','gma-school' ),  __( '📊 Reports','gma-school' ),    'read',           GMA_MENU_REPORTS, array( __CLASS__, 'page_reports' ) );
		add_submenu_page( GMA_MENU_DASHBOARD, __( 'Logs','gma-school' ),     __( '📋 Logs','gma-school' ),       'manage_options', GMA_MENU_LOGS,    array( __CLASS__, 'page_logs' ) );

		// ── PRINT (hidden) ───────────────────────────────────────────
		add_submenu_page( null, __( 'Print','gma-school' ), __( 'Print','gma-school' ), 'read', GMA_MENU_PRINT, array( __CLASS__, 'page_print' ) );

		// ── WIZARD (hidden) ──────────────────────────────────────────
		add_submenu_page( null, __( 'Setup Wizard','gma-school' ), __( 'Setup Wizard','gma-school' ), 'manage_options', GMA_MENU_WIZARD, array( __CLASS__, 'page_wizard' ) );
	}

	// ── Page callbacks ────────────────────────────────────────────────────────
	public static function page_dashboard()       { self::view( 'dashboard/index' ); }
	public static function page_schools()         { self::view( 'manager/schools' ); }
	public static function page_sessions()        { self::view( 'manager/sessions' ); }
	public static function page_classes()         { self::view( 'manager/classes' ); }
	public static function page_settings()        { self::view( 'manager/settings' ); }
	public static function page_students()        { self::view( 'school/students' ); }
	public static function page_admissions()      { self::view( 'school/admissions' ); }
	public static function page_staff()           { self::view( 'school/staff' ); }
	public static function page_roles()           { self::view( 'school/roles' ); }
	public static function page_admins()          { self::view( 'school/admins' ); }
	public static function page_certificates()    { self::view( 'school/certificates' ); }
	public static function page_id_cards()        { self::view( 'school/id-cards' ); }
	public static function page_transfer_certs()  { self::view( 'school/transfer-certs' ); }
	public static function page_promote()         { self::view( 'school/promote' ); }
	public static function page_inquiries()       { self::view( 'school/inquiries' ); }
	public static function page_notifications()   { self::view( 'school/notifications' ); }
	public static function page_staff_attendance(){ self::view( 'school/staff-attendance' ); }
	public static function page_staff_leaves()    { self::view( 'school/staff-leaves' ); }
	public static function page_subjects()        { self::view( 'classes/subjects' ); }
	public static function page_attendance()      { self::view( 'classes/attendance' ); }
	public static function page_routines()        { self::view( 'classes/routines' ); }
	public static function page_homework()        { self::view( 'classes/homework' ); }
	public static function page_study_materials() { self::view( 'classes/study-materials' ); }
	public static function page_notices()         { self::view( 'classes/notices' ); }
	public static function page_events()          { self::view( 'classes/events' ); }
	public static function page_activities()      { self::view( 'classes/activities' ); }
	public static function page_meetings()        { self::view( 'classes/meetings' ); }
	public static function page_student_leaves()  { self::view( 'classes/student-leaves' ); }
	public static function page_exams()           { self::view( 'exams/exams' ); }
	public static function page_exam_groups()     { self::view( 'exams/exam-groups' ); }
	public static function page_results()         { self::view( 'exams/results' ); }
	public static function page_admit_cards()     { self::view( 'exams/admit-cards' ); }
	public static function page_assessments()     { self::view( 'exams/assessments' ); }
	public static function page_academic_report() { self::view( 'exams/academic-report' ); }
	public static function page_fees()            { self::view( 'accounting/fees' ); }
	public static function page_invoices()        { self::view( 'accounting/invoices' ); }
	public static function page_payments()        { self::view( 'accounting/payments' ); }
	public static function page_concessions()     { self::view( 'accounting/concessions' ); }
	public static function page_income()          { self::view( 'accounting/income' ); }
	public static function page_expenses()        { self::view( 'accounting/expenses' ); }
	public static function page_finance_reports() { self::view( 'accounting/finance-reports' ); }
	public static function page_library()         { self::view( 'library/index' ); }
	public static function page_hostel()          { self::view( 'hostel/index' ); }
	public static function page_transport()       { self::view( 'transport/index' ); }
	public static function page_chapters()        { self::view( 'lectures/chapters' ); }
	public static function page_lectures()        { self::view( 'lectures/lectures' ); }
	public static function page_tickets()         { self::view( 'support/tickets' ); }
	public static function page_reports()         { self::view( 'reports/index' ); }
	public static function page_logs()            { self::view( 'reports/logs' ); }
	public static function page_print()           { self::view( 'print/index' ); }
	public static function page_wizard()          { self::view( 'wizard/index' ); }

	private static function view( $path ) {
		$file = GMA_PLUGIN_DIR . 'admin/views/' . $path . '.php';
		if ( file_exists( $file ) ) {
			include $file;
		} else {
			echo '<div class="gma-wrap"><div class="gma-notice gma-notice-warning">';
			printf( esc_html__( 'View not found: %s', 'gma-school' ), esc_html( $path ) );
			echo '</div></div>';
		}
	}
}
