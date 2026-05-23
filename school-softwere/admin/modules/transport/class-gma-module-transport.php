<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Transport {
	public static function init() {
		add_action( 'wp_ajax_gma_save_vehicle',   array( __CLASS__, 'save_vehicle' ) );
		add_action( 'wp_ajax_gma_delete_vehicle', array( __CLASS__, 'delete_vehicle' ) );
		add_action( 'wp_ajax_gma_save_route',     array( __CLASS__, 'save_route' ) );
		add_action( 'wp_ajax_gma_delete_route',   array( __CLASS__, 'delete_route' ) );
		add_action( 'wp_ajax_gma_save_stop',      array( __CLASS__, 'save_stop' ) );
		add_action( 'wp_ajax_gma_delete_stop',    array( __CLASS__, 'delete_stop' ) );
		add_action( 'wp_ajax_gma_get_vehicles',   array( __CLASS__, 'get_vehicles' ) );
		add_action( 'wp_ajax_gma_get_routes',     array( __CLASS__, 'get_routes' ) );
	}
	public static function save_vehicle() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'school_id'    => GMA_Helper::get_current_school_id(),
			'number'       => GMA_Helper::post( 'number' ),
			'model'        => GMA_Helper::post( 'model' ),
			'capacity'     => GMA_Helper::post( 'capacity', 'int' ),
			'driver_name'  => GMA_Helper::post( 'driver_name' ),
			'driver_phone' => GMA_Helper::post( 'driver_phone' ),
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_VEHICLES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Vehicle updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_VEHICLES, $data ); wp_send_json_success( array( 'message' => __( 'Vehicle added.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_vehicle() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_VEHICLES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Vehicle deleted.', 'gma-school' ) ) );
	}
	public static function save_route() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array( 'school_id' => GMA_Helper::get_current_school_id(), 'label' => GMA_Helper::post( 'label' ) );
		if ( $id ) { $wpdb->update( GMA_TABLE_ROUTES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Route updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_ROUTES, $data ); wp_send_json_success( array( 'message' => __( 'Route added.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_route() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_ROUTES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Route deleted.', 'gma-school' ) ) );
	}
	public static function save_stop() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'route_id'   => GMA_Helper::post( 'route_id', 'int' ),
			'vehicle_id' => GMA_Helper::post( 'vehicle_id', 'int' ),
			'stop_name'  => GMA_Helper::post( 'stop_name' ),
			'stop_time'  => GMA_Helper::post( 'stop_time' ) ?: null,
			'fee'        => GMA_Helper::post( 'fee', 'float' ),
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_ROUTE_VEHICLE, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Stop updated.', 'gma-school' ) ) ); }
		else { $wpdb->insert( GMA_TABLE_ROUTE_VEHICLE, $data ); wp_send_json_success( array( 'message' => __( 'Stop added.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_stop() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_ROUTE_VEHICLE, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Stop deleted.', 'gma-school' ) ) );
	}
	public static function get_vehicles() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$sid = GMA_Helper::get_current_school_id();
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_VEHICLES . " WHERE school_id=%d ORDER BY number", $sid ) ) );
	}
	public static function get_routes() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$sid = GMA_Helper::get_current_school_id();
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_ROUTES . " WHERE school_id=%d ORDER BY label", $sid ) ) );
	}
}
GMA_Module_Transport::init();
