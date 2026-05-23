<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Admissions {
	public static function init() {
		add_action( 'wp_ajax_gma_save_admission',    array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_admission',  array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_admission',     array( __CLASS__, 'get_one' ) );
		add_action( 'wp_ajax_gma_approve_admission', array( __CLASS__, 'approve' ) );
		add_action( 'wp_ajax_gma_reject_admission',  array( __CLASS__, 'reject' ) );
		// Public form (no_priv)
		add_action( 'wp_ajax_nopriv_gma_submit_admission', array( __CLASS__, 'public_submit' ) );
		add_action( 'wp_ajax_gma_submit_admission',        array( __CLASS__, 'public_submit' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$school_id = GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id();
		$data = array(
			'school_id'   => $school_id,
			'first_name'  => GMA_Helper::post( 'first_name' ),
			'last_name'   => GMA_Helper::post( 'last_name' ),
			'father_name' => GMA_Helper::post( 'father_name' ),
			'dob'         => GMA_Helper::post( 'dob' ) ?: null,
			'gender'      => GMA_Helper::post( 'gender' ),
			'phone'       => GMA_Helper::post( 'phone' ),
			'email'       => GMA_Helper::post( 'email', 'email' ),
			'class_id'    => GMA_Helper::post( 'class_id', 'int' ) ?: null,
			'address'     => GMA_Helper::post( 'address' ),
			'note'        => GMA_Helper::post( 'note' ),
			'status'      => GMA_Helper::post( 'status' ) ?: 'pending',
		);
		if ( ! empty( $_FILES['photo']['name'] ) ) {
			$u = GMA_Helper::handle_upload( $_FILES['photo'], 'gma-school/admissions' );
			if ( ! is_wp_error( $u ) ) $data['photo'] = $u;
		}
		if ( $id ) { $wpdb->update( GMA_TABLE_ADMISSIONS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Admission updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_ADMISSIONS, $data ); wp_send_json_success( array( 'message' => __( 'Admission saved.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$wpdb->delete( GMA_TABLE_ADMISSIONS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Admission deleted.', 'gma-school' ) ) );
	}
	public static function get_one() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_ADMISSIONS . " WHERE ID=%d", GMA_Helper::post( 'id', 'int' ) ) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
	public static function approve() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->update( GMA_TABLE_ADMISSIONS, array( 'status' => 'approved' ), array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Admission approved.', 'gma-school' ) ) );
	}
	public static function reject() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->update( GMA_TABLE_ADMISSIONS, array( 'status' => 'rejected' ), array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Admission rejected.', 'gma-school' ) ) );
	}
	public static function public_submit() {
		if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'gma_admission_form' ) ) wp_send_json_error( array( 'message' => __( 'Security check failed.', 'gma-school' ) ) );
		global $wpdb;
		$school_id = (int) ( $_POST['school_id'] ?? 1 );
		$data = array(
			'school_id'   => $school_id,
			'first_name'  => sanitize_text_field( $_POST['first_name'] ?? '' ),
			'last_name'   => sanitize_text_field( $_POST['last_name'] ?? '' ),
			'father_name' => sanitize_text_field( $_POST['father_name'] ?? '' ),
			'phone'       => sanitize_text_field( $_POST['phone'] ?? '' ),
			'email'       => sanitize_email( $_POST['email'] ?? '' ),
			'class_id'    => (int) ( $_POST['class_id'] ?? 0 ) ?: null,
			'address'     => sanitize_textarea_field( $_POST['address'] ?? '' ),
			'status'      => 'pending',
			'created_at'  => current_time( 'mysql' ),
		);
		$wpdb->insert( GMA_TABLE_ADMISSIONS, $data );
		wp_send_json_success( array( 'message' => __( 'Your admission form has been submitted! We will contact you soon.', 'gma-school' ) ) );
	}
}
GMA_Module_Admissions::init();
