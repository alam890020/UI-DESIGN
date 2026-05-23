<?php
/**
 * SS_School — Super-admin school management AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_School {
	public static function init() {
		add_action( 'wp_ajax_ss_save_school',   array( __CLASS__, 'save_school' ) );
		add_action( 'wp_ajax_ss_delete_school', array( __CLASS__, 'delete_school' ) );
		add_action( 'wp_ajax_ss_toggle_school', array( __CLASS__, 'toggle_school' ) );
		add_action( 'wp_ajax_ss_assign_admin',  array( __CLASS__, 'assign_admin' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Permission denied.', 'school-softwere' ) ) );
	}

	public static function save_school() {
		self::verify(); global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array(
			'label'               => SS_Helper::post( 'label' ),
			'phone'               => SS_Helper::post( 'phone' ),
			'email'               => SS_Helper::post( 'email', 'email' ),
			'address'             => SS_Helper::post( 'address' ),
			'description'         => SS_Helper::post( 'description', 'html' ),
			'registration_number' => SS_Helper::post( 'registration_number' ),
			'category_id'         => SS_Helper::post( 'category_id', 'int' ) ?: null,
			'is_active'           => SS_Helper::post( 'is_active', 'int' ),
			'admission_prefix'    => SS_Helper::post( 'admission_prefix' ) ?: 'ADM',
			'admission_base'      => SS_Helper::post( 'admission_base', 'int' ) ?: 1000,
			'admission_padding'   => SS_Helper::post( 'admission_padding', 'int' ) ?: 6,
		);
		if ( ! empty( $_FILES['logo']['name'] ) ) { $u = SS_Helper::handle_upload( $_FILES['logo'], 'school-softwere/logos' ); if ( ! is_wp_error( $u ) ) $data['logo'] = $u; }
		if ( $id ) {
			$wpdb->update( SS_TABLE_SCHOOLS, $data, array( 'ID' => $id ) );
			SS_Database::log( 'school_updated', 'School ID: ' . $id );
			wp_send_json( array( 'success' => true, 'message' => __( 'School updated.', 'school-softwere' ) ) );
		} else {
			$data['created_at'] = current_time( 'mysql' );
			$wpdb->insert( SS_TABLE_SCHOOLS, $data );
			SS_Database::log( 'school_created', 'School: ' . $data['label'] );
			wp_send_json( array( 'success' => true, 'message' => __( 'School created.', 'school-softwere' ), 'data' => array( 'id' => $wpdb->insert_id ) ) );
		}
	}

	public static function delete_school() {
		self::verify(); global $wpdb;
		$id = SS_Helper::post( 'id', 'int' );
		$wpdb->delete( SS_TABLE_SCHOOLS, array( 'ID' => $id ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'School deleted.', 'school-softwere' ) ) );
	}

	public static function toggle_school() {
		self::verify(); global $wpdb;
		$id     = SS_Helper::post( 'id', 'int' );
		$active = SS_Helper::post( 'is_active', 'int' );
		$wpdb->update( SS_TABLE_SCHOOLS, array( 'is_active' => $active ), array( 'ID' => $id ) );
		wp_send_json( array( 'success' => true, 'message' => $active ? __( 'School activated.', 'school-softwere' ) : __( 'School deactivated.', 'school-softwere' ) ) );
	}

	public static function assign_admin() {
		self::verify(); global $wpdb;
		$school_id = SS_Helper::post( 'school_id', 'int' );
		$user_id   = SS_Helper::post( 'user_id', 'int' );
		$exists    = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . SS_TABLE_ADMINS . " WHERE school_id=%d AND user_id=%d", $school_id, $user_id ) );
		if ( ! $exists ) $wpdb->insert( SS_TABLE_ADMINS, array( 'school_id' => $school_id, 'user_id' => $user_id, 'created_at' => current_time( 'mysql' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Admin assigned.', 'school-softwere' ) ) );
	}
}
SS_School::init();
