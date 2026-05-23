<?php
/**
 * SS_Library — Library management AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Library {
	public static function init() {
		add_action( 'wp_ajax_ss_save_book',        array( __CLASS__, 'save_book' ) );
		add_action( 'wp_ajax_ss_delete_book',      array( __CLASS__, 'delete_book' ) );
		add_action( 'wp_ajax_ss_issue_book',       array( __CLASS__, 'issue_book' ) );
		add_action( 'wp_ajax_ss_return_book',      array( __CLASS__, 'return_book' ) );
		add_action( 'wp_ajax_ss_issue_library_card', array( __CLASS__, 'issue_library_card' ) );
	}

	private static function verify() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Security check failed.', 'school-softwere' ) ) );
	}

	public static function save_book() {
		self::verify();
		global $wpdb;
		$id   = SS_Helper::post( 'id', 'int' );
		$data = array(
			'school_id'   => SS_Helper::post( 'school_id', 'int' ),
			'title'       => SS_Helper::post( 'title' ),
			'author'      => SS_Helper::post( 'author' ),
			'isbn'        => SS_Helper::post( 'isbn' ),
			'publisher'   => SS_Helper::post( 'publisher' ),
			'edition'     => SS_Helper::post( 'edition' ),
			'quantity'    => SS_Helper::post( 'quantity', 'int' ),
			'available'   => SS_Helper::post( 'quantity', 'int' ),
			'created_at'  => current_time( 'mysql' ),
		);
		if ( $id ) { unset( $data['created_at'] ); $wpdb->update( SS_TABLE_BOOKS, $data, array( 'ID' => $id ) ); }
		else { $wpdb->insert( SS_TABLE_BOOKS, $data ); }
		wp_send_json( array( 'success' => true, 'message' => __( 'Book saved.', 'school-softwere' ) ) );
	}

	public static function delete_book() {
		self::verify();
		global $wpdb;
		$wpdb->delete( SS_TABLE_BOOKS, array( 'ID' => SS_Helper::post( 'id', 'int' ) ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Book deleted.', 'school-softwere' ) ) );
	}

	public static function issue_book() {
		self::verify();
		global $wpdb;
		$book_id    = SS_Helper::post( 'book_id', 'int' );
		$student_id = SS_Helper::post( 'student_record_id', 'int' );
		$book       = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . SS_TABLE_BOOKS . " WHERE ID=%d", $book_id ) );
		if ( ! $book || $book->available < 1 )
			wp_send_json( array( 'success' => false, 'message' => __( 'Book not available.', 'school-softwere' ) ) );

		$wpdb->insert( SS_TABLE_BOOKS_ISSUED, array(
			'book_id'           => $book_id,
			'student_record_id' => $student_id,
			'issue_date'        => current_time( 'Y-m-d' ),
			'return_date'       => SS_Helper::post( 'return_date' ) ?: null,
			'fine'              => 0,
			'created_at'        => current_time( 'mysql' ),
		) );
		$wpdb->query( $wpdb->prepare( "UPDATE " . SS_TABLE_BOOKS . " SET available = available - 1 WHERE ID=%d", $book_id ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Book issued successfully.', 'school-softwere' ) ) );
	}

	public static function return_book() {
		self::verify();
		global $wpdb;
		$issue_id  = SS_Helper::post( 'id', 'int' );
		$fine      = SS_Helper::post( 'fine', 'float' );
		$issue     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . SS_TABLE_BOOKS_ISSUED . " WHERE ID=%d", $issue_id ) );
		if ( ! $issue ) wp_send_json( array( 'success' => false, 'message' => __( 'Issue record not found.', 'school-softwere' ) ) );
		$wpdb->update( SS_TABLE_BOOKS_ISSUED, array( 'returned_at' => current_time( 'Y-m-d' ), 'fine' => $fine ), array( 'ID' => $issue_id ) );
		$wpdb->query( $wpdb->prepare( "UPDATE " . SS_TABLE_BOOKS . " SET available = available + 1 WHERE ID=%d", $issue->book_id ) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Book returned.', 'school-softwere' ) ) );
	}

	public static function issue_library_card() {
		self::verify();
		global $wpdb;
		$student_id = SS_Helper::post( 'student_record_id', 'int' );
		$exists     = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . SS_TABLE_LIBRARY_CARDS . " WHERE student_record_id=%d", $student_id ) );
		if ( $exists ) wp_send_json( array( 'success' => false, 'message' => __( 'Library card already issued.', 'school-softwere' ) ) );
		$card_number = 'LIB-' . strtoupper( wp_generate_password( 6, false ) );
		$wpdb->insert( SS_TABLE_LIBRARY_CARDS, array(
			'student_record_id' => $student_id,
			'card_number'       => $card_number,
			'issued_date'       => current_time( 'Y-m-d' ),
			'expiry_date'       => date( 'Y-m-d', strtotime( '+1 year' ) ),
		) );
		wp_send_json( array( 'success' => true, 'message' => __( 'Library card issued.', 'school-softwere' ), 'data' => array( 'card_number' => $card_number ) ) );
	}
}
SS_Library::init();
