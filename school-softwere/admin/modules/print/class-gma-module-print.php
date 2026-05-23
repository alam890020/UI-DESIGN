<?php
/**
 * GMA Print Module — handles all print/export AJAX requests and the print view.
 *
 * @package GMA_School
 */
defined( 'ABSPATH' ) || exit;

class GMA_Module_Print {
	public static function init() {
		add_action( 'wp_ajax_gma_get_print_data', array( __CLASS__, 'get_data' ) );
	}

	/**
	 * Returns data for any printable document type.
	 * type: invoice | admit_card | student_id | staff_id | certificate | tc | result | attendance | library_card
	 */
	public static function get_data() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$type = GMA_Helper::post( 'type' );
		$id   = GMA_Helper::post( 'id', 'int' );
		$ids  = array_map( 'intval', (array) ( $_POST['ids'] ?? ( $id ? array( $id ) : array() ) ) );
		$result = array();
		foreach ( $ids as $rid ) {
			switch ( $type ) {
				case 'invoice':
					$row = $wpdb->get_row( $wpdb->prepare(
						"SELECT i.*, CONCAT(sr.first_name,' ',sr.last_name) AS student_name, sr.admission_number, sr.phone, c.label AS class_label, sc.label AS school_name, sc.address AS school_address, sc.phone AS school_phone, sc.logo AS school_logo FROM " . GMA_TABLE_INVOICES . " i INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON i.student_record_id=sr.ID LEFT JOIN " . GMA_TABLE_CLASS_SCHOOL . " cs ON sr.class_school_id=cs.ID LEFT JOIN " . GMA_TABLE_CLASSES . " c ON cs.class_id=c.ID LEFT JOIN " . GMA_TABLE_SCHOOLS . " sc ON i.school_id=sc.ID WHERE i.ID=%d", $rid
					) );
					if ( $row ) { $row->payments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_PAYMENTS . " WHERE invoice_id=%d", $rid ) ); $result[] = $row; }
					break;
				case 'admit_card':
					$row = $wpdb->get_row( $wpdb->prepare(
						"SELECT ac.*, CONCAT(sr.first_name,' ',sr.last_name) AS student_name, sr.admission_number, sr.roll_number, sr.photo, sr.dob, sr.father_name, e.label AS exam_name, sc.label AS school_name, sc.logo FROM " . GMA_TABLE_ADMIT_CARDS . " ac INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON ac.student_record_id=sr.ID INNER JOIN " . GMA_TABLE_EXAMS . " e ON ac.exam_id=e.ID INNER JOIN " . GMA_TABLE_SCHOOLS . " sc ON sr.school_id=sc.ID WHERE ac.ID=%d", $rid
					) );
					if ( $row ) {
						$row->papers = $wpdb->get_results( $wpdb->prepare( "SELECT ep.date, ep.start_time, ep.end_time, ep.total_marks, ep.venue, sub.label AS subject FROM " . GMA_TABLE_EXAM_PAPERS . " ep INNER JOIN " . GMA_TABLE_SUBJECTS . " sub ON ep.subject_id=sub.ID WHERE ep.exam_id=%d ORDER BY ep.date", $row->exam_id ) );
						$result[] = $row;
					}
					break;
				case 'student_id':
					$row = $wpdb->get_row( $wpdb->prepare( "SELECT sr.*, c.label AS class_label, sec.label AS section_label, sc.label AS school_name, sc.logo FROM " . GMA_TABLE_STUDENT_RECORDS . " sr LEFT JOIN " . GMA_TABLE_CLASS_SCHOOL . " cs ON sr.class_school_id=cs.ID LEFT JOIN " . GMA_TABLE_CLASSES . " c ON cs.class_id=c.ID LEFT JOIN " . GMA_TABLE_SECTIONS . " sec ON sr.section_id=sec.ID LEFT JOIN " . GMA_TABLE_SCHOOLS . " sc ON sr.school_id=sc.ID WHERE sr.ID=%d", $rid ) );
					if ( $row ) $result[] = $row;
					break;
				case 'staff_id':
					$row = $wpdb->get_row( $wpdb->prepare( "SELECT s.*, sc.label AS school_name, sc.logo FROM " . GMA_TABLE_STAFF . " s LEFT JOIN " . GMA_TABLE_SCHOOLS . " sc ON s.school_id=sc.ID WHERE s.ID=%d", $rid ) );
					if ( $row ) $result[] = $row;
					break;
				case 'certificate':
					$row = $wpdb->get_row( $wpdb->prepare(
						"SELECT cs_map.*, cert.label AS cert_type, cert.content_template, CONCAT(sr.first_name,' ',sr.last_name) AS student_name, sr.admission_number, sr.father_name, c.label AS class_label, sc.label AS school_name FROM " . GMA_TABLE_CERTIFICATE_STUDENT . " cs_map INNER JOIN " . GMA_TABLE_CERTIFICATES . " cert ON cs_map.certificate_id=cert.ID INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON cs_map.student_record_id=sr.ID LEFT JOIN " . GMA_TABLE_CLASS_SCHOOL . " cl ON sr.class_school_id=cl.ID LEFT JOIN " . GMA_TABLE_CLASSES . " c ON cl.class_id=c.ID LEFT JOIN " . GMA_TABLE_SCHOOLS . " sc ON sr.school_id=sc.ID WHERE cs_map.ID=%d", $rid
					) );
					if ( $row ) $result[] = $row;
					break;
				case 'tc':
					$row = $wpdb->get_row( $wpdb->prepare(
						"SELECT tc.*, CONCAT(sr.first_name,' ',sr.last_name) AS student_name, sr.admission_number, sr.dob, sr.father_name, c.label AS class_label, sc.label AS school_name FROM " . GMA_TABLE_TRANSFER_CERTS . " tc INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON tc.student_record_id=sr.ID LEFT JOIN " . GMA_TABLE_CLASS_SCHOOL . " cl ON sr.class_school_id=cl.ID LEFT JOIN " . GMA_TABLE_CLASSES . " c ON cl.class_id=c.ID LEFT JOIN " . GMA_TABLE_SCHOOLS . " sc ON sr.school_id=sc.ID WHERE tc.ID=%d", $rid
					) );
					if ( $row ) $result[] = $row;
					break;
				case 'library_card':
					$row = $wpdb->get_row( $wpdb->prepare( "SELECT lc.*, CONCAT(sr.first_name,' ',sr.last_name) AS student_name, sr.photo, sc.label AS school_name, sc.logo FROM " . GMA_TABLE_LIBRARY_CARDS . " lc INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON lc.student_record_id=sr.ID LEFT JOIN " . GMA_TABLE_SCHOOLS . " sc ON sr.school_id=sc.ID WHERE lc.ID=%d", $rid ) );
					if ( $row ) $result[] = $row;
					break;
				default:
					break;
			}
		}
		wp_send_json_success( count( $result ) === 1 ? $result[0] : $result );
	}
}
GMA_Module_Print::init();
