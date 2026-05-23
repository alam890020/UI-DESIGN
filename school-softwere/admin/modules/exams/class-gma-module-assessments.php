<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Assessments {
	public static function init() {
		add_action( 'wp_ajax_gma_save_assessment',       array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_assessment',     array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_save_assessment_grade', array( __CLASS__, 'save_grade' ) );
		add_action( 'wp_ajax_gma_delete_assessment_grade', array( __CLASS__, 'delete_grade' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array( 'school_id' => GMA_Helper::get_current_school_id(), 'label' => GMA_Helper::post( 'label' ) );
		if ( $id ) { $wpdb->update( GMA_TABLE_ASSESSMENTS, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Assessment updated.', 'gma-school' ) ) ); }
		else { $wpdb->insert( GMA_TABLE_ASSESSMENTS, $data ); wp_send_json_success( array( 'message' => __( 'Assessment created.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_ASSESSMENTS, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Deleted.', 'gma-school' ) ) );
	}
	public static function save_grade() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'assessment_id' => GMA_Helper::post( 'assessment_id', 'int' ),
			'label'         => GMA_Helper::post( 'label' ),
			'min_marks'     => GMA_Helper::post( 'min_marks', 'float' ),
			'max_marks'     => GMA_Helper::post( 'max_marks', 'float' ),
			'gpa'           => GMA_Helper::post( 'gpa', 'float' ),
			'remark'        => GMA_Helper::post( 'remark' ),
		);
		if ( $id ) { $wpdb->update( GMA_TABLE_ASSESSMENT_GRADES, $data, array( 'ID' => $id ) ); wp_send_json_success( array( 'message' => __( 'Grade updated.', 'gma-school' ) ) ); }
		else { $wpdb->insert( GMA_TABLE_ASSESSMENT_GRADES, $data ); wp_send_json_success( array( 'message' => __( 'Grade added.', 'gma-school' ), 'id' => $wpdb->insert_id ) ); }
	}
	public static function delete_grade() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$wpdb->delete( GMA_TABLE_ASSESSMENT_GRADES, array( 'ID' => GMA_Helper::post( 'id', 'int' ) ) );
		wp_send_json_success( array( 'message' => __( 'Grade deleted.', 'gma-school' ) ) );
	}
}
GMA_Module_Assessments::init();
