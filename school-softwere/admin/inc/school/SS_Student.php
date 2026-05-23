<?php
/**
 * SS_Student — Student management AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Student {

	public static function init() {
		add_action( 'wp_ajax_ss_save_student',    array( __CLASS__, 'save_student' ) );
		add_action( 'wp_ajax_ss_delete_student',  array( __CLASS__, 'delete_student' ) );
		add_action( 'wp_ajax_ss_get_student',     array( __CLASS__, 'get_student' ) );
		add_action( 'wp_ajax_ss_promote_students',array( __CLASS__, 'promote_students' ) );
		add_action( 'wp_ajax_ss_transfer_student',array( __CLASS__, 'transfer_student' ) );
		add_action( 'wp_ajax_ss_bulk_students',   array( __CLASS__, 'bulk_action' ) );
		add_action( 'wp_ajax_ss_switch_school',   array( __CLASS__, 'switch_school' ) );
	}

	/** Verify nonce + capability */
	private static function verify( $cap = 'read' ) {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) ) {
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
		}
		if ( ! current_user_can( $cap ) ) {
			wp_send_json( array( 'success' => false, 'message' => __( 'Permission denied.', 'school-softwere' ) ) );
		}
	}

	public static function save_student() {
		self::verify();
		global $wpdb;

		$id              = SS_Helper::post( 'id', 'int' );
		$school_id       = SS_Helper::post( 'school_id', 'int' );
		$class_school_id = SS_Helper::post( 'class_school_id', 'int' );
		$section_id      = SS_Helper::post( 'section_id', 'int' );

		if ( ! $school_id || ! $class_school_id || ! $section_id ) {
			wp_send_json( array( 'success' => false, 'message' => __( 'School, class, and section are required.', 'school-softwere' ) ) );
		}

		$data = array(
			'school_id'        => $school_id,
			'class_school_id'  => $class_school_id,
			'section_id'       => $section_id,
			'first_name'       => SS_Helper::post( 'first_name' ),
			'last_name'        => SS_Helper::post( 'last_name' ),
			'father_name'      => SS_Helper::post( 'father_name' ),
			'mother_name'      => SS_Helper::post( 'mother_name' ),
			'guardian_name'    => SS_Helper::post( 'guardian_name' ),
			'guardian_relation'=> SS_Helper::post( 'guardian_relation' ),
			'dob'              => SS_Helper::post( 'dob' ) ?: null,
			'gender'           => SS_Helper::post( 'gender' ),
			'blood_group'      => SS_Helper::post( 'blood_group' ),
			'religion'         => SS_Helper::post( 'religion' ),
			'caste'            => SS_Helper::post( 'caste' ),
			'nationality'      => SS_Helper::post( 'nationality' ),
			'address'          => SS_Helper::post( 'address' ),
			'city'             => SS_Helper::post( 'city' ),
			'state'            => SS_Helper::post( 'state' ),
			'zip'              => SS_Helper::post( 'zip' ),
			'country'          => SS_Helper::post( 'country' ),
			'phone'            => SS_Helper::post( 'phone' ),
			'email'            => SS_Helper::post( 'email', 'email' ),
			'admission_date'   => SS_Helper::post( 'admission_date' ) ?: null,
			'student_type_id'  => SS_Helper::post( 'student_type_id', 'int' ) ?: null,
			'roll_number'      => SS_Helper::post( 'roll_number' ),
			'is_active'        => 1,
		);

		// Handle photo upload
		if ( ! empty( $_FILES['photo']['name'] ) ) {
			$upload = SS_Helper::handle_upload( $_FILES['photo'], 'school-softwere/students' );
			if ( ! is_wp_error( $upload ) ) {
				$data['photo'] = $upload;
			}
		}

		if ( $id ) {
			$wpdb->update( SS_TABLE_STUDENT_RECORDS, $data, array( 'ID' => $id ) );
			SS_Database::log( 'student_updated', 'Student ID: ' . $id, $school_id );
			wp_send_json( array( 'success' => true, 'message' => __( 'Student updated successfully.', 'school-softwere' ), 'data' => array( 'id' => $id ) ) );
		} else {
			$data['admission_number'] = SS_Helper::generate_admission_number( $school_id );
			$data['created_at']       = current_time( 'mysql' );
			$wpdb->insert( SS_TABLE_STUDENT_RECORDS, $data );
			$new_id = $wpdb->insert_id;
			SS_Database::log( 'student_added', 'Student: ' . $data['first_name'] . ' ' . $data['last_name'], $school_id );
			wp_send_json( array( 'success' => true, 'message' => __( 'Student enrolled successfully.', 'school-softwere' ), 'data' => array( 'id' => $new_id, 'admission_number' => $data['admission_number'] ) ) );
		}
	}

	public static function delete_student() {
		self::verify( 'manage_options' );
		global $wpdb;
		$id = SS_Helper::post( 'id', 'int' );
		if ( ! $id ) wp_send_json( array( 'success' => false, 'message' => __( 'Invalid ID.', 'school-softwere' ) ) );
		$wpdb->delete( SS_TABLE_STUDENT_RECORDS, array( 'ID' => $id ) );
		SS_Database::log( 'student_deleted', 'Student ID: ' . $id );
		wp_send_json( array( 'success' => true, 'message' => __( 'Student deleted.', 'school-softwere' ) ) );
	}

	public static function get_student() {
		self::verify();
		global $wpdb;
		$id = SS_Helper::post( 'id', 'int' );
		$student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . SS_TABLE_STUDENT_RECORDS . " WHERE ID = %d", $id ) );
		if ( $student ) {
			wp_send_json( array( 'success' => true, 'data' => $student ) );
		} else {
			wp_send_json( array( 'success' => false, 'message' => __( 'Student not found.', 'school-softwere' ) ) );
		}
	}

	public static function promote_students() {
		self::verify();
		global $wpdb;
		$ids             = array_map( 'intval', (array) ( $_POST['ids'] ?? array() ) );
		$to_class_school = SS_Helper::post( 'to_class_school_id', 'int' );
		$to_section      = SS_Helper::post( 'to_section_id', 'int' );
		if ( empty( $ids ) || ! $to_class_school ) {
			wp_send_json( array( 'success' => false, 'message' => __( 'Invalid data.', 'school-softwere' ) ) );
		}
		$count = 0;
		foreach ( $ids as $student_id ) {
			$student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . SS_TABLE_STUDENT_RECORDS . " WHERE ID = %d", $student_id ) );
			if ( ! $student ) continue;
			// Log promotion
			$wpdb->insert( SS_TABLE_PROMOTIONS, array(
				'student_record_id'    => $student_id,
				'from_class_school_id' => $student->class_school_id,
				'to_class_school_id'   => $to_class_school,
				'promoted_at'          => current_time( 'mysql' ),
			) );
			// Update student record
			$wpdb->update( SS_TABLE_STUDENT_RECORDS, array(
				'class_school_id' => $to_class_school,
				'section_id'      => $to_section ?: $student->section_id,
			), array( 'ID' => $student_id ) );
			$count++;
		}
		wp_send_json( array( 'success' => true, 'message' => sprintf( _n( '%d student promoted.', '%d students promoted.', $count, 'school-softwere' ), $count ) ) );
	}

	public static function transfer_student() {
		self::verify();
		global $wpdb;
		$id     = SS_Helper::post( 'id', 'int' );
		$reason = SS_Helper::post( 'reason' );
		$wpdb->insert( SS_TABLE_TRANSFERS, array( 'student_record_id' => $id, 'reason' => $reason, 'created_at' => current_time( 'mysql' ) ) );
		$wpdb->update( SS_TABLE_STUDENT_RECORDS, array( 'is_active' => 0 ), array( 'ID' => $id ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Student transferred.', 'school-softwere' ) ) );
	}

	public static function bulk_action() {
		self::verify();
		global $wpdb;
		$action = SS_Helper::post( 'action' );
		$ids    = array_map( 'intval', (array) ( $_POST['ids'] ?? array() ) );
		if ( empty( $ids ) ) wp_send_json( array( 'success' => false, 'message' => __( 'No items selected.', 'school-softwere' ) ) );
		$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
		switch ( $action ) {
			case 'ss_bulk_delete_students':
				$wpdb->query( $wpdb->prepare( "DELETE FROM " . SS_TABLE_STUDENT_RECORDS . " WHERE ID IN ($placeholders)", $ids ) ); // phpcs:ignore
				wp_send_json( array( 'success' => true, 'message' => __( 'Selected students deleted.', 'school-softwere' ) ) );
				break;
			case 'ss_bulk_deactivate_students':
				$wpdb->query( $wpdb->prepare( "UPDATE " . SS_TABLE_STUDENT_RECORDS . " SET is_active=0 WHERE ID IN ($placeholders)", $ids ) ); // phpcs:ignore
				wp_send_json( array( 'success' => true, 'message' => __( 'Selected students deactivated.', 'school-softwere' ) ) );
				break;
			default:
				wp_send_json( array( 'success' => false, 'message' => __( 'Unknown action.', 'school-softwere' ) ) );
		}
	}

	public static function switch_school() {
		check_ajax_referer( 'ss_nonce', 'nonce' );
		$school_id = SS_Helper::post( 'school_id', 'int' );
		if ( ! session_id() ) session_start();
		$_SESSION['ss_school_id'] = $school_id;
		wp_send_json( array( 'success' => true ) );
	}
}

SS_Student::init();
