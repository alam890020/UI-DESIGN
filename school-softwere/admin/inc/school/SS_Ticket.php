<?php
/**
 * SS_Ticket — Support tickets AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Ticket {
	public static function init() {
		add_action( 'wp_ajax_ss_save_ticket',         array( __CLASS__, 'save_ticket' ) );
		add_action( 'wp_ajax_ss_delete_ticket',       array( __CLASS__, 'delete_ticket' ) );
		add_action( 'wp_ajax_ss_reply_ticket',        array( __CLASS__, 'reply_ticket' ) );
		add_action( 'wp_ajax_ss_update_ticket_status',array( __CLASS__, 'update_status' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
	}

	public static function save_ticket() {
		self::verify(); global $wpdb;
		$data = array( 'school_id' => SS_Helper::post( 'school_id', 'int' ), 'user_id' => get_current_user_id(), 'subject' => SS_Helper::post( 'subject' ), 'status' => 'open', 'priority' => SS_Helper::post( 'priority' ) ?: 'normal', 'created_at' => current_time( 'mysql' ) );
		$wpdb->insert( SS_TABLE_TICKETS, $data );
		$ticket_id = $wpdb->insert_id;
		$message   = SS_Helper::post( 'message', 'html' );
		if ( $message ) {
			$wpdb->insert( SS_TABLE_TICKET_HISTORY, array( 'ticket_id' => $ticket_id, 'user_id' => get_current_user_id(), 'message' => $message, 'created_at' => current_time( 'mysql' ) ) );
		}
		wp_send_json( array( 'success' => true, 'message' => __( 'Ticket submitted.', 'school-softwere' ), 'data' => array( 'id' => $ticket_id ) ) );
	}

	public static function delete_ticket() {
		self::verify(); global $wpdb;
		$id = SS_Helper::post( 'id', 'int' );
		$wpdb->delete( SS_TABLE_TICKETS, array( 'ID' => $id ) );
		$wpdb->delete( SS_TABLE_TICKET_HISTORY, array( 'ticket_id' => $id ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Ticket deleted.', 'school-softwere' ) ) );
	}

	public static function reply_ticket() {
		self::verify(); global $wpdb;
		$ticket_id = SS_Helper::post( 'ticket_id', 'int' );
		$message   = SS_Helper::post( 'message', 'html' );
		if ( ! $ticket_id || ! $message )
			wp_send_json( array( 'success' => false, 'message' => __( 'Message required.', 'school-softwere' ) ) );
		$row = array( 'ticket_id' => $ticket_id, 'user_id' => get_current_user_id(), 'message' => $message, 'created_at' => current_time( 'mysql' ) );
		if ( ! empty( $_FILES['attachment']['name'] ) ) { $u = SS_Helper::handle_upload( $_FILES['attachment'], 'school-softwere/tickets' ); if ( ! is_wp_error( $u ) ) $row['attachment'] = $u; }
		$wpdb->insert( SS_TABLE_TICKET_HISTORY, $row );
		wp_send_json( array( 'success' => true, 'message' => __( 'Reply sent.', 'school-softwere' ) ) );
	}

	public static function update_status() {
		self::verify(); global $wpdb;
		$id     = SS_Helper::post( 'id', 'int' );
		$status = SS_Helper::post( 'status' );
		$wpdb->update( SS_TABLE_TICKETS, array( 'status' => $status ), array( 'ID' => $id ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Status updated.', 'school-softwere' ) ) );
	}
}
SS_Ticket::init();
