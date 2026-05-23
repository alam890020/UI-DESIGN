<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Certificates {
	public static function init() {
		add_action( 'wp_ajax_gma_save_certificate',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_certificate', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_issue_certificate',  array( __CLASS__, 'issue' ) );
		add_action( 'wp_ajax_gma_get_certificate',    array( __CLASS__, 'get_one' ) );
		add_action( 'wp_ajax_gma_save_tc',            array( __CLASS__, 'save_tc' ) );
		add_action( 'wp_ajax_gma_get_tc',             array( __CLASS__, 'get_tc' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'school_id'        => GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id(),
			'label'            => GMA_Helper::post( 'label' ),
			'content_template' => GMA_Helper::post( 'content_template', 'html' ),
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_CERTIFICATES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Certificate updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_CERTIFICATES, $data ); wp_send_json_success( array( 'message' => __( 'Certificate created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$wpdb->delete( GMA_TABLE_CERTIFICATES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Deleted.', 'gma-school' ) ) );
	}
	public static function issue() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$cert_id    = GMA_Helper::post( 'certificate_id', 'int' );
		$student_id = GMA_Helper::post( 'student_record_id', 'int' );
		$wpdb->insert( GMA_TABLE_CERTIFICATE_STUDENT, array( 'certificate_id' => $cert_id, 'student_record_id' => $student_id, 'issued_date' => current_time( 'Y-m-d' ), 'created_at' => current_time( 'mysql' ) ) );
		wp_send_json_success( array( 'message' => __( 'Certificate issued.', 'gma-school' ), 'id' => $wpdb->insert_id ) );
	}
	public static function get_one() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_CERTIFICATES . " WHERE ID=%d", GMA_Helper::post( 'id', 'int' ) ) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
	public static function save_tc() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$data = array(
			'student_record_id' => GMA_Helper::post( 'student_record_id', 'int' ),
			'reason'            => GMA_Helper::post( 'reason' ),
			'issued_date'       => GMA_Helper::post( 'issued_date' ) ?: current_time( 'Y-m-d' ),
			'created_at'        => current_time( 'mysql' ),
		);
		$wpdb->insert( GMA_TABLE_TRANSFER_CERTS, $data );
		wp_send_json_success( array( 'message' => __( 'Transfer certificate issued.', 'gma-school' ), 'id' => $wpdb->insert_id ) );
	}
	public static function get_tc() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$row = $wpdb->get_row( $wpdb->prepare(
			"SELECT tc.*, CONCAT(sr.first_name,' ',sr.last_name) AS student_name, sr.admission_number FROM " . GMA_TABLE_TRANSFER_CERTS . " tc INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON tc.student_record_id=sr.ID WHERE tc.ID=%d", $id
		) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
}
GMA_Module_Certificates::init();
