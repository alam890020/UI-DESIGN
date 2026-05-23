<?php
/**
 * SS_Lecture — Lectures & study materials AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Lecture {
	public static function init() {
		add_action( 'wp_ajax_ss_save_chapter',        array( __CLASS__, 'save_chapter' ) );
		add_action( 'wp_ajax_ss_delete_chapter',      array( __CLASS__, 'delete_chapter' ) );
		add_action( 'wp_ajax_ss_save_lecture',        array( __CLASS__, 'save_lecture' ) );
		add_action( 'wp_ajax_ss_delete_lecture',      array( __CLASS__, 'delete_lecture' ) );
		add_action( 'wp_ajax_ss_save_study_material', array( __CLASS__, 'save_study_material' ) );
		add_action( 'wp_ajax_ss_delete_study_material',array( __CLASS__, 'delete_study_material' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
	}

	public static function save_chapter() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'subject_id' => SS_Helper::post( 'subject_id', 'int' ), 'class_school_id' => SS_Helper::post( 'class_school_id', 'int' ), 'label' => SS_Helper::post( 'label' ), 'description' => SS_Helper::post( 'description', 'html' ), 'order' => SS_Helper::post( 'order', 'int' ) ?: 0, 'created_at' => current_time( 'mysql' ) );
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_CHAPTER, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_CHAPTER, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Chapter saved.', 'school-softwere' ) ) );
	}

	public static function delete_chapter() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_CHAPTER, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Chapter deleted.', 'school-softwere' ) ) );
	}

	public static function save_lecture() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'chapter_id' => SS_Helper::post( 'chapter_id', 'int' ), 'staff_id' => SS_Helper::post( 'staff_id', 'int' ) ?: null, 'title' => SS_Helper::post( 'title' ), 'description' => SS_Helper::post( 'description', 'html' ), 'video_url' => esc_url_raw( SS_Helper::post( 'video_url' ) ), 'created_at' => current_time( 'mysql' ) );
		if ( ! empty( $_FILES['attachment']['name'] ) ) { $u = SS_Helper::handle_upload( $_FILES['attachment'], 'school-softwere/lectures' ); if ( ! is_wp_error( $u ) ) $data['attachment'] = $u; }
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_LECTURE, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_LECTURE, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Lecture saved.', 'school-softwere' ) ) );
	}

	public static function delete_lecture() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_LECTURE, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Lecture deleted.', 'school-softwere' ) ) );
	}

	public static function save_study_material() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'class_school_id' => SS_Helper::post( 'class_school_id', 'int' ), 'section_id' => SS_Helper::post( 'section_id', 'int' ) ?: null, 'subject_id' => SS_Helper::post( 'subject_id', 'int' ) ?: null, 'title' => SS_Helper::post( 'title' ), 'description' => SS_Helper::post( 'description', 'html' ), 'file_type' => SS_Helper::post( 'file_type' ), 'created_at' => current_time( 'mysql' ) );
		if ( ! empty( $_FILES['file']['name'] ) ) { $u = SS_Helper::handle_upload( $_FILES['file'], 'school-softwere/materials' ); if ( ! is_wp_error( $u ) ) $data['file'] = $u; }
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_STUDY_MATERIALS, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_STUDY_MATERIALS, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Study material saved.', 'school-softwere' ) ) );
	}

	public static function delete_study_material() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_STUDY_MATERIALS, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Material deleted.', 'school-softwere' ) ) );
	}
}
SS_Lecture::init();
