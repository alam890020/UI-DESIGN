<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Notifications {
	public static function init() {
		add_action( 'wp_ajax_gma_save_notification',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_notification', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_mark_notification_read', array( __CLASS__, 'mark_read' ) );
		add_action( 'wp_ajax_gma_get_notifications',   array( __CLASS__, 'get_all' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'school_id'  => GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id(),
			'title'      => GMA_Helper::post( 'title' ),
			'message'    => GMA_Helper::post( 'message', 'html' ),
			'type'       => GMA_Helper::post( 'type' ) ?: 'general',
			'audience'   => GMA_Helper::post( 'audience' ) ?: 'all',
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_NOTIFICATIONS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Notification updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_NOTIFICATIONS, $data ); wp_send_json_success( array( 'message' => __( 'Notification sent.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$wpdb->delete( GMA_TABLE_NOTIFICATIONS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Deleted.', 'gma-school' ) ) );
	}
	public static function mark_read() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->update( GMA_TABLE_NOTIFICATIONS, array( 'is_read' => 1 ), array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success();
	}
	public static function get_all() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$sid = GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id();
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_NOTIFICATIONS . " WHERE school_id=%d ORDER BY created_at DESC LIMIT 50", $sid ) ) );
	}
}
GMA_Module_Notifications::init();
