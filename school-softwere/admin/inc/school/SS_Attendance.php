<?php
/**
 * SS_Attendance — Attendance AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Attendance {

	public static function init() {
		add_action( 'wp_ajax_ss_save_attendance',       array( __CLASS__, 'save_attendance' ) );
		add_action( 'wp_ajax_ss_get_attendance',        array( __CLASS__, 'get_attendance' ) );
		add_action( 'wp_ajax_ss_save_staff_attendance', array( __CLASS__, 'save_staff_attendance' ) );
		add_action( 'wp_ajax_ss_bulk_attendance',       array( __CLASS__, 'bulk_attendance' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
	}

	public static function save_attendance() {
		self::verify();
		global $wpdb;
		$records = isset( $_POST['attendance'] ) ? (array) $_POST['attendance'] : array();
		$date    = sanitize_text_field( $_POST['date'] ?? current_time( 'Y-m-d' ) );
		$saved   = 0;

		foreach ( $records as $rec ) {
			$student_id      = (int) ( $rec['student_record_id'] ?? 0 );
			$status          = sanitize_text_field( $rec['status'] ?? 'present' );
			$note            = sanitize_text_field( $rec['note'] ?? '' );
			$class_school_id = (int) ( $rec['class_school_id'] ?? 0 );
			$section_id      = (int) ( $rec['section_id'] ?? 0 );

			if ( ! $student_id ) continue;

			$existing = $wpdb->get_var( $wpdb->prepare(
				"SELECT ID FROM " . SS_TABLE_ATTENDANCE . " WHERE student_record_id=%d AND date=%s",
				$student_id, $date
			) );

			$row = array(
				'student_record_id' => $student_id,
				'class_school_id'   => $class_school_id,
				'section_id'        => $section_id,
				'date'              => $date,
				'status'            => $status,
				'note'              => $note,
			);
			$existing ? $wpdb->update( SS_TABLE_ATTENDANCE, $row, array( 'ID' => (int) $existing ) ) : $wpdb->insert( SS_TABLE_ATTENDANCE, $row );
			$saved++;
		}
		SS_Database::log( 'attendance_saved', 'Date: ' . $date . ', Records: ' . $saved );
		wp_send_json( array( 'success' => true, 'message' => sprintf( __( 'Attendance saved for %d students.', 'school-softwere' ), $saved ) ) );
	}

	public static function get_attendance() {
		self::verify();
		global $wpdb;
		$class_school_id = SS_Helper::post( 'class_school_id', 'int' );
		$section_id      = SS_Helper::post( 'section_id', 'int' );
		$date            = SS_Helper::post( 'date' ) ?: current_time( 'Y-m-d' );

		$students = $wpdb->get_results( $wpdb->prepare(
			"SELECT sr.ID, sr.first_name, sr.last_name, sr.roll_number, sr.photo,
			        a.status, a.note
			 FROM " . SS_TABLE_STUDENT_RECORDS . " sr
			 LEFT JOIN " . SS_TABLE_ATTENDANCE . " a ON a.student_record_id=sr.ID AND a.date=%s
			 WHERE sr.class_school_id=%d AND sr.section_id=%d AND sr.is_active=1
			 ORDER BY sr.roll_number, sr.first_name",
			$date, $class_school_id, $section_id
		) );
		wp_send_json( array( 'success' => true, 'data' => $students ) );
	}

	public static function save_staff_attendance() {
		self::verify();
		global $wpdb;
		$records   = isset( $_POST['attendance'] ) ? (array) $_POST['attendance'] : array();
		$date      = sanitize_text_field( $_POST['date'] ?? current_time( 'Y-m-d' ) );
		$school_id = SS_Helper::post( 'school_id', 'int' );
		$saved     = 0;

		foreach ( $records as $rec ) {
			$staff_id = (int) ( $rec['staff_id'] ?? 0 );
			$status   = sanitize_text_field( $rec['status'] ?? 'present' );
			$note     = sanitize_text_field( $rec['note'] ?? '' );
			if ( ! $staff_id ) continue;

			$existing = $wpdb->get_var( $wpdb->prepare(
				"SELECT ID FROM " . SS_TABLE_STAFF_ATTENDANCE . " WHERE staff_id=%d AND date=%s",
				$staff_id, $date
			) );
			$row = array( 'staff_id' => $staff_id, 'school_id' => $school_id, 'date' => $date, 'status' => $status, 'note' => $note );
			$existing ? $wpdb->update( SS_TABLE_STAFF_ATTENDANCE, $row, array( 'ID' => (int) $existing ) ) : $wpdb->insert( SS_TABLE_STAFF_ATTENDANCE, $row );
			$saved++;
		}
		wp_send_json( array( 'success' => true, 'message' => sprintf( __( 'Staff attendance saved for %d members.', 'school-softwere' ), $saved ) ) );
	}

	public static function bulk_attendance() {
		self::verify();
		global $wpdb;
		$class_school_id = SS_Helper::post( 'class_school_id', 'int' );
		$section_id      = SS_Helper::post( 'section_id', 'int' );
		$date            = SS_Helper::post( 'date' ) ?: current_time( 'Y-m-d' );
		$status          = SS_Helper::post( 'status' ) ?: 'present';

		$students = $wpdb->get_results( $wpdb->prepare(
			"SELECT ID, class_school_id, section_id FROM " . SS_TABLE_STUDENT_RECORDS . " WHERE class_school_id=%d AND section_id=%d AND is_active=1",
			$class_school_id, $section_id
		) );
		$saved = 0;
		foreach ( $students as $st ) {
			$existing = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . SS_TABLE_ATTENDANCE . " WHERE student_record_id=%d AND date=%s", $st->ID, $date ) );
			$row = array( 'student_record_id' => $st->ID, 'class_school_id' => $class_school_id, 'section_id' => $section_id, 'date' => $date, 'status' => $status, 'note' => '' );
			$existing ? $wpdb->update( SS_TABLE_ATTENDANCE, $row, array( 'ID' => (int) $existing ) ) : $wpdb->insert( SS_TABLE_ATTENDANCE, $row );
			$saved++;
		}
		wp_send_json( array( 'success' => true, 'message' => sprintf( __( 'Marked %d students as %s.', 'school-softwere' ), $saved, $status ) ) );
	}
}

SS_Attendance::init();
