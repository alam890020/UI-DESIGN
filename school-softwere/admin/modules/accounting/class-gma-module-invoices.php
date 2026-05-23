<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Invoices {
	public static function init() {
		add_action( 'wp_ajax_gma_create_invoice',  array( __CLASS__, 'create' ) );
		add_action( 'wp_ajax_gma_delete_invoice',  array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_invoice',     array( __CLASS__, 'get_one' ) );
		add_action( 'wp_ajax_gma_get_invoices',    array( __CLASS__, 'get_all' ) );
	}
	public static function create() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id        = GMA_Helper::post( 'id', 'int' );
		$school_id = GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id();
		$student_id = GMA_Helper::post( 'student_record_id', 'int' );
		$fee_ids    = array_map( 'intval', (array) ( $_POST['fee_ids'] ?? array() ) );
		$discount   = GMA_Helper::post( 'discount', 'float' );
		if ( ! $student_id ) wp_send_json_error( array( 'message' => __( 'Student is required.', 'gma-school' ) ) );
		$student  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_STUDENT_RECORDS . " WHERE ID=%d", $student_id ) );
		$total    = 0;
		foreach ( $fee_ids as $fid ) {
			$fee   = $wpdb->get_row( $wpdb->prepare( "SELECT amount FROM " . GMA_TABLE_FEES . " WHERE ID=%d", $fid ) );
			$total += $fee ? (float) $fee->amount : 0;
		}
		$total    -= $discount;
		$data = array(
			'school_id'         => $school_id,
			'student_record_id' => $student_id,
			'class_school_id'   => $student->class_school_id,
			'total_amount'      => $total,
			'paid_amount'       => 0,
			'due_amount'        => $total,
			'discount'          => $discount,
			'status'            => 'unpaid',
			'due_date'          => GMA_Helper::post( 'due_date' ) ?: null,
			'note'              => GMA_Helper::post( 'note' ),
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_INVOICES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Invoice updated.', 'gma-school' ) ) ); }
		else {
			$data['invoice_number'] = GMA_Helper::generate_invoice_number( $school_id );
			$data['created_at']     = current_time( 'mysql' );
			$wpdb->insert( GMA_TABLE_INVOICES, $data );
			GMA_Database::log( 'invoice_created', $data['invoice_number'], $school_id );
			wp_send_json_success( array( 'message' => __( 'Invoice created.', 'gma-school' ), 'id' => $wpdb->insert_id, 'invoice_number' => $data['invoice_number'] ) );
		}
	}
	public static function delete() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$wpdb->delete( GMA_TABLE_INVOICES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Invoice deleted.', 'gma-school' ) ) );
	}
	public static function get_one() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id  = GMA_Helper::post( 'id', 'int' );
		$row = $wpdb->get_row( $wpdb->prepare(
			"SELECT i.*, CONCAT(sr.first_name,' ',sr.last_name) AS student_name, sr.admission_number, sr.phone AS student_phone FROM " . GMA_TABLE_INVOICES . " i INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON i.student_record_id=sr.ID WHERE i.ID=%d", $id
		) );
		if ( $row ) {
			$row->payments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_PAYMENTS . " WHERE invoice_id=%d ORDER BY payment_date", $id ) );
			wp_send_json_success( $row );
		} else { wp_send_json_error(); }
	}
	public static function get_all() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$school_id = GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id();
		$status    = GMA_Helper::post( 'status' );
		$where     = $wpdb->prepare( "WHERE i.school_id=%d", $school_id );
		if ( $status ) $where .= $wpdb->prepare( " AND i.status=%s", $status );
		wp_send_json_success( $wpdb->get_results( "SELECT i.*, CONCAT(sr.first_name,' ',sr.last_name) AS student_name FROM " . GMA_TABLE_INVOICES . " i INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON i.student_record_id=sr.ID $where ORDER BY i.created_at DESC LIMIT 100" ) );
	}
}
GMA_Module_Invoices::init();
