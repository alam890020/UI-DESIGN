<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Leaves {
	public static function init() {
		add_action( 'wp_ajax_gma_save_staff_leave',   array( __CLASS__, 'save_staff' ) );
		add_action( 'wp_ajax_gma_update_leave_status',array( __CLASS__, 'update_status' ) );
		add_action( 'wp_ajax_gma_delete_leave',       array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_save_student_leave', array( __CLASS__, 'save_student' ) );
	}
	public static function save_staff() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'staff_id'   => GMA_Helper::post( 'staff_id', 'int' ),
			'school_id'  => GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id(),
			'leave_type' => GMA_Helper::post( 'leave_type' ),
			'from_date'  => GMA_Helper::post( 'from_date' ) ?: null,
			'to_date'    => GMA_Helper::post( 'to_date' ) ?: null,
			'reason'     => GMA_Helper::post( 'reason' ),
			'status'     => 'pending',
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_LEAVES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Leave updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_LEAVES, $data ); wp_send_json_success( array( 'message' => __( 'Leave request submitted.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function update_status() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id   = GMA_Helper::post( 'id', 'int' );
		$type = GMA_Helper::post( 'type' );
		$s    = GMA_Helper::post( 'status' );
		$tbl  = ( $type === 'student' ) ? GMA_TABLE_STUDENT_LEAVES : GMA_TABLE_LEAVES;
		$wpdb->update( $tbl, array( 'status' => $s ), array( 'ID' => $id ) );
		wp_send_json_success( array( 'message' => __( 'Status updated.', 'gma-school' ) ) );
	}
	public static function delete() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$type = GMA_Helper::post( 'type' );
		$tbl  = ( $type === 'student' ) ? GMA_TABLE_STUDENT_LEAVES : GMA_TABLE_LEAVES;
		$wpdb->delete( $tbl, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Deleted.', 'gma-school' ) ) );
	}
	public static function save_student() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$data = array(
			'student_record_id' => GMA_Helper::post( 'student_record_id', 'int' ),
			'school_id'         => GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id(),
			'from_date'         => GMA_Helper::post( 'from_date' ) ?: null,
			'to_date'           => GMA_Helper::post( 'to_date' ) ?: null,
			'reason'            => GMA_Helper::post( 'reason' ),
			'status'            => 'pending',
			'created_at'        => current_time( 'mysql' ),
		);
		$wpdb->insert( GMA_TABLE_STUDENT_LEAVES, $data );
		wp_send_json_success( array( 'message' => __( 'Leave request submitted.', 'gma-school' ), 'id' => $wpdb->insert_id ) );
	}
}
GMA_Module_Leaves::init();
