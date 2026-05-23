<?php
/**
 * SS_Class — Classes, sections, subjects AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Class {
	public static function init() {
		add_action( 'wp_ajax_ss_save_class',    array( __CLASS__, 'save_class' ) );
		add_action( 'wp_ajax_ss_delete_class',  array( __CLASS__, 'delete_class' ) );
		add_action( 'wp_ajax_ss_save_section',  array( __CLASS__, 'save_section' ) );
		add_action( 'wp_ajax_ss_delete_section',array( __CLASS__, 'delete_section' ) );
		add_action( 'wp_ajax_ss_save_subject',  array( __CLASS__, 'save_subject' ) );
		add_action( 'wp_ajax_ss_delete_subject',array( __CLASS__, 'delete_subject' ) );
		add_action( 'wp_ajax_ss_save_routine',  array( __CLASS__, 'save_routine' ) );
		add_action( 'wp_ajax_ss_delete_routine',array( __CLASS__, 'delete_routine' ) );
		add_action( 'wp_ajax_ss_assign_class_school', array( __CLASS__, 'assign_class_school' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
	}

	public static function save_class() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'label' => SS_Helper::post( 'label' ), 'created_at' => current_time( 'mysql' ) );
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_CLASSES, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_CLASSES, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Class saved.', 'school-softwere' ) ) );
	}

	public static function delete_class() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_CLASSES, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Class deleted.', 'school-softwere' ) ) );
	}

	public static function save_section() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'class_school_id' => SS_Helper::post( 'class_school_id', 'int' ), 'label' => SS_Helper::post( 'label' ), 'medium_id' => SS_Helper::post( 'medium_id', 'int' ) ?: null, 'capacity' => SS_Helper::post( 'capacity', 'int' ), 'created_at' => current_time( 'mysql' ) );
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_SECTIONS, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_SECTIONS, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Section saved.', 'school-softwere' ) ) );
	}

	public static function delete_section() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_SECTIONS, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Section deleted.', 'school-softwere' ) ) );
	}

	public static function save_subject() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'class_school_id' => SS_Helper::post( 'class_school_id', 'int' ), 'label' => SS_Helper::post( 'label' ), 'subject_type_id' => SS_Helper::post( 'subject_type_id', 'int' ) ?: null, 'code' => SS_Helper::post( 'code' ), 'created_at' => current_time( 'mysql' ) );
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_SUBJECTS, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_SUBJECTS, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Subject saved.', 'school-softwere' ) ) );
	}

	public static function delete_subject() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_SUBJECTS, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Subject deleted.', 'school-softwere' ) ) );
	}

	public static function save_routine() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'class_school_id' => SS_Helper::post( 'class_school_id', 'int' ), 'section_id' => SS_Helper::post( 'section_id', 'int' ), 'subject_id' => SS_Helper::post( 'subject_id', 'int' ), 'staff_id' => SS_Helper::post( 'staff_id', 'int' ) ?: null, 'day' => SS_Helper::post( 'day' ), 'start_time' => SS_Helper::post( 'start_time' ) ?: null, 'end_time' => SS_Helper::post( 'end_time' ) ?: null );
		if ( $id ) { $wpdb->update( SS_TABLE_ROUTINES, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_ROUTINES, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Routine saved.', 'school-softwere' ) ) );
	}

	public static function delete_routine() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_ROUTINES, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Routine deleted.', 'school-softwere' ) ) );
	}

	public static function assign_class_school() {
		self::verify(); global $wpdb;
		$class_id  = SS_Helper::post( 'class_id', 'int' );
		$school_id = SS_Helper::post( 'school_id', 'int' );
		$session_id= SS_Helper::post( 'session_id', 'int' );
		$exists    = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . SS_TABLE_CLASS_SCHOOL . " WHERE class_id=%d AND school_id=%d AND session_id=%d", $class_id, $school_id, $session_id ) );
		if ( ! $exists ) { $wpdb->insert( SS_TABLE_CLASS_SCHOOL, array( 'class_id' => $class_id, 'school_id' => $school_id, 'session_id' => $session_id ) ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Class assigned to school.', 'school-softwere' ) ) );
	}
}
SS_Class::init();
