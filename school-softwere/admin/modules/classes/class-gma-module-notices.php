<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Notices {
	public static function init() {
		add_action( 'wp_ajax_gma_save_notice',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_notice', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_notice',    array( __CLASS__, 'get_one' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'school_id'   => GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id(),
			'title'       => GMA_Helper::post( 'title' ),
			'description' => GMA_Helper::post( 'description', 'html' ),
			'date'        => GMA_Helper::post( 'date' ) ?: null,
		);
		if ( ! empty( $_FILES['attachment']['name'] ) ) { $u = GMA_Helper::handle_upload( $_FILES['attachment'], 'gma-school/notices' ); if ( ! is_wp_error( $u ) ) $data['attachment'] = $u; }
		if ( $id ) { $wpdb->update( GMA_TABLE_NOTICES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Notice updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_NOTICES, $data ); wp_send_json_success( array( 'message' => __( 'Notice published.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_NOTICES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Deleted.', 'gma-school' ) ) );
	}
	public static function get_one() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_NOTICES . " WHERE ID=%d", GMA_Helper::post( 'id', 'int' ) ) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
}
GMA_Module_Notices::init();
