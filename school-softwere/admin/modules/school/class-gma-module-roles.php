<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Roles {
	public static function init() {
		add_action( 'wp_ajax_gma_save_role',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_role', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_roles',   array( __CLASS__, 'get_all' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$perms = isset( $_POST['permissions'] ) && is_array( $_POST['permissions'] ) ? array_map( 'sanitize_text_field', $_POST['permissions'] ) : array();
		$data = array(
			'school_id'   => GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id(),
			'label'       => GMA_Helper::post( 'label' ),
			'permissions' => wp_json_encode( $perms ),
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_ROLES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Role updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_ROLES, $data ); wp_send_json_success( array( 'message' => __( 'Role created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$wpdb->delete( GMA_TABLE_ROLES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Role deleted.', 'gma-school' ) ) );
	}
	public static function get_all() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$sid = GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id();
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_ROLES . " WHERE school_id=%d ORDER BY label", $sid ) ) );
	}
}
GMA_Module_Roles::init();
