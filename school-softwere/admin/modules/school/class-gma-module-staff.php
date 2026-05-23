<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Staff {
	public static function init() {
		add_action( 'wp_ajax_gma_save_staff',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_staff', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_staff',    array( __CLASS__, 'get_one' ) );
		add_action( 'wp_ajax_gma_get_all_staff',array( __CLASS__, 'get_all' ) );
		add_action( 'wp_ajax_gma_bulk_staff',   array( __CLASS__, 'bulk' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$school_id = GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id();
		$data = array(
			'school_id'    => $school_id,
			'role_id'      => GMA_Helper::post( 'role_id', 'int' ) ?: null,
			'first_name'   => GMA_Helper::post( 'first_name' ),
			'last_name'    => GMA_Helper::post( 'last_name' ),
			'dob'          => GMA_Helper::post( 'dob' ) ?: null,
			'gender'       => GMA_Helper::post( 'gender' ),
			'phone'        => GMA_Helper::post( 'phone' ),
			'email'        => GMA_Helper::post( 'email', 'email' ),
			'address'      => GMA_Helper::post( 'address' ),
			'joining_date' => GMA_Helper::post( 'joining_date' ) ?: null,
			'designation'  => GMA_Helper::post( 'designation' ),
			'qualification'=> GMA_Helper::post( 'qualification' ),
			'salary'       => GMA_Helper::post( 'salary', 'float' ),
			'is_active'    => GMA_Helper::post( 'is_active', 'int' ) ?: 1,
		);
		if ( ! empty( $_FILES['photo']['name'] ) ) {
			$u = GMA_Helper::handle_upload( $_FILES['photo'], 'gma-school/staff' );
			if ( ! is_wp_error( $u ) ) $data['photo'] = $u;
		}
		if ( $id ) { $wpdb->update( GMA_TABLE_STAFF, $data, array( 'ID' => $id ) ); GMA_Database::log( 'staff_updated', 'ID:' . $id, $school_id ); wp_send_json_success( array( 'message' => __( 'Staff updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_STAFF, $data ); GMA_Database::log( 'staff_added', $data['first_name'], $school_id ); wp_send_json_success( array( 'message' => __( 'Staff added.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$wpdb->delete( GMA_TABLE_STAFF, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Staff deleted.', 'gma-school' ) ) );
	}
	public static function get_one() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_STAFF . " WHERE ID=%d", GMA_Helper::post( 'id', 'int' ) ) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
	public static function get_all() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$sid = GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id();
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare( "SELECT ID, first_name, last_name, designation FROM " . GMA_TABLE_STAFF . " WHERE school_id=%d AND is_active=1 ORDER BY first_name", $sid ) ) );
	}
	public static function bulk() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$action = GMA_Helper::post( 'bulk_action' );
		$ids    = array_map( 'intval', (array) ( $_POST['ids'] ?? array() ) );
		if ( empty( $ids ) ) wp_send_json_error( array( 'message' => __( 'No items selected.', 'gma-school' ) ) );
		$ph = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
		if ( $action === 'delete' ) { $wpdb->query( $wpdb->prepare( "DELETE FROM " . GMA_TABLE_STAFF . " WHERE ID IN ($ph)", $ids ) ); wp_send_json_success( array( 'message' => __( 'Deleted.', 'gma-school' ) ) ); } // phpcs:ignore
		wp_send_json_error( array( 'message' => __( 'Unknown action.', 'gma-school' ) ) );
	}
}
GMA_Module_Staff::init();
