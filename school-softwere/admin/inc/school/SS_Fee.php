<?php
/**
 * SS_Fee — Fee & Accounting AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Fee {

	public static function init() {
		add_action( 'wp_ajax_ss_save_fee',            array( __CLASS__, 'save_fee' ) );
		add_action( 'wp_ajax_ss_delete_fee',          array( __CLASS__, 'delete_fee' ) );
		add_action( 'wp_ajax_ss_generate_invoice',    array( __CLASS__, 'generate_invoice' ) );
		add_action( 'wp_ajax_ss_collect_payment',     array( __CLASS__, 'collect_payment' ) );
		add_action( 'wp_ajax_ss_get_invoice',         array( __CLASS__, 'get_invoice' ) );
		add_action( 'wp_ajax_ss_save_expense',        array( __CLASS__, 'save_expense' ) );
		add_action( 'wp_ajax_ss_delete_expense',      array( __CLASS__, 'delete_expense' ) );
		add_action( 'wp_ajax_ss_save_income',         array( __CLASS__, 'save_income' ) );
		add_action( 'wp_ajax_ss_delete_income',       array( __CLASS__, 'delete_income' ) );
		add_action( 'wp_ajax_ss_save_concession_type',array( __CLASS__, 'save_concession_type' ) );
		add_action( 'wp_ajax_ss_assign_concession',   array( __CLASS__, 'assign_concession' ) );
		add_action( 'wp_ajax_ss_bulk_generate_invoices',array( __CLASS__, 'bulk_generate_invoices' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
	}

	public static function save_fee() {
		self::verify();
		global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array(
			'class_school_id' => SS_Helper::post( 'class_school_id', 'int' ),
			'label'           => SS_Helper::post( 'label' ),
			'amount'          => SS_Helper::post( 'amount', 'float' ),
			'due_date'        => SS_Helper::post( 'due_date' ) ?: null,
			'is_recurring'    => SS_Helper::post( 'is_recurring', 'int' ),
			'frequency'       => SS_Helper::post( 'frequency' ) ?: 'monthly',
		);
		if ( $id ) {
			$wpdb->update( SS_TABLE_FEES, $data, array( 'ID' => $id ) );
			wp_send_json( array( 'success' => true, 'message' => __( 'Fee updated.', 'school-softwere' ) ) );
		} else {
			$data['created_at'] = current_time( 'mysql' );
			$wpdb->insert( SS_TABLE_FEES, $data );
			wp_send_json( array( 'success' => true, 'message' => __( 'Fee created.', 'school-softwere' ), 'data' => array( 'id' => $wpdb->insert_id ) ) );
		}
	}

	public static function delete_fee() {
		self::verify();
		global $wpdb;
		$wpdb->delete( SS_TABLE_FEES, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Fee deleted.', 'school-softwere' ) ) );
	}

	public static function generate_invoice() {
		self::verify();
		global $wpdb;
		$student_id      = SS_Helper::post( 'student_record_id', 'int' );
		$school_id       = SS_Helper::post( 'school_id', 'int' );
		$class_school_id = SS_Helper::post( 'class_school_id', 'int' );
		$fee_ids         = array_map( 'intval', (array) ( $_POST['fee_ids'] ?? array() ) );

		if ( empty( $fee_ids ) ) {
			wp_send_json( array( 'success' => false, 'message' => __( 'Select at least one fee.', 'school-softwere' ) ) );
		}

		$total = 0;
		foreach ( $fee_ids as $fid ) {
			$fee = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . SS_TABLE_FEES . " WHERE ID=%d", $fid ) );
			if ( $fee ) {
				$total += (float) $fee->amount;
				// Apply concession if any
				$concession = $wpdb->get_row( $wpdb->prepare(
					"SELECT cfm.amount FROM " . SS_TABLE_STUDENT_CONCESSION . " sc
					 INNER JOIN " . SS_TABLE_CONCESSION_FEE_MAPPINGS . " cfm ON sc.concession_type_id=cfm.concession_type_id
					 WHERE sc.student_record_id=%d AND cfm.fee_id=%d LIMIT 1",
					$student_id, $fid
				) );
				if ( $concession ) $total -= (float) $concession->amount;
			}
		}
		$total = max( 0, $total );
		$inv_num = SS_Helper::generate_invoice_number( $school_id );

		$wpdb->insert( SS_TABLE_INVOICES, array(
			'school_id'         => $school_id,
			'student_record_id' => $student_id,
			'class_school_id'   => $class_school_id,
			'invoice_number'    => $inv_num,
			'total_amount'      => $total,
			'paid_amount'       => 0,
			'due_amount'        => $total,
			'status'            => 'unpaid',
			'due_date'          => SS_Helper::post( 'due_date' ) ?: null,
			'created_at'        => current_time( 'mysql' ),
		) );
		$inv_id = $wpdb->insert_id;

		foreach ( $fee_ids as $fid ) {
			$fee = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . SS_TABLE_FEES . " WHERE ID=%d", $fid ) );
			if ( $fee ) {
				$wpdb->insert( SS_TABLE_STUDENT_FEES, array(
					'student_record_id' => $student_id,
					'fee_id'            => $fid,
					'amount'            => $fee->amount,
					'created_at'        => current_time( 'mysql' ),
				) );
			}
		}
		SS_Database::log( 'invoice_generated', 'Invoice: ' . $inv_num, $school_id );
		wp_send_json( array( 'success' => true, 'message' => __( 'Invoice generated.', 'school-softwere' ), 'data' => array( 'invoice_id' => $inv_id, 'invoice_number' => $inv_num, 'total' => $total ) ) );
	}

	public static function collect_payment() {
		self::verify();
		global $wpdb;
		$invoice_id = SS_Helper::post( 'invoice_id', 'int' );
		$amount     = SS_Helper::post( 'amount', 'float' );
		$method     = SS_Helper::post( 'payment_method' ) ?: 'cash';
		$note       = SS_Helper::post( 'note' );

		$invoice = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . SS_TABLE_INVOICES . " WHERE ID=%d", $invoice_id ) );
		if ( ! $invoice ) wp_send_json( array( 'success' => false, 'message' => __( 'Invoice not found.', 'school-softwere' ) ) );

		$wpdb->insert( SS_TABLE_PAYMENTS, array(
			'invoice_id'     => $invoice_id,
			'amount'         => $amount,
			'payment_method' => $method,
			'payment_date'   => current_time( 'Y-m-d' ),
			'note'           => $note,
			'created_at'     => current_time( 'mysql' ),
		) );

		$new_paid = (float) $invoice->paid_amount + $amount;
		$new_due  = max( 0, (float) $invoice->total_amount - $new_paid );
		$status   = $new_due <= 0 ? 'paid' : ( $new_paid > 0 ? 'partial' : 'unpaid' );
		$wpdb->update( SS_TABLE_INVOICES, array( 'paid_amount' => $new_paid, 'due_amount' => $new_due, 'status' => $status ), array( 'ID' => $invoice_id ) );

		SS_Database::log( 'payment_collected', 'Invoice: ' . $invoice->invoice_number . ', Amount: ' . $amount, $invoice->school_id );
		wp_send_json( array( 'success' => true, 'message' => __( 'Payment recorded.', 'school-softwere' ), 'data' => array( 'status' => $status, 'paid' => $new_paid, 'due' => $new_due ) ) );
	}

	public static function get_invoice() {
		self::verify();
		global $wpdb;
		$id  = SS_Helper::post( 'id', 'int' );
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT i.*, CONCAT(sr.first_name,' ',sr.last_name) AS student_name FROM " . SS_TABLE_INVOICES . " i LEFT JOIN " . SS_TABLE_STUDENT_RECORDS . " sr ON i.student_record_id=sr.ID WHERE i.ID=%d", $id ) );
		$row ? wp_send_json( array( 'success' => true, 'data' => $row ) ) : wp_send_json( array( 'success' => false ) );
	}

	public static function save_expense() {
		self::verify();
		global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array(
			'school_id'           => SS_Helper::post( 'school_id', 'int' ),
			'expense_category_id' => SS_Helper::post( 'expense_category_id', 'int' ) ?: null,
			'label'               => SS_Helper::post( 'label' ),
			'amount'              => SS_Helper::post( 'amount', 'float' ),
			'date'                => SS_Helper::post( 'date' ) ?: current_time( 'Y-m-d' ),
			'note'                => SS_Helper::post( 'note' ),
		);
		if ( ! empty( $_FILES['attachment']['name'] ) ) {
			$upload = SS_Helper::handle_upload( $_FILES['attachment'], 'school-softwere/expenses' );
			if ( ! is_wp_error( $upload ) ) $data['attachment'] = $upload;
		}
		if ( $id ) { $wpdb->update( SS_TABLE_EXPENSES, $data, array( 'ID' => $id ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( SS_TABLE_EXPENSES, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Expense saved.', 'school-softwere' ) ) );
	}

	public static function delete_expense() {
		self::verify();
		global $wpdb;
		$wpdb->delete( SS_TABLE_EXPENSES, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Expense deleted.', 'school-softwere' ) ) );
	}

	public static function save_income() {
		self::verify();
		global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array(
			'school_id'          => SS_Helper::post( 'school_id', 'int' ),
			'income_category_id' => SS_Helper::post( 'income_category_id', 'int' ) ?: null,
			'label'              => SS_Helper::post( 'label' ),
			'amount'             => SS_Helper::post( 'amount', 'float' ),
			'date'               => SS_Helper::post( 'date' ) ?: current_time( 'Y-m-d' ),
			'note'               => SS_Helper::post( 'note' ),
		);
		if ( $id ) { $wpdb->update( SS_TABLE_INCOME, $data, array( 'ID' => $id ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( SS_TABLE_INCOME, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Income saved.', 'school-softwere' ) ) );
	}

	public static function delete_income() {
		self::verify();
		global $wpdb;
		$wpdb->delete( SS_TABLE_INCOME, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Income deleted.', 'school-softwere' ) ) );
	}

	public static function save_concession_type() {
		self::verify();
		global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array( 'school_id' => SS_Helper::post( 'school_id', 'int' ), 'label' => SS_Helper::post( 'label' ), 'created_at' => current_time( 'mysql' ) );
		if ( $id ) { $wpdb->update( SS_TABLE_CONCESSION_TYPES, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_CONCESSION_TYPES, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Concession type saved.', 'school-softwere' ) ) );
	}

	public static function assign_concession() {
		self::verify();
		global $wpdb;
		$student_id = SS_Helper::post( 'student_record_id', 'int' );
		$type_id    = SS_Helper::post( 'concession_type_id', 'int' );
		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . SS_TABLE_STUDENT_CONCESSION . " WHERE student_record_id=%d AND concession_type_id=%d", $student_id, $type_id ) );
		if ( ! $exists ) {
			$wpdb->insert( SS_TABLE_STUDENT_CONCESSION, array( 'student_record_id' => $student_id, 'concession_type_id' => $type_id, 'created_at' => current_time( 'mysql' ) ) );
		}
		wp_send_json( array( 'success' => true, 'message' => __( 'Concession assigned.', 'school-softwere' ) ) );
	}

	public static function bulk_generate_invoices() {
		self::verify();
		global $wpdb;
		$school_id       = SS_Helper::post( 'school_id', 'int' );
		$class_school_id = SS_Helper::post( 'class_school_id', 'int' );
		$fee_ids         = array_map( 'intval', (array) ( $_POST['fee_ids'] ?? array() ) );
		$due_date        = SS_Helper::post( 'due_date' ) ?: null;

		$students = $wpdb->get_results( $wpdb->prepare(
			"SELECT ID, class_school_id FROM " . SS_TABLE_STUDENT_RECORDS . " WHERE school_id=%d AND class_school_id=%d AND is_active=1",
			$school_id, $class_school_id
		) );
		$count = 0;
		foreach ( $students as $st ) {
			$total = 0;
			foreach ( $fee_ids as $fid ) {
				$fee = $wpdb->get_row( $wpdb->prepare( "SELECT amount FROM " . SS_TABLE_FEES . " WHERE ID=%d", $fid ) );
				if ( $fee ) $total += (float) $fee->amount;
			}
			if ( $total <= 0 ) continue;
			$inv_num = SS_Helper::generate_invoice_number( $school_id );
			$wpdb->insert( SS_TABLE_INVOICES, array(
				'school_id' => $school_id, 'student_record_id' => $st->ID,
				'class_school_id' => $class_school_id, 'invoice_number' => $inv_num,
				'total_amount' => $total, 'paid_amount' => 0, 'due_amount' => $total,
				'status' => 'unpaid', 'due_date' => $due_date, 'created_at' => current_time( 'mysql' ),
			) );
			$count++;
		}
		wp_send_json( array( 'success' => true, 'message' => sprintf( __( '%d invoices generated.', 'school-softwere' ), $count ) ) );
	}
}

SS_Fee::init();
