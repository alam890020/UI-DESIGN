<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Payments {
	public static function init() {
		add_action( 'wp_ajax_gma_collect_payment',  array( __CLASS__, 'collect' ) );
		add_action( 'wp_ajax_gma_delete_payment',   array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_payment_history', array( __CLASS__, 'history' ) );
	}
	public static function collect() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$invoice_id = GMA_Helper::post( 'invoice_id', 'int' );
		$amount     = GMA_Helper::post( 'amount', 'float' );
		if ( ! $invoice_id || $amount <= 0 ) wp_send_json_error( array( 'message' => __( 'Invalid payment data.', 'gma-school' ) ) );
		$invoice = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_INVOICES . " WHERE ID=%d", $invoice_id ) );
		if ( ! $invoice ) wp_send_json_error( array( 'message' => __( 'Invoice not found.', 'gma-school' ) ) );
		$wpdb->insert( GMA_TABLE_PAYMENTS, array(
			'invoice_id'     => $invoice_id,
			'amount'         => $amount,
			'payment_method' => GMA_Helper::post( 'payment_method' ) ?: 'cash',
			'transaction_id' => GMA_Helper::post( 'transaction_id' ),
			'payment_date'   => GMA_Helper::post( 'payment_date' ) ?: current_time( 'Y-m-d' ),
			'note'           => GMA_Helper::post( 'note' ),
			'created_at'     => current_time( 'mysql' ),
		) );
		$new_paid = (float) $invoice->paid_amount + $amount;
		$new_due  = (float) $invoice->total_amount - $new_paid;
		$status   = $new_due <= 0 ? 'paid' : 'partial';
		$wpdb->update( GMA_TABLE_INVOICES, array( 'paid_amount' => $new_paid, 'due_amount' => max( 0, $new_due ), 'status' => $status ), array( 'ID' => $invoice_id ) );
		GMA_Database::log( 'payment_collected', "Invoice #{$invoice->invoice_number}, Amount: {$amount}", $invoice->school_id );
		wp_send_json_success( array( 'message' => __( 'Payment recorded.', 'gma-school' ), 'status' => $status, 'new_due' => max( 0, $new_due ) ) );
	}
	public static function delete() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$id      = GMA_Helper::post( 'id', 'int' );
		$payment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_PAYMENTS . " WHERE ID=%d", $id ) );
		if ( $payment ) {
			$wpdb->delete( GMA_TABLE_PAYMENTS, array( 'ID' => $id ) );
			$invoice    = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_INVOICES . " WHERE ID=%d", $payment->invoice_id ) );
			if ( $invoice ) {
				$new_paid = max( 0, (float) $invoice->paid_amount - (float) $payment->amount );
				$new_due  = (float) $invoice->total_amount - $new_paid;
				$status   = $new_paid <= 0 ? 'unpaid' : ( $new_due <= 0 ? 'paid' : 'partial' );
				$wpdb->update( GMA_TABLE_INVOICES, array( 'paid_amount' => $new_paid, 'due_amount' => $new_due, 'status' => $status ), array( 'ID' => $payment->invoice_id ) );
			}
		}
		wp_send_json_success( array( 'message' => __( 'Payment deleted.', 'gma-school' ) ) );
	}
	public static function history() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$school_id = GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id();
		$from      = GMA_Helper::post( 'from_date' );
		$to        = GMA_Helper::post( 'to_date' );
		$where     = $wpdb->prepare( "WHERE i.school_id=%d", $school_id );
		if ( $from && $to ) $where .= $wpdb->prepare( " AND p.payment_date BETWEEN %s AND %s", $from, $to );
		wp_send_json_success( $wpdb->get_results( "SELECT p.*, i.invoice_number, CONCAT(sr.first_name,' ',sr.last_name) AS student_name FROM " . GMA_TABLE_PAYMENTS . " p INNER JOIN " . GMA_TABLE_INVOICES . " i ON p.invoice_id=i.ID INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON i.student_record_id=sr.ID $where ORDER BY p.payment_date DESC LIMIT 200" ) );
	}
}
GMA_Module_Payments::init();
