<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Subjects {
	public static function init() {
		add_action( 'wp_ajax_gma_save_subject',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_subject', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_subjects',   array( __CLASS__, 'get_all' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'class_school_id'  => GMA_Helper::post( 'class_school_id', 'int' ),
			'label'            => GMA_Helper::post( 'label' ),
			'subject_type_id'  => GMA_Helper::post( 'subject_type_id', 'int' ) ?: null,
			'code'             => GMA_Helper::post( 'code' ),
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_SUBJECTS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Subject updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_SUBJECTS, $data ); wp_send_json_success( array( 'message' => __( 'Subject added.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_SUBJECTS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Subject deleted.', 'gma-school' ) ) );
	}
	public static function get_all() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$csid = GMA_Helper::post( 'class_school_id', 'int' );
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_SUBJECTS . " WHERE class_school_id=%d ORDER BY label", $csid ) ) );
	}
}
GMA_Module_Subjects::init();
