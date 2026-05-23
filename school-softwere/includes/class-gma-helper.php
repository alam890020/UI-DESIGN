<?php
/**
 * GMA_Helper — Utility functions used across the plugin.
 *
 * @package GMA_School
 */
defined( 'ABSPATH' ) || exit;

class GMA_Helper {

	/** Sanitise POST field */
	public static function post( $key, $type = 'text', $default = '' ) {
		if ( ! isset( $_POST[ $key ] ) ) return $default;
		$val = $_POST[ $key ];
		switch ( $type ) {
			case 'int':   return (int) $val;
			case 'float': return (float) $val;
			case 'email': return sanitize_email( $val );
			case 'url':   return esc_url_raw( $val );
			case 'html':  return wp_kses_post( $val );
			case 'array': return is_array( $val ) ? array_map( 'sanitize_text_field', $val ) : array();
			default:      return sanitize_text_field( $val );
		}
	}

	/** Get current school ID (from session or first available) */
	public static function get_current_school_id() {
		if ( ! session_id() ) @session_start();
		if ( ! empty( $_SESSION['gma_school_id'] ) ) return (int) $_SESSION['gma_school_id'];
		global $wpdb;
		$id = (int) $wpdb->get_var( "SELECT ID FROM " . GMA_TABLE_SCHOOLS . " WHERE is_active=1 ORDER BY ID ASC LIMIT 1" );
		$_SESSION['gma_school_id'] = $id;
		return $id;
	}

	/** Get active session ID for school */
	public static function get_active_session_id( $school_id ) {
		global $wpdb;
		return (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT ID FROM " . GMA_TABLE_SESSIONS . " WHERE school_id=%d AND is_active=1 ORDER BY ID DESC LIMIT 1",
			$school_id
		) );
	}

	/** Generate admission number */
	public static function generate_admission_number( $school_id ) {
		global $wpdb;
		$school = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_SCHOOLS . " WHERE ID=%d", $school_id ) );
		if ( ! $school ) return 'ADM-001';
		$count  = (int) $school->last_enrollment_count + 1;
		$number = $school->admission_prefix . str_pad( $school->admission_base + $count, (int) $school->admission_padding, '0', STR_PAD_LEFT );
		$wpdb->update( GMA_TABLE_SCHOOLS, array( 'last_enrollment_count' => $count ), array( 'ID' => $school_id ) );
		return $number;
	}

	/** Generate invoice number */
	public static function generate_invoice_number( $school_id ) {
		global $wpdb;
		$school = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_SCHOOLS . " WHERE ID=%d", $school_id ) );
		$count  = ( $school ? (int) $school->last_invoice_count : 0 ) + 1;
		$number = 'INV-' . date( 'Y' ) . '-' . str_pad( $count, 5, '0', STR_PAD_LEFT );
		$wpdb->update( GMA_TABLE_SCHOOLS, array( 'last_invoice_count' => $count ), array( 'ID' => $school_id ) );
		return $number;
	}

	/** Format currency */
	public static function format_currency( $amount, $symbol = '₹' ) {
		return $symbol . number_format( (float) $amount, 2 );
	}

	/** Format date */
	public static function format_date( $date, $format = 'd M Y' ) {
		if ( ! $date || $date === '0000-00-00' ) return '—';
		return date_i18n( $format, strtotime( $date ) );
	}

	/** Status badge HTML */
	public static function status_badge( $status ) {
		$map = array(
			'active'   => array( 'label' => __( 'Active', 'gma-school' ),   'class' => 'success' ),
			'inactive' => array( 'label' => __( 'Inactive', 'gma-school' ), 'class' => 'danger' ),
			'paid'     => array( 'label' => __( 'Paid', 'gma-school' ),     'class' => 'success' ),
			'unpaid'   => array( 'label' => __( 'Unpaid', 'gma-school' ),   'class' => 'danger' ),
			'partial'  => array( 'label' => __( 'Partial', 'gma-school' ),  'class' => 'warning' ),
			'pending'  => array( 'label' => __( 'Pending', 'gma-school' ),  'class' => 'warning' ),
			'approved' => array( 'label' => __( 'Approved', 'gma-school' ), 'class' => 'success' ),
			'rejected' => array( 'label' => __( 'Rejected', 'gma-school' ), 'class' => 'danger' ),
			'open'     => array( 'label' => __( 'Open', 'gma-school' ),     'class' => 'primary' ),
			'closed'   => array( 'label' => __( 'Closed', 'gma-school' ),   'class' => 'secondary' ),
			'new'      => array( 'label' => __( 'New', 'gma-school' ),      'class' => 'info' ),
			'present'  => array( 'label' => __( 'Present', 'gma-school' ),  'class' => 'success' ),
			'absent'   => array( 'label' => __( 'Absent', 'gma-school' ),   'class' => 'danger' ),
			'late'     => array( 'label' => __( 'Late', 'gma-school' ),     'class' => 'warning' ),
		);
		$cfg = $map[ $status ] ?? array( 'label' => ucfirst( $status ), 'class' => 'secondary' );
		return '<span class="gma-badge gma-badge-' . esc_attr( $cfg['class'] ) . '">' . esc_html( $cfg['label'] ) . '</span>';
	}

	/** Handle file upload */
	public static function handle_upload( $file, $subdir = 'gma-school' ) {
		if ( ! function_exists( 'wp_handle_upload' ) ) require_once ABSPATH . 'wp-admin/includes/file.php';
		add_filter( 'upload_dir', function( $dirs ) use ( $subdir ) {
			$dirs['subdir'] = '/' . $subdir;
			$dirs['path']   = $dirs['basedir'] . '/' . $subdir;
			$dirs['url']    = $dirs['baseurl'] . '/' . $subdir;
			return $dirs;
		} );
		$result = wp_handle_upload( $file, array( 'test_form' => false ) );
		remove_all_filters( 'upload_dir' );
		if ( isset( $result['error'] ) ) return new WP_Error( 'upload_error', $result['error'] );
		return $result['url'];
	}

	/** Verify AJAX nonce */
	public static function verify_nonce( $cap = 'read' ) {
		if ( ! check_ajax_referer( 'gma_nonce', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'gma-school' ) ) );
		}
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'gma-school' ) ) );
		}
	}

	/** Get setting value */
	public static function get_setting( $school_id, $key, $default = '' ) {
		global $wpdb;
		$val = $wpdb->get_var( $wpdb->prepare(
			"SELECT setting_value FROM " . GMA_TABLE_SETTINGS . " WHERE school_id=%d AND setting_key=%s",
			$school_id, $key
		) );
		return $val !== null ? $val : $default;
	}

	/** Save setting value */
	public static function save_setting( $school_id, $key, $value ) {
		global $wpdb;
		$exists = $wpdb->get_var( $wpdb->prepare(
			"SELECT ID FROM " . GMA_TABLE_SETTINGS . " WHERE school_id=%d AND setting_key=%s",
			$school_id, $key
		) );
		if ( $exists ) {
			$wpdb->update( GMA_TABLE_SETTINGS, array( 'setting_value' => $value ), array( 'school_id' => $school_id, 'setting_key' => $key ) );
		} else {
			$wpdb->insert( GMA_TABLE_SETTINGS, array( 'school_id' => $school_id, 'setting_key' => $key, 'setting_value' => $value ) );
		}
	}

	/** Paginate results */
	public static function paginate( $total, $per_page, $current, $base_url ) {
		if ( $total <= $per_page ) return '';
		$pages = (int) ceil( $total / $per_page );
		$html  = '<div class="gma-pagination">';
		$html .= '<span class="gma-pagination-info">' . sprintf( __( 'Page %1$d of %2$d (%3$d total)', 'gma-school' ), $current, $pages, $total ) . '</span>';
		$html .= '<div class="gma-page-links">';
		if ( $current > 1 ) $html .= '<a href="' . esc_url( add_query_arg( 'paged', $current - 1, $base_url ) ) . '" class="gma-page-link">‹</a>';
		$start = max( 1, $current - 2 );
		$end   = min( $pages, $current + 2 );
		for ( $p = $start; $p <= $end; $p++ ) {
			$html .= '<a href="' . esc_url( add_query_arg( 'paged', $p, $base_url ) ) . '" class="gma-page-link' . ( $p === $current ? ' active' : '' ) . '">' . $p . '</a>';
		}
		if ( $current < $pages ) $html .= '<a href="' . esc_url( add_query_arg( 'paged', $current + 1, $base_url ) ) . '" class="gma-page-link">›</a>';
		$html .= '</div></div>';
		return $html;
	}

	/** Get school info */
	public static function get_school( $id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_SCHOOLS . " WHERE ID=%d", $id ) );
	}

	/** Blood group options */
	public static function blood_groups() {
		return array( 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-' );
	}

	/** Days of week */
	public static function days_of_week() {
		return array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );
	}
}
