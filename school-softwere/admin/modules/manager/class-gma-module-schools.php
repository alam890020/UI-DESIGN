<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Schools {
	public static function init() {
		add_action( 'wp_ajax_gma_save_school',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_school', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_toggle_school', array( __CLASS__, 'toggle' ) );
		add_action( 'wp_ajax_gma_get_school',    array( __CLASS__, 'get_one' ) );
		add_action( 'wp_ajax_gma_get_schools',   array( __CLASS__, 'get_all' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$id   = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'label'               => GMA_Helper::post( 'label' ),
			'phone'               => GMA_Helper::post( 'phone' ),
			'email'               => GMA_Helper::post( 'email', 'email' ),
			'address'             => GMA_Helper::post( 'address' ),
			'description'         => GMA_Helper::post( 'description', 'html' ),
			'registration_number' => GMA_Helper::post( 'registration_number' ),
			'is_active'           => GMA_Helper::post( 'is_active', 'int' ),
			'admission_prefix'    => GMA_Helper::post( 'admission_prefix' ) ?: 'ADM',
			'admission_base'      => GMA_Helper::post( 'admission_base', 'int' ) ?: 1000,
			'admission_padding'   => GMA_Helper::post( 'admission_padding', 'int' ) ?: 6,
		);
		if ( ! empty( $_FILES['logo']['name'] ) ) {
			$u = GMA_Helper::handle_upload( $_FILES['logo'], 'gma-school/logos' );
			if ( ! is_wp_error( $u ) ) $data['logo'] = $u;
		}
		if ( $id ) {
			$wpdb->update( GMA_TABLE_SCHOOLS, $data, array( 'ID' => $id ) );
			GMA_Database::log( 'school_updated', 'School ID:' . $id );
			wp_send_json_success( array( 'message' => __( 'School updated.', 'gma-school' ) ) );
		} else {
			$data['created_at'] = current_time( 'mysql' );
			$wpdb->insert( GMA_TABLE_SCHOOLS, $data );
			GMA_Database::log( 'school_created', $data['label'] );
			wp_send_json_success( array( 'message' => __( 'School created.', 'gma-school' ), 'id' => $wpdb->insert_id ) );
		}
	}
	public static function delete() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$wpdb->delete( GMA_TABLE_SCHOOLS, array( 'ID' => $id ) );
		wp_send_json_success( array( 'message' => __( 'School deleted.', 'gma-school' ) ) );
	}
	public static function toggle() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$v  = GMA_Helper::post( 'is_active', 'int' );
		$wpdb->update( GMA_TABLE_SCHOOLS, array( 'is_active' => $v ), array( 'ID' => $id ) );
		wp_send_json_success( array( 'message' => $v ? __( 'Activated.', 'gma-school' ) : __( 'Deactivated.', 'gma-school' ) ) );
	}
	public static function get_one() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_SCHOOLS . " WHERE ID=%d", GMA_Helper::post( 'id', 'int' ) ) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error( array( 'message' => __( 'Not found.', 'gma-school' ) ) );
	}
	public static function get_all() {
		GMA_Helper::verify_nonce(); global $wpdb;
		wp_send_json_success( $wpdb->get_results( "SELECT ID, label FROM " . GMA_TABLE_SCHOOLS . " WHERE is_active=1 ORDER BY label" ) );
	}
}
GMA_Module_Schools::init();
