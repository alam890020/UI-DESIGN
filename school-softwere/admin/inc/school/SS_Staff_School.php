<?php
/**
 * SS_Staff_School — Staff management AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Staff_School {

	public static function init() {
		add_action( 'wp_ajax_ss_save_staff',   array( __CLASS__, 'save_staff' ) );
		add_action( 'wp_ajax_ss_delete_staff', array( __CLASS__, 'delete_staff' ) );
		add_action( 'wp_ajax_ss_get_staff',    array( __CLASS__, 'get_staff' ) );
		add_action( 'wp_ajax_ss_get_sections', array( __CLASS__, 'get_sections' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) ) {
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
		}
	}

	public static function save_staff() {
		self::verify();
		global $wpdb;
		$id        = SS_Helper::post( 'id', 'int' );
		$school_id = SS_Helper::post( 'school_id', 'int' );

		$data = array(
			'school_id'   => $school_id,
			'role_id'     => SS_Helper::post( 'role_id', 'int' ) ?: null,
			'first_name'  => SS_Helper::post( 'first_name' ),
			'last_name'   => SS_Helper::post( 'last_name' ),
			'dob'         => SS_Helper::post( 'dob' ) ?: null,
			'gender'      => SS_Helper::post( 'gender' ),
			'phone'       => SS_Helper::post( 'phone' ),
			'email'       => SS_Helper::post( 'email', 'email' ),
			'address'     => SS_Helper::post( 'address' ),
			'joining_date'=> SS_Helper::post( 'joining_date' ) ?: null,
			'designation' => SS_Helper::post( 'designation' ),
			'salary'      => SS_Helper::post( 'salary', 'float' ),
			'is_active'   => 1,
		);

		if ( ! empty( $_FILES['photo']['name'] ) ) {
			$upload = SS_Helper::handle_upload( $_FILES['photo'], 'school-softwere/staff' );
			if ( ! is_wp_error( $upload ) ) $data['photo'] = $upload;
		}

		if ( $id ) {
			$wpdb->update( SS_TABLE_STAFF, $data, array( 'ID' => $id ) );
			SS_Database::log( 'staff_updated', 'Staff ID: ' . $id, $school_id );
			wp_send_json( array( 'success' => true, 'message' => __( 'Staff updated.', 'school-softwere' ), 'data' => array( 'id' => $id ) ) );
		} else {
			$data['created_at'] = current_time( 'mysql' );
			$wpdb->insert( SS_TABLE_STAFF, $data );
			$new_id = $wpdb->insert_id;
			SS_Database::log( 'staff_added', 'Staff: ' . $data['first_name'] . ' ' . $data['last_name'], $school_id );
			wp_send_json( array( 'success' => true, 'message' => __( 'Staff member added.', 'school-softwere' ), 'data' => array( 'id' => $new_id ) ) );
		}
	}

	public static function delete_staff() {
		self::verify();
		global $wpdb;
		$id = SS_Helper::post( 'id', 'int' );
		$wpdb->delete( SS_TABLE_STAFF, array( 'ID' => $id ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Staff deleted.', 'school-softwere' ) ) );
	}

	public static function get_staff() {
		self::verify();
		global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$row  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . SS_TABLE_STAFF . " WHERE ID=%d", $id ) );
		$row ? wp_send_json( array( 'success' => true, 'data' => $row ) ) : wp_send_json( array( 'success' => false ) );
	}

	public static function get_sections() {
		self::verify();
		global $wpdb;
		$cs_id = SS_Helper::post( 'class_school_id', 'int' );
		$rows  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . SS_TABLE_SECTIONS . " WHERE class_school_id=%d ORDER BY label", $cs_id ) );
		wp_send_json( array( 'success' => true, 'data' => $rows ) );
	}
}

SS_Staff_School::init();
