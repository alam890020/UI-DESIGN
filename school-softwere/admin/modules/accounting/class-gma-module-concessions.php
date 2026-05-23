<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Concessions {
	public static function init() {
		add_action( 'wp_ajax_gma_save_concession_type',   array( __CLASS__, 'save_type' ) );
		add_action( 'wp_ajax_gma_delete_concession_type', array( __CLASS__, 'delete_type' ) );
		add_action( 'wp_ajax_gma_apply_concession',       array( __CLASS__, 'apply' ) );
		add_action( 'wp_ajax_gma_remove_concession',      array( __CLASS__, 'remove' ) );
	}
	public static function save_type() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array( 'school_id' => GMA_Helper::get_current_school_id(), 'label' => GMA_Helper::post( 'label' ) );
		if ( $id ) { $wpdb->update( GMA_TABLE_CONCESSION_TYPES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Concession type updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_CONCESSION_TYPES, $data ); wp_send_json_success( array( 'message' => __( 'Concession type created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_type() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_CONCESSION_TYPES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Deleted.', 'gma-school' ) ) );
	}
	public static function apply() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$student_id = GMA_Helper::post( 'student_record_id', 'int' );
		$type_id    = GMA_Helper::post( 'concession_type_id', 'int' );
		$e = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . GMA_TABLE_STUDENT_CONCESSION . " WHERE student_record_id=%d AND concession_type_id=%d", $student_id, $type_id ) );
		if ( $e ) wp_send_json_error( array( 'message' => __( 'Already applied.', 'gma-school' ) ) );
		$wpdb->insert( GMA_TABLE_STUDENT_CONCESSION, array( 'student_record_id' => $student_id, 'concession_type_id' => $type_id, 'created_at' => current_time( 'mysql' ) ) );
		wp_send_json_success( array( 'message' => __( 'Concession applied.', 'gma-school' ) ) );
	}
	public static function remove() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_STUDENT_CONCESSION, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Concession removed.', 'gma-school' ) ) );
	}
}
GMA_Module_Concessions::init();
