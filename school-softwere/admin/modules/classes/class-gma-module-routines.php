<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Routines {
	public static function init() {
		add_action( 'wp_ajax_gma_save_routine',   array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_routine', array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_routines',   array( __CLASS__, 'get_all' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'class_school_id' => GMA_Helper::post( 'class_school_id', 'int' ),
			'section_id'      => GMA_Helper::post( 'section_id', 'int' ),
			'subject_id'      => GMA_Helper::post( 'subject_id', 'int' ),
			'staff_id'        => GMA_Helper::post( 'staff_id', 'int' ) ?: null,
			'day'             => GMA_Helper::post( 'day' ),
			'start_time'      => GMA_Helper::post( 'start_time' ) ?: null,
			'end_time'        => GMA_Helper::post( 'end_time' ) ?: null,
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_ROUTINES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Period updated.', 'gma-school' ) ) ); }
		else { $wpdb->insert( GMA_TABLE_ROUTINES, $data ); wp_send_json_success( array( 'message' => __( 'Period added.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_ROUTINES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Period deleted.', 'gma-school' ) ) );
	}
	public static function get_all() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$csid = GMA_Helper::post( 'class_school_id', 'int' );
		$sec  = GMA_Helper::post( 'section_id', 'int' );
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare(
			"SELECT r.*, sub.label AS subject_label, CONCAT(st.first_name,' ',st.last_name) AS staff_name FROM " . GMA_TABLE_ROUTINES . " r LEFT JOIN " . GMA_TABLE_SUBJECTS . " sub ON r.subject_id=sub.ID LEFT JOIN " . GMA_TABLE_STAFF . " st ON r.staff_id=st.ID WHERE r.class_school_id=%d AND r.section_id=%d ORDER BY FIELD(r.day,'monday','tuesday','wednesday','thursday','friday','saturday'), r.start_time",
			$csid, $sec
		) ) );
	}
}
GMA_Module_Routines::init();
