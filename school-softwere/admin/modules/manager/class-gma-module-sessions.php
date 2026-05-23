<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Sessions {
	public static function init() {
		add_action( 'wp_ajax_gma_save_session',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_session', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_activate_session', array( __CLASS__, 'activate' ) );
		add_action( 'wp_ajax_gma_get_sessions',   array( __CLASS__, 'get_all' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'school_id'  => GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id(),
			'label'      => GMA_Helper::post( 'label' ),
			'start_date' => GMA_Helper::post( 'start_date' ) ?: null,
			'end_date'   => GMA_Helper::post( 'end_date' ) ?: null,
			'is_active'  => GMA_Helper::post( 'is_active', 'int' ),
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_SESSIONS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Session updated.', 'gma-school' ) ) ); }
		else { $wpdb->insert( GMA_TABLE_SESSIONS, $data ); wp_send_json_success( array( 'message' => __( 'Session created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$wpdb->delete( GMA_TABLE_SESSIONS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Session deleted.', 'gma-school' ) ) );
	}
	public static function activate() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$sid = GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id();
		$wpdb->update( GMA_TABLE_SESSIONS, array( 'is_active' => 0 ), array( 'school_id' => $sid ) );
		$wpdb->update( GMA_TABLE_SESSIONS, array( 'is_active' => 1 ), array( 'ID' => $id ) );
		wp_send_json_success( array( 'message' => __( 'Session activated.', 'gma-school' ) ) );
	}
	public static function get_all() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$sid = GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id();
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_SESSIONS . " WHERE school_id=%d ORDER BY ID DESC", $sid ) ) );
	}
}
GMA_Module_Sessions::init();
