<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Homework {
	public static function init() {
		add_action( 'wp_ajax_gma_save_homework',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_homework', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_homework',    array( __CLASS__, 'get_one' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'class_school_id' => GMA_Helper::post( 'class_school_id', 'int' ),
			'section_id'      => GMA_Helper::post( 'section_id', 'int' ),
			'subject_id'      => GMA_Helper::post( 'subject_id', 'int' ),
			'staff_id'        => GMA_Helper::post( 'staff_id', 'int' ) ?: null,
			'title'           => GMA_Helper::post( 'title' ),
			'description'     => GMA_Helper::post( 'description', 'html' ),
			'submission_date' => GMA_Helper::post( 'submission_date' ) ?: null,
		);
		if ( ! empty( $_FILES['attachment']['name'] ) ) {
			$u = GMA_Helper::handle_upload( $_FILES['attachment'], 'gma-school/homework' );
			if ( ! is_wp_error( $u ) ) $data['attachment'] = $u;
		}
		if ( $id ) { $wpdb->update( GMA_TABLE_HOMEWORK, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Homework updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_HOMEWORK, $data ); wp_send_json_success( array( 'message' => __( 'Homework assigned.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_HOMEWORK, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Deleted.', 'gma-school' ) ) );
	}
	public static function get_one() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_HOMEWORK . " WHERE ID=%d", GMA_Helper::post( 'id', 'int' ) ) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
}
GMA_Module_Homework::init();
