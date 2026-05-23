<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Events {
	public static function init() {
		add_action( 'wp_ajax_gma_save_event',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_event', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_event',    array( __CLASS__, 'get_one' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'school_id'   => GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id(),
			'title'       => GMA_Helper::post( 'title' ),
			'description' => GMA_Helper::post( 'description', 'html' ),
			'start_date'  => GMA_Helper::post( 'start_date' ) ?: null,
			'end_date'    => GMA_Helper::post( 'end_date' ) ?: null,
			'venue'       => GMA_Helper::post( 'venue' ),
		);
		if ( ! empty( $_FILES['attachment']['name'] ) ) { $u = GMA_Helper::handle_upload( $_FILES['attachment'], 'gma-school/events' ); if ( ! is_wp_error( $u ) ) $data['attachment'] = $u; }
		if ( $id ) { $wpdb->update( GMA_TABLE_EVENTS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Event updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_EVENTS, $data ); wp_send_json_success( array( 'message' => __( 'Event created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_EVENTS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Deleted.', 'gma-school' ) ) );
	}
	public static function get_one() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_EVENTS . " WHERE ID=%d", GMA_Helper::post( 'id', 'int' ) ) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
}
GMA_Module_Events::init();
