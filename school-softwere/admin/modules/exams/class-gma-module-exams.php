<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Exams {
	public static function init() {
		add_action( 'wp_ajax_gma_save_exam',        array( __CLASS__, 'save_exam' ) );
		add_action( 'wp_ajax_gma_delete_exam',       array( __CLASS__, 'delete_exam' ) );
		add_action( 'wp_ajax_gma_get_exam',          array( __CLASS__, 'get_exam' ) );
		add_action( 'wp_ajax_gma_save_exam_group',   array( __CLASS__, 'save_group' ) );
		add_action( 'wp_ajax_gma_delete_exam_group', array( __CLASS__, 'delete_group' ) );
		add_action( 'wp_ajax_gma_save_exam_paper',   array( __CLASS__, 'save_paper' ) );
		add_action( 'wp_ajax_gma_delete_exam_paper', array( __CLASS__, 'delete_paper' ) );
		add_action( 'wp_ajax_gma_get_exams',         array( __CLASS__, 'get_all' ) );
	}
	public static function save_exam() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$school_id = GMA_Helper::get_current_school_id();
		$data = array(
			'class_school_id' => GMA_Helper::post( 'class_school_id', 'int' ),
			'label'           => GMA_Helper::post( 'label' ),
			'session_id'      => GMA_Helper::post( 'session_id', 'int' ) ?: GMA_Helper::get_active_session_id( $school_id ),
			'exam_group_id'   => GMA_Helper::post( 'exam_group_id', 'int' ) ?: null,
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_EXAMS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Exam updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_EXAMS, $data ); wp_send_json_success( array( 'message' => __( 'Exam created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_exam() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_EXAMS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Exam deleted.', 'gma-school' ) ) );
	}
	public static function get_exam() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_EXAMS . " WHERE ID=%d", GMA_Helper::post( 'id', 'int' ) ) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
	public static function save_group() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$school_id = GMA_Helper::get_current_school_id();
		$data = array(
			'school_id'  => $school_id,
			'label'      => GMA_Helper::post( 'label' ),
			'session_id' => GMA_Helper::post( 'session_id', 'int' ) ?: GMA_Helper::get_active_session_id( $school_id ),
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_EXAMS_GROUP, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Group updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_EXAMS_GROUP, $data ); wp_send_json_success( array( 'message' => __( 'Group created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_group() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_EXAMS_GROUP, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Group deleted.', 'gma-school' ) ) );
	}
	public static function save_paper() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'exam_id'     => GMA_Helper::post( 'exam_id', 'int' ),
			'subject_id'  => GMA_Helper::post( 'subject_id', 'int' ),
			'date'        => GMA_Helper::post( 'date' ) ?: null,
			'start_time'  => GMA_Helper::post( 'start_time' ) ?: null,
			'end_time'    => GMA_Helper::post( 'end_time' ) ?: null,
			'total_marks' => GMA_Helper::post( 'total_marks', 'float' ) ?: 100,
			'pass_marks'  => GMA_Helper::post( 'pass_marks', 'float' ) ?: 40,
			'venue'       => GMA_Helper::post( 'venue' ),
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_EXAM_PAPERS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Paper updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_EXAM_PAPERS, $data ); wp_send_json_success( array( 'message' => __( 'Paper added.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_paper() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_EXAM_PAPERS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Paper deleted.', 'gma-school' ) ) );
	}
	public static function get_all() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$csid = GMA_Helper::post( 'class_school_id', 'int' );
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_EXAMS . " WHERE class_school_id=%d ORDER BY created_at DESC", $csid ) ) );
	}
}
GMA_Module_Exams::init();
