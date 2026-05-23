<?php
/**
 * SS_M_Role — Role & permission helpers.
 *
 * @package School_Softwere
 */

defined( 'ABSPATH' ) || die();

class SS_M_Role {

	/** All known permission keys */
	const PERMISSIONS = array(
		'manage_students', 'view_students',
		'manage_staff',    'view_staff',
		'manage_classes',
		'manage_fees',     'collect_fees',  'view_fees',
		'manage_exams',    'enter_results', 'view_results',
		'manage_attendance','view_attendance',
		'manage_library',  'manage_transport', 'manage_hostel',
		'manage_notices',  'manage_events',    'manage_homework',
		'manage_settings', 'manage_logs',      'view_inquiries',
		'manage_leaves',   'approve_leaves',
		'view_live_classes','manage_meetings',
	);

	/** Built-in role permission sets */
	const ROLE_DEFAULTS = array(
		'super_admin'       => '*',  // all
		'school_admin'      => '*',
		'teacher'           => array( 'view_students','manage_attendance','view_attendance','manage_exams','enter_results','view_results','manage_homework','view_live_classes' ),
		'accountant'        => array( 'view_students','manage_fees','collect_fees','view_fees' ),
		'librarian'         => array( 'view_students','manage_library' ),
		'transport_manager' => array( 'manage_transport' ),
		'receptionist'      => array( 'view_students','view_inquiries','manage_notices' ),
		'hostel_warden'     => array( 'manage_hostel' ),
		'employee'          => array( 'manage_leaves' ),
	);

	/**
	 * Check if a WP user has a given SS permission.
	 *
	 * @param  string $permission
	 * @param  int    $user_id  Default: current user.
	 * @param  int    $school_id
	 * @return bool
	 */
	public static function can( $permission, $user_id = 0, $school_id = 0 ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		// WP admins = super admin
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}
		if ( ! $school_id ) {
			$school_id = SS_Helper::get_current_school_id();
		}
		// Check if school admin
		if ( self::is_school_admin( $user_id, $school_id ) ) {
			return true;
		}
		// Get staff role
		$perms = self::get_user_permissions( $user_id, $school_id );
		return in_array( $permission, $perms, true );
	}

	/**
	 * Check if user is a school admin.
	 *
	 * @param  int $user_id
	 * @param  int $school_id
	 * @return bool
	 */
	public static function is_school_admin( $user_id, $school_id ) {
		global $wpdb;
		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->prefix}ss_admins WHERE user_id = %d AND school_id = %d",
			$user_id,
			$school_id
		) );
		return (int) $count > 0;
	}

	/**
	 * Get the permission array for a user.
	 *
	 * @param  int $user_id
	 * @param  int $school_id
	 * @return array
	 */
	public static function get_user_permissions( $user_id, $school_id ) {
		global $wpdb;
		$staff = $wpdb->get_row( $wpdb->prepare(
			"SELECT role_id FROM {$wpdb->prefix}ss_staff WHERE user_id = %d AND school_id = %d LIMIT 1",
			$user_id,
			$school_id
		) );
		if ( ! $staff || ! $staff->role_id ) {
			return array();
		}
		$role = $wpdb->get_row( $wpdb->prepare(
			"SELECT permissions FROM {$wpdb->prefix}ss_roles WHERE ID = %d LIMIT 1",
			$staff->role_id
		) );
		if ( ! $role ) {
			return array();
		}
		$perms = json_decode( $role->permissions, true );
		return is_array( $perms ) ? $perms : array();
	}

	/**
	 * Get all roles for a school.
	 *
	 * @param  int $school_id
	 * @return array
	 */
	public static function get_roles( $school_id ) {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}ss_roles WHERE school_id = %d ORDER BY label ASC",
			$school_id
		) );
	}

	/**
	 * Save a role.
	 *
	 * @param  array $data  label, school_id, permissions (array).
	 * @param  int   $id    0 = insert, >0 = update.
	 * @return int   Inserted/updated ID.
	 */
	public static function save_role( $data, $id = 0 ) {
		global $wpdb;
		$row = array(
			'school_id'   => (int) $data['school_id'],
			'label'       => sanitize_text_field( $data['label'] ),
			'permissions' => wp_json_encode( array_map( 'sanitize_text_field', (array) $data['permissions'] ) ),
			'created_at'  => current_time( 'mysql' ),
		);
		if ( $id ) {
			$wpdb->update( $wpdb->prefix . 'ss_roles', $row, array( 'ID' => (int) $id ) );
			return (int) $id;
		}
		$wpdb->insert( $wpdb->prefix . 'ss_roles', $row );
		return (int) $wpdb->insert_id;
	}
}
