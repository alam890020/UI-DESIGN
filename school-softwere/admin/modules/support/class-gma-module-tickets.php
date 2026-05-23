<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Tickets {
	public static function init() {
		add_action( 'wp_ajax_gma_save_ticket',        array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_ticket',       array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_ticket',          array( __CLASS__, 'get_one' ) );
		add_action( 'wp_ajax_gma_reply_ticket',        array( __CLASS__, 'reply' ) );
		add_action( 'wp_ajax_gma_update_ticket_status',array( __CLASS__, 'update_status' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$data = array(
			'school_id' => GMA_Helper::get_current_school_id(),
			'user_id'   => get_current_user_id(),
			'subject'   => GMA_Helper::post( 'subject' ),
			'message'   => GMA_Helper::post( 'message', 'html' ),
			'priority'  => GMA_Helper::post( 'priority' ) ?: 'normal',
			'status'    => 'open',
			'created_at'=> current_time( 'mysql' ),
		);
		$wpdb->insert( GMA_TABLE_TICKETS, $data );
		wp_send_json_success( array( 'message' => __( 'Ticket submitted.', 'gma-school' ), 'id' => $wpdb->insert_id ) );
	}
	public static function delete() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$wpdb->delete( GMA_TABLE_TICKETS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Ticket deleted.', 'gma-school' ) ) );
	}
	public static function get_one() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id  = GMA_Helper::post( 'id', 'int' );
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT t.*, u.display_name AS user_name FROM " . GMA_TABLE_TICKETS . " t LEFT JOIN {$wpdb->users} u ON t.user_id=u.ID WHERE t.ID=%d", $id ) );
		if ( $row ) {
			$row->replies = $wpdb->get_results( $wpdb->prepare( "SELECT r.*, u.display_name AS user_name FROM " . GMA_TABLE_TICKET_REPLIES . " r LEFT JOIN {$wpdb->users} u ON r.user_id=u.ID WHERE r.ticket_id=%d ORDER BY r.created_at", $id ) );
			wp_send_json_success( $row );
		} else { wp_send_json_error(); }
	}
	public static function reply() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$ticket_id = GMA_Helper::post( 'ticket_id', 'int' );
		$data = array(
			'ticket_id'  => $ticket_id,
			'user_id'    => get_current_user_id(),
			'message'    => GMA_Helper::post( 'message', 'html' ),
			'created_at' => current_time( 'mysql' ),
		);
		if ( ! empty( $_FILES['attachment']['name'] ) ) { $u = GMA_Helper::handle_upload( $_FILES['attachment'], 'gma-school/tickets' ); if ( ! is_wp_error( $u ) ) $data['attachment'] = $u; }
		$wpdb->insert( GMA_TABLE_TICKET_REPLIES, $data );
		wp_send_json_success( array( 'message' => __( 'Reply posted.', 'gma-school' ), 'id' => $wpdb->insert_id ) );
	}
	public static function update_status() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->update( GMA_TABLE_TICKETS, array( 'status' => GMA_Helper::post( 'status' ) ), array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Status updated.', 'gma-school' ) ) );
	}
}
GMA_Module_Tickets::init();
