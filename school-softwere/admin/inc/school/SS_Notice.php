<?php
/**
 * SS_Notice — Noticeboard, Events, Homework AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Notice {
	public static function init() {
		add_action( 'wp_ajax_ss_save_notice',     array( __CLASS__, 'save_notice' ) );
		add_action( 'wp_ajax_ss_delete_notice',   array( __CLASS__, 'delete_notice' ) );
		add_action( 'wp_ajax_ss_save_event',      array( __CLASS__, 'save_event' ) );
		add_action( 'wp_ajax_ss_delete_event',    array( __CLASS__, 'delete_event' ) );
		add_action( 'wp_ajax_ss_save_homework',   array( __CLASS__, 'save_homework' ) );
		add_action( 'wp_ajax_ss_delete_homework', array( __CLASS__, 'delete_homework' ) );
		add_action( 'wp_ajax_ss_save_meeting',    array( __CLASS__, 'save_meeting' ) );
		add_action( 'wp_ajax_ss_delete_meeting',  array( __CLASS__, 'delete_meeting' ) );
		add_action( 'wp_ajax_ss_save_inquiry',    array( 'SS_Notice', 'save_inquiry' ) );
		add_action( 'wp_ajax_nopriv_ss_save_inquiry', array( 'SS_Notice', 'save_inquiry' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
	}

	public static function save_notice() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'school_id' => SS_Helper::post( 'school_id', 'int' ), 'title' => SS_Helper::post( 'title' ), 'description' => SS_Helper::post( 'description', 'html' ), 'date' => SS_Helper::post( 'date' ) ?: current_time( 'Y-m-d' ), 'created_at' => current_time( 'mysql' ) );
		if ( ! empty( $_FILES['attachment']['name'] ) ) { $u = SS_Helper::handle_upload( $_FILES['attachment'], 'school-softwere/notices' ); if ( ! is_wp_error( $u ) ) $data['attachment'] = $u; }
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_NOTICES, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_NOTICES, $data ); $notice_id = $wpdb->insert_id;
			$class_ids = array_map( 'intval', (array) ( $_POST['class_school_ids'] ?? array() ) );
			foreach ( $class_ids as $csid ) { $wpdb->insert( SS_TABLE_CLASS_SCHOOL_NOTICE, array( 'class_school_id' => $csid, 'notice_id' => $notice_id ) ); }
		}
		wp_send_json( array( 'success' => true, 'message' => __( 'Notice saved.', 'school-softwere' ) ) );
	}

	public static function delete_notice() {
		self::verify(); global $wpdb;
		$id = SS_Helper::post( 'id', 'int' );
		$wpdb->delete( SS_TABLE_NOTICES, array( 'ID' => $id ) );
		$wpdb->delete( SS_TABLE_CLASS_SCHOOL_NOTICE, array( 'notice_id' => $id ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Notice deleted.', 'school-softwere' ) ) );
	}

	public static function save_event() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'school_id' => SS_Helper::post( 'school_id', 'int' ), 'title' => SS_Helper::post( 'title' ), 'description' => SS_Helper::post( 'description', 'html' ), 'start_date' => SS_Helper::post( 'start_date' ) ?: null, 'end_date' => SS_Helper::post( 'end_date' ) ?: null, 'venue' => SS_Helper::post( 'venue' ), 'created_at' => current_time( 'mysql' ) );
		if ( ! empty( $_FILES['attachment']['name'] ) ) { $u = SS_Helper::handle_upload( $_FILES['attachment'], 'school-softwere/events' ); if ( ! is_wp_error( $u ) ) $data['attachment'] = $u; }
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_EVENTS, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_EVENTS, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Event saved.', 'school-softwere' ) ) );
	}

	public static function delete_event() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_EVENTS, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Event deleted.', 'school-softwere' ) ) );
	}

	public static function save_homework() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'class_school_id' => SS_Helper::post( 'class_school_id', 'int' ), 'section_id' => SS_Helper::post( 'section_id', 'int' ), 'subject_id' => SS_Helper::post( 'subject_id', 'int' ), 'staff_id' => SS_Helper::post( 'staff_id', 'int' ) ?: null, 'title' => SS_Helper::post( 'title' ), 'description' => SS_Helper::post( 'description', 'html' ), 'submission_date' => SS_Helper::post( 'submission_date' ) ?: null, 'created_at' => current_time( 'mysql' ) );
		if ( ! empty( $_FILES['attachment']['name'] ) ) { $u = SS_Helper::handle_upload( $_FILES['attachment'], 'school-softwere/homework' ); if ( ! is_wp_error( $u ) ) $data['attachment'] = $u; }
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_HOMEWORK, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_HOMEWORK, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Homework saved.', 'school-softwere' ) ) );
	}

	public static function delete_homework() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_HOMEWORK, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Homework deleted.', 'school-softwere' ) ) );
	}

	public static function save_meeting() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'school_id' => SS_Helper::post( 'school_id', 'int' ), 'class_school_id' => SS_Helper::post( 'class_school_id', 'int' ) ?: null, 'section_id' => SS_Helper::post( 'section_id', 'int' ) ?: null, 'title' => SS_Helper::post( 'title' ), 'meeting_link' => SS_Helper::post( 'meeting_link', 'url' ), 'start_time' => SS_Helper::post( 'start_time' ) ?: null, 'duration' => SS_Helper::post( 'duration', 'int' ) ?: 60, 'created_at' => current_time( 'mysql' ) );
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_MEETINGS, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_MEETINGS, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Meeting saved.', 'school-softwere' ) ) );
	}

	public static function delete_meeting() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_MEETINGS, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Meeting deleted.', 'school-softwere' ) ) );
	}

	public static function save_inquiry() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ?? '' ) ), 'ss_nonce' ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
		global $wpdb;
		$wpdb->insert( SS_TABLE_INQUIRIES, array( 'school_id' => SS_Helper::post( 'school_id', 'int' ), 'name' => SS_Helper::post( 'name' ), 'email' => SS_Helper::post( 'email', 'email' ), 'phone' => SS_Helper::post( 'phone' ), 'message' => SS_Helper::post( 'message', 'html' ), 'source' => SS_Helper::post( 'source' ) ?: 'website', 'status' => 'new', 'created_at' => current_time( 'mysql' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Inquiry submitted. We will contact you soon.', 'school-softwere' ) ) );
	}
}
SS_Notice::init();
