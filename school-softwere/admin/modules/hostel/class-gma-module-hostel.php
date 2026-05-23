<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Hostel {
	public static function init() {
		add_action( 'wp_ajax_gma_save_hostel',   array( __CLASS__, 'save_hostel' ) );
		add_action( 'wp_ajax_gma_delete_hostel', array( __CLASS__, 'delete_hostel' ) );
		add_action( 'wp_ajax_gma_get_hostel',    array( __CLASS__, 'get_hostel' ) );
		add_action( 'wp_ajax_gma_save_room',     array( __CLASS__, 'save_room' ) );
		add_action( 'wp_ajax_gma_delete_room',   array( __CLASS__, 'delete_room' ) );
		add_action( 'wp_ajax_gma_get_rooms',     array( __CLASS__, 'get_rooms' ) );
	}
	public static function save_hostel() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'school_id'   => GMA_Helper::get_current_school_id(),
			'label'       => GMA_Helper::post( 'label' ),
			'type'        => GMA_Helper::post( 'type' ) ?: 'boys',
			'warden_name' => GMA_Helper::post( 'warden_name' ),
			'capacity'    => GMA_Helper::post( 'capacity', 'int' ),
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_HOSTELS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Hostel updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_HOSTELS, $data ); wp_send_json_success( array( 'message' => __( 'Hostel created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_hostel() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_HOSTELS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Hostel deleted.', 'gma-school' ) ) );
	}
	public static function get_hostel() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_HOSTELS . " WHERE ID=%d", GMA_Helper::post( 'id', 'int' ) ) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
	public static function save_room() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'hostel_id'   => GMA_Helper::post( 'hostel_id', 'int' ),
			'room_number' => GMA_Helper::post( 'room_number' ),
			'capacity'    => GMA_Helper::post( 'capacity', 'int' ),
			'room_type'   => GMA_Helper::post( 'room_type' ),
			'fee'         => GMA_Helper::post( 'fee', 'float' ),
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_ROOMS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Room updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_ROOMS, $data ); wp_send_json_success( array( 'message' => __( 'Room added.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_room() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_ROOMS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Room deleted.', 'gma-school' ) ) );
	}
	public static function get_rooms() {
		GMA_Helper::verify_nonce(); global $wpdb;
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_ROOMS . " WHERE hostel_id=%d ORDER BY room_number", GMA_Helper::post( 'hostel_id', 'int' ) ) ) );
	}
}
GMA_Module_Hostel::init();
