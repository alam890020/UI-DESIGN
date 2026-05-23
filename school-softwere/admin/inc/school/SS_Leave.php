<?php
/**
 * SS_Leave — Leave management AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Leave {
	public static function init() {
		add_action( 'wp_ajax_ss_save_leave',   array( __CLASS__, 'save_leave' ) );
		add_action( 'wp_ajax_ss_delete_leave', array( __CLASS__, 'delete_leave' ) );
		add_action( 'wp_ajax_ss_approve_leave',array( __CLASS__, 'approve_leave' ) );
		add_action( 'wp_ajax_ss_reject_leave', array( __CLASS__, 'reject_leave' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
	}

	public static function save_leave() {
		self::verify(); global $wpdb;
		$id        = SS_Helper::post( 'id', 'int' );
		$school_id = SS_Helper::post( 'school_id', 'int' );
		$staff_id  = SS_Helper::post( 'staff_id', 'int' );
		if ( ! $staff_id ) {
			$staff_row = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM " . SS_TABLE_STAFF . " WHERE user_id=%d AND school_id=%d LIMIT 1", get_current_user_id(), $school_id ) );
			$staff_id  = $staff_row ? $staff_row->ID : 0;
		}
		$data = array( 'staff_id' => $staff_id, 'school_id' => $school_id, 'leave_type' => SS_Helper::post( 'leave_type' ), 'from_date' => SS_Helper::post( 'from_date' ) ?: null, 'to_date' => SS_Helper::post( 'to_date' ) ?: null, 'reason' => SS_Helper::post( 'reason' ), 'status' => 'pending', 'created_at' => current_time( 'mysql' ) );
		if ( $id ) { unset( $data['created_at'], $data['status'] ); $wpdb->update( SS_TABLE_LEAVES, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_LEAVES, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Leave request saved.', 'school-softwere' ) ) );
	}

	public static function delete_leave() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_LEAVES, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Leave deleted.', 'school-softwere' ) ) );
	}

	public static function approve_leave() {
		self::verify();
		if ( ! SS_M_Role::can( 'approve_leaves' ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Permission denied.', 'school-softwere' ) ) );
		global $wpdb;
		$wpdb->update( SS_TABLE_LEAVES, array( 'status' => 'approved' ), array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Leave approved.', 'school-softwere' ) ) );
	}

	public static function reject_leave() {
		self::verify();
		if ( ! SS_M_Role::can( 'approve_leaves' ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Permission denied.', 'school-softwere' ) ) );
		global $wpdb;
		$wpdb->update( SS_TABLE_LEAVES, array( 'status' => 'rejected' ), array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Leave rejected.', 'school-softwere' ) ) );
	}
}
SS_Leave::init();
