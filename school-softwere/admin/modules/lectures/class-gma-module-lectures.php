<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Lectures {
	public static function init() {
		add_action( 'wp_ajax_gma_save_lecture',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_lecture', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_lecture',    array( __CLASS__, 'get_one' ) );
		add_action( 'wp_ajax_gma_get_lectures',   array( __CLASS__, 'get_all' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'chapter_id'  => GMA_Helper::post( 'chapter_id', 'int' ),
			'staff_id'    => GMA_Helper::post( 'staff_id', 'int' ) ?: null,
			'title'       => GMA_Helper::post( 'title' ),
			'description' => GMA_Helper::post( 'description', 'html' ),
			'video_url'   => GMA_Helper::post( 'video_url', 'url' ),
		);
		if ( ! empty( $_FILES['attachment']['name'] ) ) { $u = GMA_Helper::handle_upload( $_FILES['attachment'], 'gma-school/lectures' ); if ( ! is_wp_error( $u ) ) $data['attachment'] = $u; }
		if ( $id ) { $wpdb->update( GMA_TABLE_LECTURES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Lecture updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_LECTURES, $data ); wp_send_json_success( array( 'message' => __( 'Lecture added.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_LECTURES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Lecture deleted.', 'gma-school' ) ) );
	}
	public static function get_one() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_LECTURES . " WHERE ID=%d", GMA_Helper::post( 'id', 'int' ) ) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
	public static function get_all() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$chapter_id = GMA_Helper::post( 'chapter_id', 'int' );
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare( "SELECT l.*, CONCAT(s.first_name,' ',s.last_name) AS staff_name FROM " . GMA_TABLE_LECTURES . " l LEFT JOIN " . GMA_TABLE_STAFF . " s ON l.staff_id=s.ID WHERE l.chapter_id=%d ORDER BY l.created_at", $chapter_id ) ) );
	}
}
GMA_Module_Lectures::init();
