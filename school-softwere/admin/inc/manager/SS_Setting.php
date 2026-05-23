<?php
/**
 * SS_Setting — Settings AJAX handlers.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Setting {
	public static function init() {
		add_action( 'wp_ajax_ss_save_settings', array( __CLASS__, 'save_settings' ) );
	}

	public static function save_settings() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Permission denied.', 'school-softwere' ) ) );

		$school_id = SS_Helper::post( 'school_id', 'int' );
		$allowed   = array_keys( SS_Config::defaults() );
		foreach ( $allowed as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				SS_Config::set( $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ), $school_id );
			}
		}
		wp_send_json( array( 'success' => true, 'message' => __( 'Settings saved.', 'school-softwere' ) ) );
	}
}
SS_Setting::init();
