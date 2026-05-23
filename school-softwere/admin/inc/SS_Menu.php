<?php
/**
 * SS_Menu — Register all WordPress admin menus for School Softwere.
 *
 * @package School_Softwere
 */

defined( 'ABSPATH' ) || die();

class SS_Menu {

	/** Boot hooks */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_setup_redirect' ) );
	}

	/** Redirect to setup wizard on first activation */
	public static function handle_setup_redirect() {
		if ( get_option( 'ss_activation_redirect' ) ) {
			delete_option( 'ss_activation_redirect' );
			if ( ! isset( $_GET['activate-multi'] ) ) {
				wp_safe_redirect( admin_url( 'admin.php?page=' . SS_MENU_WIZARD ) );
				exit;
			}
		}
	}

	/** Register all menus */
	public static function register_menus() {
		$icon = 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>' );

		// ── Top-level menu
		add_menu_page(
			__( 'School Softwere', 'school-softwere' ),
			__( 'School Softwere', 'school-softwere' ),
			'read',
			SS_MENU_DASHBOARD,
			array( __CLASS__, 'page_dashboard' ),
			$icon,
			2
		);

		// ── Dashboard
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Dashboard', 'school-softwere' ), __( 'Dashboard', 'school-softwere' ), 'read', SS_MENU_DASHBOARD, array( __CLASS__, 'page_dashboard' ) );

		// ── Schools (super-admin only)
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Schools', 'school-softwere' ), __( '🏫 Schools', 'school-softwere' ), 'manage_options', SS_MENU_SCHOOLS, array( __CLASS__, 'page_schools' ) );

		// ── Students
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Students', 'school-softwere' ), __( '🎓 Students', 'school-softwere' ), 'read', SS_MENU_STUDENTS, array( __CLASS__, 'page_students' ) );

		// ── Staff
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Staff', 'school-softwere' ), __( '👩‍🏫 Staff', 'school-softwere' ), 'read', SS_MENU_STAFF, array( __CLASS__, 'page_staff' ) );

		// ── Classes
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Classes', 'school-softwere' ), __( '📚 Classes', 'school-softwere' ), 'read', SS_MENU_CLASSES, array( __CLASS__, 'page_classes' ) );

		// ── Subjects
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Subjects', 'school-softwere' ), __( '📖 Subjects', 'school-softwere' ), 'read', SS_MENU_SUBJECTS, array( __CLASS__, 'page_subjects' ) );

		// ── Sessions
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Sessions', 'school-softwere' ), __( '📅 Sessions', 'school-softwere' ), 'manage_options', SS_MENU_SESSIONS, array( __CLASS__, 'page_sessions' ) );

		// ── Examinations
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Examinations', 'school-softwere' ), __( '📝 Examinations', 'school-softwere' ), 'read', SS_MENU_EXAMS, array( __CLASS__, 'page_exams' ) );

		// ── Fees
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Fees', 'school-softwere' ), __( '💰 Fees', 'school-softwere' ), 'read', SS_MENU_FEES, array( __CLASS__, 'page_fees' ) );

		// ── Attendance
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Attendance', 'school-softwere' ), __( '✅ Attendance', 'school-softwere' ), 'read', SS_MENU_ATTENDANCE, array( __CLASS__, 'page_attendance' ) );

		// ── Library
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Library', 'school-softwere' ), __( '📖 Library', 'school-softwere' ), 'read', SS_MENU_LIBRARY, array( __CLASS__, 'page_library' ) );

		// ── Transport
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Transport', 'school-softwere' ), __( '🚌 Transport', 'school-softwere' ), 'read', SS_MENU_TRANSPORT, array( __CLASS__, 'page_transport' ) );

		// ── Hostel
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Hostel', 'school-softwere' ), __( '🏠 Hostel', 'school-softwere' ), 'read', SS_MENU_HOSTEL, array( __CLASS__, 'page_hostel' ) );

		// ── Notices
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Notices', 'school-softwere' ), __( '📢 Notices', 'school-softwere' ), 'read', SS_MENU_NOTICES, array( __CLASS__, 'page_notices' ) );

		// ── Events
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Events', 'school-softwere' ), __( '🗓️ Events', 'school-softwere' ), 'read', SS_MENU_EVENTS, array( __CLASS__, 'page_events' ) );

		// ── Homework
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Homework', 'school-softwere' ), __( '📓 Homework', 'school-softwere' ), 'read', SS_MENU_HOMEWORK, array( __CLASS__, 'page_homework' ) );

		// ── Lectures
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Lectures', 'school-softwere' ), __( '🎥 Lectures', 'school-softwere' ), 'read', SS_MENU_LECTURES, array( __CLASS__, 'page_lectures' ) );

		// ── Leaves
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Leaves', 'school-softwere' ), __( '🗓️ Leaves', 'school-softwere' ), 'read', SS_MENU_LEAVES, array( __CLASS__, 'page_leaves' ) );

		// ── Reports
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Reports', 'school-softwere' ), __( '📊 Reports', 'school-softwere' ), 'read', SS_MENU_REPORTS, array( __CLASS__, 'page_reports' ) );

		// ── Tickets
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Support Tickets', 'school-softwere' ), __( '🎫 Support', 'school-softwere' ), 'read', SS_MENU_TICKETS, array( __CLASS__, 'page_tickets' ) );

		// ── Activity Logs
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Activity Logs', 'school-softwere' ), __( '📋 Activity Logs', 'school-softwere' ), 'manage_options', SS_MENU_LOGS, array( __CLASS__, 'page_logs' ) );

		// ── Settings
		add_submenu_page( SS_MENU_DASHBOARD, __( 'Settings', 'school-softwere' ), __( '⚙️ Settings', 'school-softwere' ), 'manage_options', SS_MENU_SETTINGS, array( __CLASS__, 'page_settings' ) );

		// ── Setup Wizard (hidden from menu)
		add_submenu_page( null, __( 'Setup Wizard', 'school-softwere' ), __( 'Setup Wizard', 'school-softwere' ), 'manage_options', SS_MENU_WIZARD, array( __CLASS__, 'page_wizard' ) );
	}

	// ── Page callbacks ────────────────────────────────────────────────

	public static function page_dashboard()  { self::load_view( 'dashboard/index' ); }
	public static function page_schools()    { self::load_view( 'manager/schools/index' ); }
	public static function page_students()   { self::load_view( 'school/students/index' ); }
	public static function page_staff()      { self::load_view( 'school/staff/index' ); }
	public static function page_classes()    { self::load_view( 'school/classes/index' ); }
	public static function page_subjects()   { self::load_view( 'school/subjects/index' ); }
	public static function page_sessions()   { self::load_view( 'manager/sessions/index' ); }
	public static function page_exams()      { self::load_view( 'school/exams/index' ); }
	public static function page_fees()       { self::load_view( 'school/fees/index' ); }
	public static function page_attendance() { self::load_view( 'school/attendance/index' ); }
	public static function page_library()    { self::load_view( 'school/library/index' ); }
	public static function page_transport()  { self::load_view( 'school/transport/index' ); }
	public static function page_hostel()     { self::load_view( 'school/hostel/index' ); }
	public static function page_notices()    { self::load_view( 'school/notices/index' ); }
	public static function page_events()     { self::load_view( 'school/events/index' ); }
	public static function page_homework()   { self::load_view( 'school/homework/index' ); }
	public static function page_lectures()   { self::load_view( 'school/lectures/index' ); }
	public static function page_leaves()     { self::load_view( 'school/leaves/index' ); }
	public static function page_reports()    { self::load_view( 'school/reports/index' ); }
	public static function page_tickets()    { self::load_view( 'school/tickets/index' ); }
	public static function page_logs()       { self::load_view( 'manager/logs/index' ); }
	public static function page_settings()   { self::load_view( 'manager/settings/index' ); }
	public static function page_wizard()     { self::load_view( 'school/wizard/index' ); }

	/**
	 * Load a view file from admin/views/.
	 *
	 * @param string $path  Relative path (no .php).
	 */
	private static function load_view( $path ) {
		$file = SS_PLUGIN_DIR . 'admin/views/' . $path . '.php';
		if ( file_exists( $file ) ) {
			include $file;
		} else {
			echo '<div class="ss-wrap"><div class="ss-notice ss-notice-warning">';
			/* translators: %s: file path */
			printf( esc_html__( 'View not found: %s', 'school-softwere' ), esc_html( $path ) );
			echo '</div></div>';
		}
	}
}
