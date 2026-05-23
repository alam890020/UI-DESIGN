<?php
/**
 * SS_Transport — Transport & Hostel AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Transport {
	public static function init() {
		add_action( 'wp_ajax_ss_save_vehicle',  array( __CLASS__, 'save_vehicle' ) );
		add_action( 'wp_ajax_ss_delete_vehicle',array( __CLASS__, 'delete_vehicle' ) );
		add_action( 'wp_ajax_ss_save_route',    array( __CLASS__, 'save_route' ) );
		add_action( 'wp_ajax_ss_delete_route',  array( __CLASS__, 'delete_route' ) );
		add_action( 'wp_ajax_ss_save_route_stop', array( __CLASS__, 'save_route_stop' ) );
		add_action( 'wp_ajax_ss_save_hostel',   array( __CLASS__, 'save_hostel' ) );
		add_action( 'wp_ajax_ss_delete_hostel', array( __CLASS__, 'delete_hostel' ) );
		add_action( 'wp_ajax_ss_save_room',     array( __CLASS__, 'save_room' ) );
		add_action( 'wp_ajax_ss_delete_room',   array( __CLASS__, 'delete_room' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
	}

	public static function save_vehicle() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'school_id' => SS_Helper::post( 'school_id', 'int' ), 'number' => SS_Helper::post( 'number' ), 'model' => SS_Helper::post( 'model' ), 'capacity' => SS_Helper::post( 'capacity', 'int' ), 'driver_name' => SS_Helper::post( 'driver_name' ), 'driver_phone' => SS_Helper::post( 'driver_phone' ), 'created_at' => current_time( 'mysql' ) );
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_VEHICLES, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_VEHICLES, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Vehicle saved.', 'school-softwere' ) ) );
	}

	public static function delete_vehicle() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_VEHICLES, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Vehicle deleted.', 'school-softwere' ) ) );
	}

	public static function save_route() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'school_id' => SS_Helper::post( 'school_id', 'int' ), 'label' => SS_Helper::post( 'label' ), 'created_at' => current_time( 'mysql' ) );
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_ROUTES, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_ROUTES, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Route saved.', 'school-softwere' ) ) );
	}

	public static function delete_route() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_ROUTES, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Route deleted.', 'school-softwere' ) ) );
	}

	public static function save_route_stop() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'route_id' => SS_Helper::post( 'route_id', 'int' ), 'vehicle_id' => SS_Helper::post( 'vehicle_id', 'int' ), 'stop_name' => SS_Helper::post( 'stop_name' ), 'stop_time' => SS_Helper::post( 'stop_time' ) ?: null, 'fee' => SS_Helper::post( 'fee', 'float' ) );
		if ( $id ) { $wpdb->update( SS_TABLE_ROUTE_VEHICLE, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_ROUTE_VEHICLE, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Stop saved.', 'school-softwere' ) ) );
	}

	public static function save_hostel() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'school_id' => SS_Helper::post( 'school_id', 'int' ), 'label' => SS_Helper::post( 'label' ), 'type' => SS_Helper::post( 'type' ) ?: 'boys', 'warden_name' => SS_Helper::post( 'warden_name' ), 'capacity' => SS_Helper::post( 'capacity', 'int' ), 'created_at' => current_time( 'mysql' ) );
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_HOSTELS, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_HOSTELS, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Hostel saved.', 'school-softwere' ) ) );
	}

	public static function delete_hostel() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_HOSTELS, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Hostel deleted.', 'school-softwere' ) ) );
	}

	public static function save_room() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'hostel_id' => SS_Helper::post( 'hostel_id', 'int' ), 'room_number' => SS_Helper::post( 'room_number' ), 'capacity' => SS_Helper::post( 'capacity', 'int' ), 'room_type' => SS_Helper::post( 'room_type' ), 'fee' => SS_Helper::post( 'fee', 'float' ), 'created_at' => current_time( 'mysql' ) );
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_ROOMS, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_ROOMS, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Room saved.', 'school-softwere' ) ) );
	}

	public static function delete_room() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_ROOMS, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Room deleted.', 'school-softwere' ) ) );
	}
}
SS_Transport::init();
