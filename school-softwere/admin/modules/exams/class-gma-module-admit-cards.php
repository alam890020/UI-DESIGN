<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Admit_Cards {
	public static function init() {
		add_action( 'wp_ajax_gma_generate_admit_cards', array( __CLASS__, 'generate' ) );
		add_action( 'wp_ajax_gma_get_admit_card',       array( __CLASS__, 'get_one' ) );
	}
	public static function generate() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$exam_id    = GMA_Helper::post( 'exam_id', 'int' );
		$student_ids = array_map( 'intval', (array) ( $_POST['student_ids'] ?? array() ) );
		if ( empty( $student_ids ) ) {
			$school_id = GMA_Helper::get_current_school_id();
			$exam      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_EXAMS . " WHERE ID=%d", $exam_id ) );
			if ( $exam ) {
				$student_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM " . GMA_TABLE_STUDENT_RECORDS . " WHERE class_school_id=%d AND is_active=1", $exam->class_school_id ) );
			}
		}
		$generated = 0;
		foreach ( $student_ids as $sid ) {
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . GMA_TABLE_ADMIT_CARDS . " WHERE exam_id=%d AND student_record_id=%d", $exam_id, $sid ) );
			if ( ! $exists ) {
				$card_number = 'AC-' . $exam_id . '-' . str_pad( $sid, 5, '0', STR_PAD_LEFT );
				$wpdb->insert( GMA_TABLE_ADMIT_CARDS, array( 'exam_id' => $exam_id, 'student_record_id' => $sid, 'admit_card_number' => $card_number, 'created_at' => current_time( 'mysql' ) ) );
				$generated++;
			}
		}
		wp_send_json_success( array( 'message' => sprintf( _n( '%d admit card generated.', '%d admit cards generated.', $generated, 'gma-school' ), $generated ) ) );
	}
	public static function get_one() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id  = GMA_Helper::post( 'id', 'int' );
		$row = $wpdb->get_row( $wpdb->prepare(
			"SELECT ac.*, CONCAT(sr.first_name,' ',sr.last_name) AS student_name, sr.admission_number, sr.roll_number, sr.photo, e.label AS exam_name, sc.label AS school_name FROM " . GMA_TABLE_ADMIT_CARDS . " ac INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON ac.student_record_id=sr.ID INNER JOIN " . GMA_TABLE_EXAMS . " e ON ac.exam_id=e.ID INNER JOIN " . GMA_TABLE_SCHOOLS . " sc ON sr.school_id=sc.ID WHERE ac.ID=%d", $id
		) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
}
GMA_Module_Admit_Cards::init();
