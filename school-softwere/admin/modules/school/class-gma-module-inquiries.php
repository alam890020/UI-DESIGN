<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Inquiries {
	public static function init() {
		add_action( 'wp_ajax_gma_save_inquiry',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_inquiry', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_update_inquiry_status', array( __CLASS__, 'update_status' ) );
		add_action( 'wp_ajax_nopriv_gma_submit_inquiry', array( __CLASS__, 'public_submit' ) );
		add_action( 'wp_ajax_gma_submit_inquiry',        array( __CLASS__, 'public_submit' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'school_id' => GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id(),
			'name'    => GMA_Helper::post( 'name' ),
			'email'   => GMA_Helper::post( 'email', 'email' ),
			'phone'   => GMA_Helper::post( 'phone' ),
			'message' => GMA_Helper::post( 'message' ),
			'source'  => GMA_Helper::post( 'source' ),
			'status'  => GMA_Helper::post( 'status' ) ?: 'new',
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_INQUIRIES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Inquiry updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_INQUIRIES, $data ); wp_send_json_success( array( 'message' => __( 'Inquiry added.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$wpdb->delete( GMA_TABLE_INQUIRIES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Deleted.', 'gma-school' ) ) );
	}
	public static function update_status() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->update( GMA_TABLE_INQUIRIES, array( 'status' => GMA_Helper::post( 'status' ) ), array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Status updated.', 'gma-school' ) ) );
	}
	public static function public_submit() {
		if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'gma_contact_form' ) ) wp_send_json_error( array( 'message' => __( 'Security check failed.', 'gma-school' ) ) );
		global $wpdb;
		$wpdb->insert( GMA_TABLE_INQUIRIES, array(
			'school_id'  => 1,
			'name'       => sanitize_text_field( $_POST['name'] ?? '' ),
			'email'      => sanitize_email( $_POST['email'] ?? '' ),
			'phone'      => sanitize_text_field( $_POST['phone'] ?? '' ),
			'message'    => sanitize_textarea_field( $_POST['message'] ?? '' ),
			'source'     => 'website',
			'status'     => 'new',
			'created_at' => current_time( 'mysql' ),
		) );
		wp_send_json_success( array( 'message' => __( 'Your inquiry has been submitted! We will get back to you soon.', 'gma-school' ) ) );
	}
}
GMA_Module_Inquiries::init();
