<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Results {
	public static function init() {
		add_action( 'wp_ajax_gma_save_results',    array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_get_results',     array( __CLASS__, 'get_all' ) );
		add_action( 'wp_ajax_gma_get_report_card', array( __CLASS__, 'report_card' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$paper_id = GMA_Helper::post( 'exam_paper_id', 'int' );
		$results  = isset( $_POST['results'] ) && is_array( $_POST['results'] ) ? $_POST['results'] : array();
		$saved    = 0;
		foreach ( $results as $student_id => $marks ) {
			$student_id = (int) $student_id;
			$marks      = (float) $marks;
			$grade      = sanitize_text_field( $_POST['grade'][ $student_id ] ?? '' );
			$remarks    = sanitize_text_field( $_POST['remarks'][ $student_id ] ?? '' );
			$existing   = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . GMA_TABLE_EXAM_RESULTS . " WHERE exam_paper_id=%d AND student_record_id=%d", $paper_id, $student_id ) );
			$data       = array( 'obtained_marks' => $marks, 'grade' => $grade, 'remarks' => $remarks );
			if ( $existing ) { $wpdb->update( GMA_TABLE_EXAM_RESULTS, $data, array( 'ID' => $existing ) ); }
			else { $wpdb->insert( GMA_TABLE_EXAM_RESULTS, array_merge( $data, array( 'exam_paper_id' => $paper_id, 'student_record_id' => $student_id, 'created_at' => current_time( 'mysql' ) ) ) ); }
			$saved++;
		}
		GMA_Database::log( 'results_saved', "Paper ID: $paper_id, Count: $saved" );
		wp_send_json_success( array( 'message' => sprintf( _n( '%d result saved.', '%d results saved.', $saved, 'gma-school' ), $saved ) ) );
	}
	public static function get_all() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$paper_id = GMA_Helper::post( 'exam_paper_id', 'int' );
		$rows     = $wpdb->get_results( $wpdb->prepare(
			"SELECT er.*, CONCAT(sr.first_name,' ',sr.last_name) AS student_name, sr.admission_number, sr.roll_number FROM " . GMA_TABLE_EXAM_RESULTS . " er INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON er.student_record_id=sr.ID WHERE er.exam_paper_id=%d ORDER BY sr.roll_number",
			$paper_id
		) );
		wp_send_json_success( $rows );
	}
	public static function report_card() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$student_id = GMA_Helper::post( 'student_record_id', 'int' );
		$exam_id    = GMA_Helper::post( 'exam_id', 'int' );
		$rows       = $wpdb->get_results( $wpdb->prepare(
			"SELECT er.*, sub.label AS subject, ep.total_marks, ep.pass_marks, ep.date FROM " . GMA_TABLE_EXAM_RESULTS . " er INNER JOIN " . GMA_TABLE_EXAM_PAPERS . " ep ON er.exam_paper_id=ep.ID INNER JOIN " . GMA_TABLE_SUBJECTS . " sub ON ep.subject_id=sub.ID WHERE er.student_record_id=%d AND ep.exam_id=%d ORDER BY sub.label",
			$student_id, $exam_id
		) );
		$total_obtained = array_sum( array_column( $rows, 'obtained_marks' ) );
		$total_max      = array_sum( array_column( $rows, 'total_marks' ) );
		$percentage     = $total_max > 0 ? round( ( $total_obtained / $total_max ) * 100, 2 ) : 0;
		wp_send_json_success( array( 'subjects' => $rows, 'total_obtained' => $total_obtained, 'total_max' => $total_max, 'percentage' => $percentage ) );
	}
}
GMA_Module_Results::init();
