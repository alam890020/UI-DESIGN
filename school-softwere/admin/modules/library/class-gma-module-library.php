<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Library {
	public static function init() {
		add_action( 'wp_ajax_gma_save_book',        array( __CLASS__, 'save_book' ) );
		add_action( 'wp_ajax_gma_delete_book',       array( __CLASS__, 'delete_book' ) );
		add_action( 'wp_ajax_gma_issue_book',        array( __CLASS__, 'issue_book' ) );
		add_action( 'wp_ajax_gma_return_book',       array( __CLASS__, 'return_book' ) );
		add_action( 'wp_ajax_gma_save_library_card', array( __CLASS__, 'save_card' ) );
		add_action( 'wp_ajax_gma_get_book',          array( __CLASS__, 'get_book' ) );
	}
	public static function save_book() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$qty = GMA_Helper::post( 'quantity', 'int' );
		$data = array(
			'school_id'  => GMA_Helper::get_current_school_id(),
			'title'      => GMA_Helper::post( 'title' ),
			'author'     => GMA_Helper::post( 'author' ),
			'isbn'       => GMA_Helper::post( 'isbn' ),
			'publisher'  => GMA_Helper::post( 'publisher' ),
			'edition'    => GMA_Helper::post( 'edition' ),
			'quantity'   => $qty,
			'available'  => $qty,
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_BOOKS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Book updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_BOOKS, $data ); wp_send_json_success( array( 'message' => __( 'Book added.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_book() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_BOOKS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Book deleted.', 'gma-school' ) ) );
	}
	public static function issue_book() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$book_id    = GMA_Helper::post( 'book_id', 'int' );
		$student_id = GMA_Helper::post( 'student_record_id', 'int' );
		$book       = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_BOOKS . " WHERE ID=%d", $book_id ) );
		if ( ! $book || $book->available < 1 ) wp_send_json_error( array( 'message' => __( 'Book not available.', 'gma-school' ) ) );
		$wpdb->insert( GMA_TABLE_BOOKS_ISSUED, array( 'book_id' => $book_id, 'student_record_id' => $student_id, 'issue_date' => GMA_Helper::post( 'issue_date' ) ?: current_time( 'Y-m-d' ), 'return_date' => GMA_Helper::post( 'return_date' ) ?: null, 'created_at' => current_time( 'mysql' ) ) );
		$wpdb->update( GMA_TABLE_BOOKS, array( 'available' => $book->available - 1 ), array( 'ID' => $book_id ) );
		wp_send_json_success( array( 'message' => __( 'Book issued.', 'gma-school' ), 'id' => $wpdb->insert_id ) );
	}
	public static function return_book() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id   = GMA_Helper::post( 'id', 'int' );
		$fine = GMA_Helper::post( 'fine', 'float' );
		$row  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_BOOKS_ISSUED . " WHERE ID=%d", $id ) );
		if ( ! $row ) wp_send_json_error( array( 'message' => __( 'Record not found.', 'gma-school' ) ) );
		$wpdb->update( GMA_TABLE_BOOKS_ISSUED, array( 'returned_at' => current_time( 'Y-m-d' ), 'fine' => $fine ), array( 'ID' => $id ) );
		$book = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_BOOKS . " WHERE ID=%d", $row->book_id ) );
		if ( $book ) $wpdb->update( GMA_TABLE_BOOKS, array( 'available' => $book->available + 1 ), array( 'ID' => $book->ID ) );
		wp_send_json_success( array( 'message' => __( 'Book returned.', 'gma-school' ) ) );
	}
	public static function save_card() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'student_record_id' => GMA_Helper::post( 'student_record_id', 'int' ),
			'card_number'       => GMA_Helper::post( 'card_number' ),
			'issued_date'       => GMA_Helper::post( 'issued_date' ) ?: null,
			'expiry_date'       => GMA_Helper::post( 'expiry_date' ) ?: null,
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_LIBRARY_CARDS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Card updated.', 'gma-school' ) ) ); }
		else { $wpdb->insert( GMA_TABLE_LIBRARY_CARDS, $data ); wp_send_json_success( array( 'message' => __( 'Library card issued.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function get_book() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_BOOKS . " WHERE ID=%d", GMA_Helper::post( 'id', 'int' ) ) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
}
GMA_Module_Library::init();
