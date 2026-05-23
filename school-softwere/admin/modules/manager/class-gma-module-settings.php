<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Settings {
	public static function init() {
		add_action( 'wp_ajax_gma_save_settings', array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_get_settings',  array( __CLASS__, 'get_all' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce( 'manage_options' );
		$school_id = GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id();
		$settings = array(
			'school_name', 'currency', 'currency_symbol', 'date_format',
			'time_format', 'timezone', 'academic_year', 'address',
			'phone', 'email', 'website', 'logo', 'footer_text',
			'sms_gateway', 'sms_api_key', 'email_notifications',
			'late_fine_per_day', 'library_fine_per_day',
		);
		foreach ( $settings as $key ) {
			$val = isset( $_POST[ $key ] ) ? sanitize_text_field( $_POST[ $key ] ) : '';
			GMA_Helper::save_setting( $school_id, $key, $val );
		}
		GMA_Database::log( 'settings_updated', 'School settings saved.', $school_id );
		wp_send_json_success( array( 'message' => __( 'Settings saved.', 'gma-school' ) ) );
	}
	public static function get_all() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$school_id = GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id();
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT setting_key, setting_value FROM " . GMA_TABLE_SETTINGS . " WHERE school_id=%d", $school_id ) );
		$result = array();
		foreach ( $rows as $r ) $result[ $r->setting_key ] = $r->setting_value;
		wp_send_json_success( $result );
	}
}
GMA_Module_Settings::init();
