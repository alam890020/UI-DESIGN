<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Attendance {
	public static function init() {
		add_action( 'wp_ajax_gma_save_attendance',       array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_get_attendance',        array( __CLASS__, 'get_by_date' ) );
		add_action( 'wp_ajax_gma_get_attendance_report', array( __CLASS__, 'get_report' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$class_school_id = GMA_Helper::post( 'class_school_id', 'int' );
		$section_id      = GMA_Helper::post( 'section_id', 'int' );
		$date            = GMA_Helper::post( 'date' );
		$records         = isset( $_POST['attendance'] ) && is_array( $_POST['attendance'] ) ? $_POST['attendance'] : array();
		foreach ( $records as $student_id => $status ) {
			$student_id = (int) $student_id;
			$status     = sanitize_text_field( $status );
			$note       = sanitize_text_field( $_POST['note'][ $student_id ] ?? '' );
			$existing   = $wpdb->get_var( $wpdb->prepare(
				"SELECT ID FROM " . GMA_TABLE_ATTENDANCE . " WHERE student_record_id=%d AND date=%s",
				$student_id, $date
			) );
			$att_data   = array( 'status' => $status, 'note' => $note );
			if ( $existing ) { $wpdb->update( GMA_TABLE_ATTENDANCE, $att_data, array( 'ID' => $existing ) ); }
			else { $wpdb->insert( GMA_TABLE_ATTENDANCE, array_merge( $att_data, array( 'student_record_id' => $student_id, 'class_school_id' => $class_school_id, 'section_id' => $section_id, 'date' => $date ) ) ); }
		}
		GMA_Database::log( 'attendance_saved', "Date: $date, Class: $class_school_id" );
		wp_send_json_success( array( 'message' => __( 'Attendance saved.', 'gma-school' ) ) );
	}
	public static function get_by_date() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$csid = GMA_Helper::post( 'class_school_id', 'int' );
		$sec  = GMA_Helper::post( 'section_id', 'int' );
		$date = GMA_Helper::post( 'date' );
		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT a.student_record_id, a.status, a.note FROM " . GMA_TABLE_ATTENDANCE . " a WHERE a.class_school_id=%d AND a.section_id=%d AND a.date=%s",
			$csid, $sec, $date
		) );
		wp_send_json_success( $rows );
	}
	public static function get_report() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$student_id = GMA_Helper::post( 'student_record_id', 'int' );
		$from       = GMA_Helper::post( 'from_date' );
		$to         = GMA_Helper::post( 'to_date' );
		$rows       = $wpdb->get_results( $wpdb->prepare(
			"SELECT date, status, note FROM " . GMA_TABLE_ATTENDANCE . " WHERE student_record_id=%d AND date BETWEEN %s AND %s ORDER BY date",
			$student_id, $from, $to
		) );
		$summary = array( 'present' => 0, 'absent' => 0, 'late' => 0, 'total' => count( $rows ) );
		foreach ( $rows as $r ) {
			if ( isset( $summary[ $r->status ] ) ) $summary[ $r->status ]++;
		}
		wp_send_json_success( array( 'records' => $rows, 'summary' => $summary ) );
	}
}
GMA_Module_Attendance::init();
