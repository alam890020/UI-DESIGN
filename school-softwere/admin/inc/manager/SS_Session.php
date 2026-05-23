<?php
/**
 * SS_Session — Session management AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Session {
	public static function init() {
		add_action( 'wp_ajax_ss_save_session',   array( __CLASS__, 'save_session' ) );
		add_action( 'wp_ajax_ss_delete_session', array( __CLASS__, 'delete_session' ) );
		add_action( 'wp_ajax_ss_set_active_session', array( __CLASS__, 'set_active' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Permission denied.', 'school-softwere' ) ) );
	}

	public static function save_session() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'school_id' => SS_Helper::post( 'school_id', 'int' ), 'label' => SS_Helper::post( 'label' ), 'start_date' => SS_Helper::post( 'start_date' ) ?: null, 'end_date' => SS_Helper::post( 'end_date' ) ?: null, 'is_active' => SS_Helper::post( 'is_active', 'int' ) );
		if ( $id ) { $wpdb->update( SS_TABLE_SESSIONS, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_SESSIONS, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Session saved.', 'school-softwere' ) ) );
	}

	public static function delete_session() {
		self::verify(); global $wpdb;
		$wpdb->delete( SS_TABLE_SESSIONS, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Session deleted.', 'school-softwere' ) ) );
	}

	public static function set_active() {
		self::verify(); global $wpdb;
		$id        = SS_Helper::post( 'id', 'int' );
		$school_id = SS_Helper::post( 'school_id', 'int' );
		$wpdb->update( SS_TABLE_SESSIONS, array( 'is_active' => 0 ), array( 'school_id' => $school_id ) );
		$wpdb->update( SS_TABLE_SESSIONS, array( 'is_active' => 1 ), array( 'ID' => $id ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Session activated.', 'school-softwere' ) ) );
	}
}
SS_Session::init();
