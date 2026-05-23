<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Fees {
	public static function init() {
		add_action( 'wp_ajax_gma_save_fee',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_fee', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_fee',    array( __CLASS__, 'get_one' ) );
		add_action( 'wp_ajax_gma_get_fees',   array( __CLASS__, 'get_all' ) );
		add_action( 'wp_ajax_gma_assign_fee_to_student', array( __CLASS__, 'assign_to_student' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'class_school_id' => GMA_Helper::post( 'class_school_id', 'int' ),
			'label'           => GMA_Helper::post( 'label' ),
			'amount'          => GMA_Helper::post( 'amount', 'float' ),
			'due_date'        => GMA_Helper::post( 'due_date' ) ?: null,
			'is_recurring'    => GMA_Helper::post( 'is_recurring', 'int' ),
			'frequency'       => GMA_Helper::post( 'frequency' ) ?: 'monthly',
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_FEES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Fee updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_FEES, $data ); wp_send_json_success( array( 'message' => __( 'Fee created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_FEES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Fee deleted.', 'gma-school' ) ) );
	}
	public static function get_one() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_FEES . " WHERE ID=%d", GMA_Helper::post( 'id', 'int' ) ) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
	public static function get_all() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$csid = GMA_Helper::post( 'class_school_id', 'int' );
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_FEES . " WHERE class_school_id=%d ORDER BY label", $csid ) ) );
	}
	public static function assign_to_student() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$student_id = GMA_Helper::post( 'student_record_id', 'int' );
		$fee_ids    = array_map( 'intval', (array) ( $_POST['fee_ids'] ?? array() ) );
		foreach ( $fee_ids as $fee_id ) {
			$e = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . GMA_TABLE_STUDENT_FEES . " WHERE student_record_id=%d AND fee_id=%d", $student_id, $fee_id ) );
			if ( ! $e ) {
				$fee = $wpdb->get_row( $wpdb->prepare( "SELECT amount FROM " . GMA_TABLE_FEES . " WHERE ID=%d", $fee_id ) );
				$wpdb->insert( GMA_TABLE_STUDENT_FEES, array( 'student_record_id' => $student_id, 'fee_id' => $fee_id, 'amount' => $fee ? $fee->amount : 0, 'created_at' => current_time( 'mysql' ) ) );
			}
		}
		wp_send_json_success( array( 'message' => __( 'Fees assigned.', 'gma-school' ) ) );
	}
}
GMA_Module_Fees::init();
