<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Classes {
	public static function init() {
		add_action( 'wp_ajax_gma_save_class',         array( __CLASS__, 'save_class' ) );
		add_action( 'wp_ajax_gma_delete_class',        array( __CLASS__, 'delete_class' ) );
		add_action( 'wp_ajax_gma_save_section',        array( __CLASS__, 'save_section' ) );
		add_action( 'wp_ajax_gma_delete_section',      array( __CLASS__, 'delete_section' ) );
		add_action( 'wp_ajax_gma_get_sections',        array( __CLASS__, 'get_sections' ) );
		add_action( 'wp_ajax_gma_get_classes',         array( __CLASS__, 'get_classes' ) );
		add_action( 'wp_ajax_gma_assign_class_school', array( __CLASS__, 'assign_class_school' ) );
	}
	public static function save_class() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array( 'label' => GMA_Helper::post( 'label' ) );
		if ( $id ) { $wpdb->update( GMA_TABLE_CLASSES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Class updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_CLASSES, $data ); wp_send_json_success( array( 'message' => __( 'Class created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_class() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$wpdb->delete( GMA_TABLE_CLASSES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Class deleted.', 'gma-school' ) ) );
	}
	public static function save_section() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'class_school_id' => GMA_Helper::post( 'class_school_id', 'int' ),
			'label'           => GMA_Helper::post( 'label' ),
			'capacity'        => GMA_Helper::post( 'capacity', 'int' ),
			'medium_id'       => GMA_Helper::post( 'medium_id', 'int' ) ?: null,
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_SECTIONS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Section updated.', 'gma-school' ) ) ); }
		else { $data['created_at'] = current_time( 'mysql' ); $wpdb->insert( GMA_TABLE_SECTIONS, $data ); wp_send_json_success( array( 'message' => __( 'Section created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_section() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_SECTIONS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Section deleted.', 'gma-school' ) ) );
	}
	public static function get_sections() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$csid = GMA_Helper::post( 'class_school_id', 'int' );
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_SECTIONS . " WHERE class_school_id=%d ORDER BY label", $csid ) ) );
	}
	public static function get_classes() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$sid = GMA_Helper::post( 'session_id', 'int' ) ?: GMA_Helper::get_active_session_id( GMA_Helper::get_current_school_id() );
		$school_id = GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id();
		wp_send_json_success( $wpdb->get_results( $wpdb->prepare(
			"SELECT cs.ID, c.label FROM " . GMA_TABLE_CLASS_SCHOOL . " cs INNER JOIN " . GMA_TABLE_CLASSES . " c ON cs.class_id=c.ID WHERE cs.school_id=%d AND cs.session_id=%d ORDER BY c.label",
			$school_id, $sid
		) ) );
	}
	public static function assign_class_school() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$data = array(
			'class_id'   => GMA_Helper::post( 'class_id', 'int' ),
			'school_id'  => GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id(),
			'session_id' => GMA_Helper::post( 'session_id', 'int' ) ?: GMA_Helper::get_active_session_id( GMA_Helper::get_current_school_id() ),
		);
		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . GMA_TABLE_CLASS_SCHOOL . " WHERE class_id=%d AND school_id=%d AND session_id=%d", $data['class_id'], $data['school_id'], $data['session_id'] ) );
		if ( ! $exists ) { $wpdb->insert( GMA_TABLE_CLASS_SCHOOL, $data ); wp_send_json_success( array( 'message' => __( 'Class assigned.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
		else { wp_send_json_error( array( 'message' => __( 'Already assigned.', 'gma-school' ) ) ); }
	}
}
GMA_Module_Classes::init();
