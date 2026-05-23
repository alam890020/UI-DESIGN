<?php
/**
 * SS_Exam — Examination AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Exam {

	public static function init() {
		add_action( 'wp_ajax_ss_save_exam',         array( __CLASS__, 'save_exam' ) );
		add_action( 'wp_ajax_ss_delete_exam',       array( __CLASS__, 'delete_exam' ) );
		add_action( 'wp_ajax_ss_save_exam_paper',   array( __CLASS__, 'save_exam_paper' ) );
		add_action( 'wp_ajax_ss_save_exam_result',  array( __CLASS__, 'save_exam_result' ) );
		add_action( 'wp_ajax_ss_bulk_save_results', array( __CLASS__, 'bulk_save_results' ) );
		add_action( 'wp_ajax_ss_save_exam_group',   array( __CLASS__, 'save_exam_group' ) );
		add_action( 'wp_ajax_ss_get_exam_papers',   array( __CLASS__, 'get_exam_papers' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
	}

	public static function save_exam() {
		self::verify();
		global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array(
			'class_school_id' => SS_Helper::post( 'class_school_id', 'int' ),
			'label'           => SS_Helper::post( 'label' ),
			'session_id'      => SS_Helper::post( 'session_id', 'int' ),
			'exam_group_id'   => SS_Helper::post( 'exam_group_id', 'int' ) ?: null,
		);
		if ( $id ) {
			$wpdb->update( SS_TABLE_EXAMS, $data, array( 'ID' => $id ) );
			wp_send_json( array( 'success' => true, 'message' => __( 'Exam updated.', 'school-softwere' ) ) );
		} else {
			$data['created_at'] = current_time( 'mysql' );
			$wpdb->insert( SS_TABLE_EXAMS, $data );
			wp_send_json( array( 'success' => true, 'message' => __( 'Exam created.', 'school-softwere' ), 'data' => array( 'id' => $wpdb->insert_id ) ) );
		}
	}

	public static function delete_exam() {
		self::verify();
		global $wpdb;
		$id = SS_Helper::post( 'id', 'int' );
		$wpdb->delete( SS_TABLE_EXAMS, array( 'ID' => $id ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Exam deleted.', 'school-softwere' ) ) );
	}

	public static function save_exam_paper() {
		self::verify();
		global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array(
			'exam_id'     => SS_Helper::post( 'exam_id', 'int' ),
			'subject_id'  => SS_Helper::post( 'subject_id', 'int' ),
			'date'        => SS_Helper::post( 'date' ) ?: null,
			'start_time'  => SS_Helper::post( 'start_time' ) ?: null,
			'end_time'    => SS_Helper::post( 'end_time' ) ?: null,
			'total_marks' => SS_Helper::post( 'total_marks', 'float' ),
			'pass_marks'  => SS_Helper::post( 'pass_marks', 'float' ),
		);
		if ( $id ) {
			$wpdb->update( SS_TABLE_EXAM_PAPERS, $data, array( 'ID' => $id ) );
		} else {
			$data['created_at'] = current_time( 'mysql' );
			$wpdb->insert( SS_TABLE_EXAM_PAPERS, $data );
		}
		wp_send_json( array( 'success' => true, 'message' => __( 'Exam paper saved.', 'school-softwere' ) ) );
	}

	public static function save_exam_result() {
		self::verify();
		global $wpdb;
		$paper_id   = SS_Helper::post( 'exam_paper_id', 'int' );
		$student_id = SS_Helper::post( 'student_record_id', 'int' );
		$obtained   = SS_Helper::post( 'obtained_marks', 'float' );
		$remarks    = SS_Helper::post( 'remarks' );

		// Fetch paper to compute grade
		$paper = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . SS_TABLE_EXAM_PAPERS . " WHERE ID=%d", $paper_id ) );
		$grade = $paper ? SS_Helper::calculate_grade( $obtained, $paper->total_marks ) : '';

		$existing = $wpdb->get_var( $wpdb->prepare(
			"SELECT ID FROM " . SS_TABLE_EXAM_RESULTS . " WHERE exam_paper_id=%d AND student_record_id=%d",
			$paper_id, $student_id
		) );
		$row_data = array(
			'exam_paper_id'     => $paper_id,
			'student_record_id' => $student_id,
			'obtained_marks'    => $obtained,
			'grade'             => $grade,
			'remarks'           => $remarks,
			'created_at'        => current_time( 'mysql' ),
		);
		if ( $existing ) {
			$wpdb->update( SS_TABLE_EXAM_RESULTS, $row_data, array( 'ID' => (int) $existing ) );
		} else {
			$wpdb->insert( SS_TABLE_EXAM_RESULTS, $row_data );
		}
		wp_send_json( array( 'success' => true, 'message' => __( 'Result saved.', 'school-softwere' ), 'data' => array( 'grade' => $grade ) ) );
	}

	public static function bulk_save_results() {
		self::verify();
		global $wpdb;
		$results = isset( $_POST['results'] ) ? (array) $_POST['results'] : array();
		$saved   = 0;
		foreach ( $results as $r ) {
			$paper_id   = (int) ( $r['exam_paper_id'] ?? 0 );
			$student_id = (int) ( $r['student_record_id'] ?? 0 );
			$obtained   = (float) ( $r['obtained_marks'] ?? 0 );
			if ( ! $paper_id || ! $student_id ) continue;
			$paper = $wpdb->get_row( $wpdb->prepare( "SELECT total_marks FROM " . SS_TABLE_EXAM_PAPERS . " WHERE ID=%d", $paper_id ) );
			$grade = $paper ? SS_Helper::calculate_grade( $obtained, $paper->total_marks ) : '';
			$existing = $wpdb->get_var( $wpdb->prepare(
				"SELECT ID FROM " . SS_TABLE_EXAM_RESULTS . " WHERE exam_paper_id=%d AND student_record_id=%d",
				$paper_id, $student_id
			) );
			$row = array( 'exam_paper_id' => $paper_id, 'student_record_id' => $student_id, 'obtained_marks' => $obtained, 'grade' => $grade, 'created_at' => current_time( 'mysql' ) );
			$existing ? $wpdb->update( SS_TABLE_EXAM_RESULTS, $row, array( 'ID' => (int) $existing ) ) : $wpdb->insert( SS_TABLE_EXAM_RESULTS, $row );
			$saved++;
		}
		wp_send_json( array( 'success' => true, 'message' => sprintf( __( '%d results saved.', 'school-softwere' ), $saved ) ) );
	}

	public static function save_exam_group() {
		self::verify();
		global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array(
			'school_id'  => SS_Helper::post( 'school_id', 'int' ),
			'label'      => SS_Helper::post( 'label' ),
			'session_id' => SS_Helper::post( 'session_id', 'int' ),
		);
		if ( $id ) {
			$wpdb->update( SS_TABLE_EXAMS_GROUP, $data, array( 'ID' => $id ) );
		} else {
			$data['created_at'] = current_time( 'mysql' );
			$wpdb->insert( SS_TABLE_EXAMS_GROUP, $data );
		}
		wp_send_json( array( 'success' => true, 'message' => __( 'Exam group saved.', 'school-softwere' ) ) );
	}

	public static function get_exam_papers() {
		self::verify();
		global $wpdb;
		$exam_id = SS_Helper::post( 'exam_id', 'int' );
		$papers  = $wpdb->get_results( $wpdb->prepare(
			"SELECT ep.*, s.label AS subject_label FROM " . SS_TABLE_EXAM_PAPERS . " ep
			 LEFT JOIN " . SS_TABLE_SUBJECTS . " s ON ep.subject_id=s.ID
			 WHERE ep.exam_id=%d ORDER BY ep.date, ep.start_time",
			$exam_id
		) );
		wp_send_json( array( 'success' => true, 'data' => $papers ) );
	}
}

SS_Exam::init();
