<?php defined( 'ABSPATH' ) || exit;
class GMA_Module_Students {
	public static function init() {
		add_action( 'wp_ajax_gma_save_student',     array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_gma_delete_student',   array( __CLASS__, 'delete' ) );
		add_action( 'wp_ajax_gma_get_student',      array( __CLASS__, 'get_one' ) );
		add_action( 'wp_ajax_gma_promote_students', array( __CLASS__, 'promote' ) );
		add_action( 'wp_ajax_gma_transfer_student', array( __CLASS__, 'transfer' ) );
		add_action( 'wp_ajax_gma_bulk_students',    array( __CLASS__, 'bulk' ) );
		add_action( 'wp_ajax_gma_switch_school',    array( __CLASS__, 'switch_school' ) );
		add_action( 'wp_ajax_gma_unassign_student', array( __CLASS__, 'unassign' ) );
	}
	public static function save() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$data = array(
			'school_id'         => GMA_Helper::post( 'school_id', 'int' ) ?: GMA_Helper::get_current_school_id(),
			'class_school_id'   => GMA_Helper::post( 'class_school_id', 'int' ),
			'section_id'        => GMA_Helper::post( 'section_id', 'int' ),
			'first_name'        => GMA_Helper::post( 'first_name' ),
			'last_name'         => GMA_Helper::post( 'last_name' ),
			'father_name'       => GMA_Helper::post( 'father_name' ),
			'mother_name'       => GMA_Helper::post( 'mother_name' ),
			'guardian_name'     => GMA_Helper::post( 'guardian_name' ),
			'guardian_relation' => GMA_Helper::post( 'guardian_relation' ),
			'dob'               => GMA_Helper::post( 'dob' ) ?: null,
			'gender'            => GMA_Helper::post( 'gender' ),
			'blood_group'       => GMA_Helper::post( 'blood_group' ),
			'religion'          => GMA_Helper::post( 'religion' ),
			'caste'             => GMA_Helper::post( 'caste' ),
			'nationality'       => GMA_Helper::post( 'nationality' ),
			'address'           => GMA_Helper::post( 'address' ),
			'city'              => GMA_Helper::post( 'city' ),
			'state'             => GMA_Helper::post( 'state' ),
			'zip'               => GMA_Helper::post( 'zip' ),
			'country'           => GMA_Helper::post( 'country' ),
			'phone'             => GMA_Helper::post( 'phone' ),
			'email'             => GMA_Helper::post( 'email', 'email' ),
			'admission_date'    => GMA_Helper::post( 'admission_date' ) ?: null,
			'student_type_id'   => GMA_Helper::post( 'student_type_id', 'int' ) ?: null,
			'roll_number'       => GMA_Helper::post( 'roll_number' ),
			'is_active'         => 1,
		);
		if ( ! empty( $_FILES['photo']['name'] ) ) {
			$u = GMA_Helper::handle_upload( $_FILES['photo'], 'gma-school/students' );
			if ( ! is_wp_error( $u ) ) $data['photo'] = $u;
		}
		if ( $id ) {
			$wpdb->update( GMA_TABLE_STUDENT_RECORDS, $data, array( 'ID' => $id ) );
			GMA_Database::log( 'student_updated', 'Student ID:' . $id, $data['school_id'] );
			wp_send_json_success( array( 'message' => __( 'Student updated.', 'gma-school' ), 'id' => $id ) );
		} else {
			$data['admission_number'] = GMA_Helper::generate_admission_number( $data['school_id'] );
			$data['created_at']       = current_time( 'mysql' );
			$wpdb->insert( GMA_TABLE_STUDENT_RECORDS, $data );
			$new_id = $wpdb->insert_id;
			GMA_Database::log( 'student_added', $data['first_name'] . ' ' . $data['last_name'], $data['school_id'] );
			wp_send_json_success( array( 'message' => __( 'Student enrolled.', 'gma-school' ), 'id' => $new_id, 'admission_number' => $data['admission_number'] ) );
		}
	}
	public static function delete() {
		GMA_Helper::verify_nonce( 'manage_options' ); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$wpdb->delete( GMA_TABLE_STUDENT_RECORDS, array( 'ID' => $id ) );
		GMA_Database::log( 'student_deleted', 'ID:' . $id );
		wp_send_json_success( array( 'message' => __( 'Student deleted.', 'gma-school' ) ) );
	}
	public static function get_one() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_STUDENT_RECORDS . " WHERE ID=%d", GMA_Helper::post( 'id', 'int' ) ) );
		$row ? wp_send_json_success( $row ) : wp_send_json_error( array( 'message' => __( 'Not found.', 'gma-school' ) ) );
	}
	public static function promote() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$ids    = array_map( 'intval', (array) ( $_POST['ids'] ?? array() ) );
		$to_cs  = GMA_Helper::post( 'to_class_school_id', 'int' );
		$to_sec = GMA_Helper::post( 'to_section_id', 'int' );
		foreach ( $ids as $sid ) {
			$s = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_STUDENT_RECORDS . " WHERE ID=%d", $sid ) );
			if ( ! $s ) continue;
			$wpdb->insert( GMA_TABLE_PROMOTIONS, array( 'student_record_id' => $sid, 'from_class_school_id' => $s->class_school_id, 'to_class_school_id' => $to_cs, 'promoted_at' => current_time( 'mysql' ) ) );
			$wpdb->update( GMA_TABLE_STUDENT_RECORDS, array( 'class_school_id' => $to_cs, 'section_id' => $to_sec ?: $s->section_id ), array( 'ID' => $sid ) );
		}
		wp_send_json_success( array( 'message' => sprintf( _n( '%d student promoted.', '%d students promoted.', count( $ids ), 'gma-school' ), count( $ids ) ) ) );
	}
	public static function transfer() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$wpdb->insert( GMA_TABLE_TRANSFERS, array( 'student_record_id' => $id, 'reason' => GMA_Helper::post( 'reason' ), 'created_at' => current_time( 'mysql' ) ) );
		$wpdb->update( GMA_TABLE_STUDENT_RECORDS, array( 'is_active' => 0 ), array( 'ID' => $id ) );
		wp_send_json_success( array( 'message' => __( 'Student transferred.', 'gma-school' ) ) );
	}
	public static function bulk() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$action = GMA_Helper::post( 'bulk_action' );
		$ids    = array_map( 'intval', (array) ( $_POST['ids'] ?? array() ) );
		if ( empty( $ids ) ) wp_send_json_error( array( 'message' => __( 'No items selected.', 'gma-school' ) ) );
		$ph = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
		switch ( $action ) {
			case 'delete':
				$wpdb->query( $wpdb->prepare( "DELETE FROM " . GMA_TABLE_STUDENT_RECORDS . " WHERE ID IN ($ph)", $ids ) ); // phpcs:ignore
				wp_send_json_success( array( 'message' => __( 'Deleted.', 'gma-school' ) ) );
				break;
			case 'deactivate':
				$wpdb->query( $wpdb->prepare( "UPDATE " . GMA_TABLE_STUDENT_RECORDS . " SET is_active=0 WHERE ID IN ($ph)", $ids ) ); // phpcs:ignore
				wp_send_json_success( array( 'message' => __( 'Deactivated.', 'gma-school' ) ) );
				break;
			default:
				wp_send_json_error( array( 'message' => __( 'Unknown action.', 'gma-school' ) ) );
		}
	}
	public static function switch_school() {
		check_ajax_referer( 'gma_nonce', 'nonce' );
		if ( ! session_id() ) @session_start();
		$_SESSION['gma_school_id'] = GMA_Helper::post( 'school_id', 'int' );
		wp_send_json_success();
	}
	public static function unassign() {
		GMA_Helper::verify_nonce(); global $wpdb;
		$id = GMA_Helper::post( 'id', 'int' );
		$wpdb->update( GMA_TABLE_STUDENT_RECORDS, array( 'class_school_id' => 0, 'section_id' => 0 ), array( 'ID' => $id ) );
		wp_send_json_success( array( 'message' => __( 'Student unassigned from class.', 'gma-school' ) ) );
	}
}
GMA_Module_Students::init();
