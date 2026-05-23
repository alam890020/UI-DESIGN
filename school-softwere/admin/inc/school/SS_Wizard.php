<?php
/**
 * SS_Wizard — Setup wizard AJAX handler.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Wizard {
	public static function init() {
		add_action( 'wp_ajax_ss_wizard_step', array( __CLASS__, 'handle_step' ) );
	}

	public static function handle_step() {
		if ( ! check_ajax_referer( 'ss_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) )
			wp_send_json( array( 'success' => false, 'message' => __( 'Permission denied.', 'school-softwere' ) ) );
		global $wpdb;
		$step = SS_Helper::post( 'step', 'int' );

		switch ( $step ) {
			case 1: // School details
				$school_id = (int) get_option( 'ss_default_school_id', 1 );
				$wpdb->update( SS_TABLE_SCHOOLS, array(
					'label'               => SS_Helper::post( 'school_name' ),
					'phone'               => SS_Helper::post( 'phone' ),
					'email'               => SS_Helper::post( 'email', 'email' ),
					'address'             => SS_Helper::post( 'address' ),
					'registration_number' => SS_Helper::post( 'registration_number' ),
				), array( 'ID' => $school_id ) );
				if ( ! empty( $_FILES['logo']['name'] ) ) {
					$u = SS_Helper::handle_upload( $_FILES['logo'], 'school-softwere/logos' );
					if ( ! is_wp_error( $u ) ) $wpdb->update( SS_TABLE_SCHOOLS, array( 'logo' => $u ), array( 'ID' => $school_id ) );
				}
				update_option( 'ss_wizard_step', 2 );
				wp_send_json( array( 'success' => true, 'message' => __( 'School details saved!', 'school-softwere' ) ) );
				break;

			case 2: // Classes & Sections
				$school_id  = (int) get_option( 'ss_default_school_id', 1 );
				$session_id = SS_Helper::get_active_session_id( $school_id );
				$classes    = array_filter( array_map( 'sanitize_text_field', (array) ( $_POST['classes'] ?? array() ) ) );
				foreach ( $classes as $label ) {
					$wpdb->insert( SS_TABLE_CLASSES, array( 'label' => $label, 'created_at' => current_time( 'mysql' ) ) );
					$cid = $wpdb->insert_id;
					$wpdb->insert( SS_TABLE_CLASS_SCHOOL, array( 'class_id' => $cid, 'school_id' => $school_id, 'session_id' => $session_id ) );
					$csid = $wpdb->insert_id;
					$wpdb->insert( SS_TABLE_SECTIONS, array( 'class_school_id' => $csid, 'label' => 'A', 'capacity' => 40, 'created_at' => current_time( 'mysql' ) ) );
				}
				update_option( 'ss_wizard_step', 3 );
				wp_send_json( array( 'success' => true, 'message' => __( 'Classes created!', 'school-softwere' ) ) );
				break;

			case 3: // Academic Session
				$school_id = (int) get_option( 'ss_default_school_id', 1 );
				$wpdb->insert( SS_TABLE_SESSIONS, array(
					'school_id'  => $school_id,
					'label'      => SS_Helper::post( 'label' ),
					'start_date' => SS_Helper::post( 'start_date' ) ?: null,
					'end_date'   => SS_Helper::post( 'end_date' ) ?: null,
					'is_active'  => 1,
				) );
				update_option( 'ss_wizard_step', 4 );
				wp_send_json( array( 'success' => true, 'message' => __( 'Session created!', 'school-softwere' ) ) );
				break;

			case 4: // Add Admin Staff
				$school_id = (int) get_option( 'ss_default_school_id', 1 );
				$user_data = array(
					'user_login' => SS_Helper::post( 'username' ),
					'user_email' => SS_Helper::post( 'email', 'email' ),
					'user_pass'  => SS_Helper::post( 'password' ),
					'display_name'=> SS_Helper::post( 'full_name' ),
					'role'       => 'administrator',
				);
				$uid = wp_insert_user( $user_data );
				if ( ! is_wp_error( $uid ) ) {
					$wpdb->insert( SS_TABLE_ADMINS, array( 'school_id' => $school_id, 'user_id' => $uid, 'created_at' => current_time( 'mysql' ) ) );
				}
				update_option( 'ss_wizard_step', 5 );
				wp_send_json( array( 'success' => true, 'message' => __( 'Admin added!', 'school-softwere' ) ) );
				break;

			case 5: // Fee Structure
				$school_id       = (int) get_option( 'ss_default_school_id', 1 );
				$class_school_id = SS_Helper::post( 'class_school_id', 'int' );
				$fees            = (array) ( $_POST['fees'] ?? array() );
				foreach ( $fees as $fee ) {
					if ( empty( $fee['label'] ) || empty( $fee['amount'] ) ) continue;
					$wpdb->insert( SS_TABLE_FEES, array(
						'class_school_id' => $class_school_id,
						'label'           => sanitize_text_field( $fee['label'] ),
						'amount'          => (float) $fee['amount'],
						'is_recurring'    => 1,
						'frequency'       => 'monthly',
						'created_at'      => current_time( 'mysql' ),
					) );
				}
				update_option( 'ss_wizard_step', 6 );
				wp_send_json( array( 'success' => true, 'message' => __( 'Fee structure configured!', 'school-softwere' ) ) );
				break;

			case 6: // Complete
				update_option( 'ss_setup_complete', true );
				update_option( 'ss_wizard_step', 6 );
				wp_send_json( array( 'success' => true, 'message' => __( 'Setup complete!', 'school-softwere' ), 'data' => array( 'redirect' => admin_url( 'admin.php?page=school-softwere' ) ) ) );
				break;

			default:
				wp_send_json( array( 'success' => false, 'message' => __( 'Invalid step.', 'school-softwere' ) ) );
		}
	}
}
SS_Wizard::init();
