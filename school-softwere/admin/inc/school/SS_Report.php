<?php
/**
 * SS_Report — Reports & analytics AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Report {
	public static function init() {
		add_action( 'wp_ajax_ss_get_financial_report',  array( __CLASS__, 'financial_report' ) );
		add_action( 'wp_ajax_ss_get_attendance_report', array( __CLASS__, 'attendance_report' ) );
		add_action( 'wp_ajax_ss_get_student_report',    array( __CLASS__, 'student_report' ) );
		add_action( 'wp_ajax_ss_get_exam_report',       array( __CLASS__, 'exam_report' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
	}

	public static function financial_report() {
		self::verify(); global $wpdb;
		$school_id  = SS_Helper::post( 'school_id', 'int' );
		$from       = SS_Helper::post( 'from_date' ) ?: date( 'Y-01-01' );
		$to         = SS_Helper::post( 'to_date' )   ?: current_time( 'Y-m-d' );

		$total_collected = (float) $wpdb->get_var( $wpdb->prepare(
			"SELECT COALESCE(SUM(p.amount),0) FROM " . SS_TABLE_PAYMENTS . " p INNER JOIN " . SS_TABLE_INVOICES . " i ON p.invoice_id=i.ID WHERE i.school_id=%d AND p.payment_date BETWEEN %s AND %s",
			$school_id, $from, $to
		) );
		$total_pending = (float) $wpdb->get_var( $wpdb->prepare(
			"SELECT COALESCE(SUM(due_amount),0) FROM " . SS_TABLE_INVOICES . " WHERE school_id=%d AND status!='paid'",
			$school_id
		) );
		$total_expense = (float) $wpdb->get_var( $wpdb->prepare(
			"SELECT COALESCE(SUM(amount),0) FROM " . SS_TABLE_EXPENSES . " WHERE school_id=%d AND date BETWEEN %s AND %s",
			$school_id, $from, $to
		) );
		$total_income = (float) $wpdb->get_var( $wpdb->prepare(
			"SELECT COALESCE(SUM(amount),0) FROM " . SS_TABLE_INCOME . " WHERE school_id=%d AND date BETWEEN %s AND %s",
			$school_id, $from, $to
		) );

		// Monthly breakdown
		$monthly = $wpdb->get_results( $wpdb->prepare(
			"SELECT DATE_FORMAT(p.payment_date,'%%Y-%%m') AS month, SUM(p.amount) AS collected
			 FROM " . SS_TABLE_PAYMENTS . " p INNER JOIN " . SS_TABLE_INVOICES . " i ON p.invoice_id=i.ID
			 WHERE i.school_id=%d AND p.payment_date BETWEEN %s AND %s
			 GROUP BY month ORDER BY month",
			$school_id, $from, $to
		) );

		wp_send_json( array( 'success' => true, 'data' => compact( 'total_collected', 'total_pending', 'total_expense', 'total_income', 'monthly' ) ) );
	}

	public static function attendance_report() {
		self::verify(); global $wpdb;
		$school_id  = SS_Helper::post( 'school_id', 'int' );
		$from       = SS_Helper::post( 'from_date' ) ?: date( 'Y-m-01' );
		$to         = SS_Helper::post( 'to_date' )   ?: current_time( 'Y-m-d' );
		$class_school_id = SS_Helper::post( 'class_school_id', 'int' );

		$q = "SELECT sr.ID, sr.first_name, sr.last_name, sr.admission_number,
			         COUNT(CASE WHEN a.status='present' THEN 1 END) AS present,
			         COUNT(CASE WHEN a.status='absent'  THEN 1 END) AS absent,
			         COUNT(CASE WHEN a.status='late'    THEN 1 END) AS late,
			         COUNT(a.ID) AS total_days
			  FROM " . SS_TABLE_STUDENT_RECORDS . " sr
			  LEFT JOIN " . SS_TABLE_ATTENDANCE . " a ON a.student_record_id=sr.ID AND a.date BETWEEN %s AND %s
			  WHERE sr.school_id=%d AND sr.is_active=1";
		$args = array( $from, $to, $school_id );
		if ( $class_school_id ) { $q .= " AND sr.class_school_id=%d"; $args[] = $class_school_id; }
		$q .= " GROUP BY sr.ID ORDER BY sr.first_name";
		$rows = $wpdb->get_results( $wpdb->prepare( $q, $args ) ); // phpcs:ignore
		wp_send_json( array( 'success' => true, 'data' => $rows ) );
	}

	public static function student_report() {
		self::verify(); global $wpdb;
		$school_id = SS_Helper::post( 'school_id', 'int' );
		$total     = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . SS_TABLE_STUDENT_RECORDS . " WHERE school_id=%d AND is_active=1", $school_id ) );
		$male      = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . SS_TABLE_STUDENT_RECORDS . " WHERE school_id=%d AND is_active=1 AND gender='male'", $school_id ) );
		$female    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . SS_TABLE_STUDENT_RECORDS . " WHERE school_id=%d AND is_active=1 AND gender='female'", $school_id ) );
		$by_class  = $wpdb->get_results( $wpdb->prepare(
			"SELECT c.label AS class_label, COUNT(sr.ID) AS total FROM " . SS_TABLE_STUDENT_RECORDS . " sr INNER JOIN " . SS_TABLE_CLASS_SCHOOL . " cs ON sr.class_school_id=cs.ID INNER JOIN " . SS_TABLE_CLASSES . " c ON cs.class_id=c.ID WHERE sr.school_id=%d AND sr.is_active=1 GROUP BY cs.ID ORDER BY c.label",
			$school_id
		) );
		wp_send_json( array( 'success' => true, 'data' => compact( 'total', 'male', 'female', 'by_class' ) ) );
	}

	public static function exam_report() {
		self::verify(); global $wpdb;
		$exam_id = SS_Helper::post( 'exam_id', 'int' );
		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT sr.first_name, sr.last_name, sr.admission_number, sr.roll_number,
			        SUM(er.obtained_marks) AS total_obtained,
			        SUM(ep.total_marks) AS total_marks,
			        SUM(ep.pass_marks) AS total_pass,
			        GROUP_CONCAT(sub.label,':', er.obtained_marks,'/', ep.total_marks SEPARATOR ' | ') AS subject_wise
			 FROM " . SS_TABLE_EXAM_RESULTS . " er
			 INNER JOIN " . SS_TABLE_EXAM_PAPERS . " ep ON er.exam_paper_id=ep.ID
			 INNER JOIN " . SS_TABLE_EXAMS . " e ON ep.exam_id=e.ID
			 INNER JOIN " . SS_TABLE_STUDENT_RECORDS . " sr ON er.student_record_id=sr.ID
			 INNER JOIN " . SS_TABLE_SUBJECTS . " sub ON ep.subject_id=sub.ID
			 WHERE ep.exam_id=%d
			 GROUP BY er.student_record_id ORDER BY total_obtained DESC",
			$exam_id
		) );
		wp_send_json( array( 'success' => true, 'data' => $results ) );
	}
}
SS_Report::init();
