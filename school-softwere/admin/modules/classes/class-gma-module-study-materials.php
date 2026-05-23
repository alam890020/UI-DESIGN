<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Study_Materials {
	public static function init() {
		add_action( 'wp_ajax_gma_save_study_material',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_study_material', array( __CLASS__, 'delete' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'class_school_id' => GMA_Helper::post( 'class_school_id', 'int' ),
			'section_id'      => GMA_Helper::post( 'section_id', 'int' ) ?: null,
			'subject_id'      => GMA_Helper::post( 'subject_id', 'int' ) ?: null,
			'title'           => GMA_Helper::post( 'title' ),
			'description'     => GMA_Helper::post( 'description' ),
			'file_type'       => GMA_Helper::post( 'file_type' ),
		);
		if ( ! empty( $_FILES['file']['name'] ) ) {
			$u = GMA_Helper::handle_upload( $_FILES['file'], 'gma-school/materials' );
			if ( ! is_wp_error( $u ) ) $data['file'] = $u;
		} else {
			$data['file'] = GMA_Helper::post( 'file', 'url' );
		}
		if ( $id ) { $wpdb->update( GMA_TABLE_STUDY_MATERIALS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_STUDY_MATERIALS, $data ); wp_send_json_success( array( 'message' => __( 'Material uploaded.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_STUDY_MATERIALS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Deleted.', 'gma-school' ) ) );
	}
}
GMA_Module_Study_Materials::init();
