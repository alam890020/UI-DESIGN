<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Expenses {
	public static function init() {
		add_action( 'wp_ajax_gma_save_expense',           array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_expense',          array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_save_expense_category',   array( __CLASS__, 'save_cat' ) );
		add_action( 'wp_ajax_gma_delete_expense_category', array( __CLASS__, 'delete_cat' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'school_id'           => GMA_Helper::get_current_school_id(),
			'expense_category_id' => GMA_Helper::post( 'expense_category_id', 'int' ) ?: null,
			'label'               => GMA_Helper::post( 'label' ),
			'amount'              => GMA_Helper::post( 'amount', 'float' ),
			'date'                => GMA_Helper::post( 'date' ) ?: null,
			'note'                => GMA_Helper::post( 'note' ),
		);
		if ( ! empty( $_FILES['attachment']['name'] ) ) { $u = GMA_Helper::handle_upload( $_FILES['attachment'], 'gma-school/expenses' ); if ( ! is_wp_error( $u ) ) $data['attachment'] = $u; }
		if ( $id ) { $wpdb->update( GMA_TABLE_EXPENSES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Expense updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_EXPENSES, $data ); wp_send_json_success( array( 'message' => __( 'Expense recorded.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_EXPENSES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Deleted.', 'gma-school' ) ) );
	}
	public static function save_cat() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array( 'school_id' => GMA_Helper::get_current_school_id(), 'label' => GMA_Helper::post( 'label' ) );
		if ( $id ) { $wpdb->update( GMA_TABLE_EXPENSE_CATEGORIES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Category updated.', 'gma-school' ) ) ); }
		else { $wpdb->insert( GMA_TABLE_EXPENSE_CATEGORIES, $data ); wp_send_json_success( array( 'message' => __( 'Category created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_cat() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_EXPENSE_CATEGORIES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Category deleted.', 'gma-school' ) ) );
	}
}
GMA_Module_Expenses::init();
