<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Chapters {
	public static function init() {
		add_action( 'wp_ajax_gma_save_chapter',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_chapter', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_chapters',   array( __CLASS__, 'get_all' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'subject_id'      => GMA_Helper::post( 'subject_id', 'int' ),
			'class_school_id' => GMA_Helper::post( 'class_school_id', 'int' ),
			'label'           => GMA_Helper::post( 'label' ),
			'description'     => GMA_Helper::post( 'description' ),
			'sort_order'      => GMA_Helper::post( 'sort_order', 'int' ) ?: 0,
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_CHAPTERS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Chapter updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_CHAPTERS, $data ); wp_send_json_success( array( 'message' => __( 'Chapter created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_CHAPTERS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Chapter deleted.', 'gma-school' ) ) );
	}
	public static function get_all() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$subject_id = GMA_Helper::post( 'subject_id', 'int' );
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_CHAPTERS . " WHERE subject_id=%d ORDER BY sort_order, label", $subject_id ) ) );
	}
}
GMA_Module_Chapters::init();
