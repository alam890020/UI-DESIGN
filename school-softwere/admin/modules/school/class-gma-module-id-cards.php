<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Id_Cards {
	public static function init() {
		add_action( 'wp_ajax_gma_get_id_card_data', array( __CLASS__, 'get_data' ) );
		add_action( 'wp_ajax_gma_bulk_id_cards',    array( __CLASS__, 'bulk' ) );
	}
	public static function get_data() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$type = GMA_Helper::post( 'type' ); // 'student' or 'staff'
		if ( $type === 'staff' ) {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT s.*, sc.label AS school_name FROM " . GMA_TABLE_STAFF . " s LEFT JOIN " . GMA_TABLE_SCHOOLS . " sc ON s.school_id=sc.ID WHERE s.ID=%d", $id ) );
		} else {
			$row = $wpdb->get_row( $wpdb->prepare(
				"SELECT sr.*, CONCAT(c.label,' - ',sec.label) AS class_label, sc.label AS school_name FROM " . GMA_TABLE_STUDENT_RECORDS . " sr LEFT JOIN " . GMA_TABLE_CLASS_SCHOOL . " cs ON sr.class_school_id=cs.ID LEFT JOIN " . GMA_TABLE_CLASSES . " c ON cs.class_id=c.ID LEFT JOIN " . GMA_TABLE_SECTIONS . " sec ON sr.section_id=sec.ID LEFT JOIN " . GMA_TABLE_SCHOOLS . " sc ON sr.school_id=sc.ID WHERE sr.ID=%d", $id
			) );
		}
		$row ? wp_send_json_success( $row ) : wp_send_json_error();
	}
	public static function bulk() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$type = GMA_Helper::post( 'type' );
		$ids  = array_map( 'intval', (array) ( $_POST['ids'] ?? array() ) );
		$data = array();
		foreach ( $ids as $id ) {
			if ( $type === 'staff' ) {
				$row = $wpdb->get_row( $wpdb->prepare( "SELECT s.*, sc.label AS school_name FROM " . GMA_TABLE_STAFF . " s LEFT JOIN " . GMA_TABLE_SCHOOLS . " sc ON s.school_id=sc.ID WHERE s.ID=%d", $id ) );
			} else {
				$row = $wpdb->get_row( $wpdb->prepare( "SELECT sr.*, c.label AS class_label, sc.label AS school_name FROM " . GMA_TABLE_STUDENT_RECORDS . " sr LEFT JOIN " . GMA_TABLE_CLASS_SCHOOL . " cs ON sr.class_school_id=cs.ID LEFT JOIN " . GMA_TABLE_CLASSES . " c ON cs.class_id=c.ID LEFT JOIN " . GMA_TABLE_SCHOOLS . " sc ON sr.school_id=sc.ID WHERE sr.ID=%d", $id ) );
			}
			if ( $row ) $data[] = $row;
		}
		wp_send_json_success( $data );
	}
}
GMA_Module_Id_Cards::init();
