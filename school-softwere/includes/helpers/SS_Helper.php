<?php
/**
 * SS_Helper — Utility / helper methods.
 *
 * @package School_Softwere
 */

defined( 'ABSPATH' ) || die();

class SS_Helper {

	/**
	 * Format a date according to the plugin date-format setting.
	 *
	 * @param  string $date  MySQL date string.
	 * @param  string $format Override format.
	 * @return string
	 */
	public static function format_date( $date, $format = '' ) {
		if ( empty( $date ) || '0000-00-00' === $date ) {
			return '—';
		}
		if ( ! $format ) {
			$format = get_option( 'ss_date_format', 'd/m/Y' );
		}
		return date_i18n( $format, strtotime( $date ) );
	}

	/**
	 * Format a currency amount.
	 *
	 * @param  float  $amount
	 * @param  string $symbol Override symbol.
	 * @return string
	 */
	public static function format_currency( $amount, $symbol = '' ) {
		if ( ! $symbol ) {
			$symbol = get_option( 'ss_currency_symbol', '$' );
		}
		return $symbol . number_format( (float) $amount, 2 );
	}

	/**
	 * Generate a unique admission number.
	 *
	 * @param  int $school_id
	 * @return string
	 */
	public static function generate_admission_number( $school_id ) {
		global $wpdb;
		$school = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}ss_schools WHERE ID = %d",
			$school_id
		) );
		if ( ! $school ) {
			return 'ADM-' . time();
		}
		$next = (int) $school->last_enrollment_count + 1;
		$wpdb->update(
			$wpdb->prefix . 'ss_schools',
			array( 'last_enrollment_count' => $next ),
			array( 'ID' => $school_id )
		);
		$prefix  = sanitize_text_field( $school->admission_prefix );
		$padded  = str_pad( (string) ( (int) $school->admission_base + $next ), (int) $school->admission_padding, '0', STR_PAD_LEFT );
		return $prefix . $padded;
	}

	/**
	 * Generate a unique invoice number.
	 *
	 * @param  int $school_id
	 * @return string
	 */
	public static function generate_invoice_number( $school_id ) {
		global $wpdb;
		$school = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}ss_schools WHERE ID = %d",
			$school_id
		) );
		$next = $school ? (int) $school->last_invoice_count + 1 : 1;
		$wpdb->update(
			$wpdb->prefix . 'ss_schools',
			array( 'last_invoice_count' => $next ),
			array( 'ID' => $school_id )
		);
		return 'INV-' . str_pad( (string) $next, 6, '0', STR_PAD_LEFT );
	}

	/**
	 * Get the active school ID for the current user.
	 *
	 * @return int
	 */
	public static function get_current_school_id() {
		if ( current_user_can( 'manage_options' ) ) {
			$sid = isset( $_SESSION['ss_school_id'] ) ? (int) $_SESSION['ss_school_id'] : 0;
			if ( ! $sid ) {
				$sid = (int) get_option( 'ss_default_school_id', 1 );
			}
			return $sid;
		}
		global $wpdb;
		$user_id = get_current_user_id();
		$row = $wpdb->get_row( $wpdb->prepare(
			"SELECT school_id FROM {$wpdb->prefix}ss_admins WHERE user_id = %d LIMIT 1",
			$user_id
		) );
		if ( $row ) {
			return (int) $row->school_id;
		}
		$row = $wpdb->get_row( $wpdb->prepare(
			"SELECT school_id FROM {$wpdb->prefix}ss_staff WHERE user_id = %d LIMIT 1",
			$user_id
		) );
		return $row ? (int) $row->school_id : 0;
	}

	/**
	 * Get the active session ID for a school.
	 *
	 * @param  int $school_id
	 * @return int
	 */
	public static function get_active_session_id( $school_id ) {
		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->prefix}ss_sessions WHERE school_id = %d AND is_active = 1 LIMIT 1",
			$school_id
		) );
		return $row ? (int) $row->ID : 0;
	}

	/**
	 * Calculate a letter grade from obtained and total marks.
	 *
	 * @param  float $obtained
	 * @param  float $total
	 * @return string
	 */
	public static function calculate_grade( $obtained, $total ) {
		if ( $total <= 0 ) {
			return 'N/A';
		}
		$pct = ( $obtained / $total ) * 100;
		if ( $pct >= 90 ) return 'A+';
		if ( $pct >= 80 ) return 'A';
		if ( $pct >= 70 ) return 'B+';
		if ( $pct >= 60 ) return 'B';
		if ( $pct >= 50 ) return 'C';
		if ( $pct >= 40 ) return 'D';
		return 'F';
	}

	/**
	 * Upload a file and return the URL.
	 *
	 * @param  array  $file       $_FILES element.
	 * @param  string $sub_dir    Sub-directory under uploads.
	 * @return string|WP_Error
	 */
	public static function handle_upload( $file, $sub_dir = 'school-softwere' ) {
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		add_filter( 'upload_dir', function ( $dirs ) use ( $sub_dir ) {
			$dirs['subdir'] = '/' . $sub_dir;
			$dirs['path']   = $dirs['basedir'] . '/' . $sub_dir;
			$dirs['url']    = $dirs['baseurl'] . '/' . $sub_dir;
			return $dirs;
		} );
		$uploaded = wp_handle_upload( $file, array( 'test_form' => false ) );
		remove_all_filters( 'upload_dir' );
		if ( isset( $uploaded['error'] ) ) {
			return new WP_Error( 'upload_failed', $uploaded['error'] );
		}
		return $uploaded['url'] ?? '';
	}

	/**
	 * Render a status badge HTML.
	 *
	 * @param  string $status
	 * @return string
	 */
	public static function status_badge( $status ) {
		$map = array(
			'active'    => array( 'Active',    'success' ),
			'inactive'  => array( 'Inactive',  'danger'  ),
			'paid'      => array( 'Paid',       'success' ),
			'partial'   => array( 'Partial',    'warning' ),
			'unpaid'    => array( 'Unpaid',     'danger'  ),
			'present'   => array( 'Present',    'success' ),
			'absent'    => array( 'Absent',     'danger'  ),
			'late'      => array( 'Late',       'warning' ),
			'half_day'  => array( 'Half Day',   'info'    ),
			'pending'   => array( 'Pending',    'warning' ),
			'approved'  => array( 'Approved',   'success' ),
			'rejected'  => array( 'Rejected',   'danger'  ),
			'open'      => array( 'Open',       'info'    ),
			'closed'    => array( 'Closed',     'secondary'),
		);
		$key   = strtolower( trim( $status ) );
		$label = isset( $map[ $key ] ) ? $map[ $key ][0] : ucfirst( $status );
		$color = isset( $map[ $key ] ) ? $map[ $key ][1] : 'secondary';
		return '<span class="ss-badge ss-badge-' . esc_attr( $color ) . '">' . esc_html( $label ) . '</span>';
	}

	/**
	 * Paginate a query — returns array with 'items', 'total', 'pages'.
	 *
	 * @param  string $query      SQL without LIMIT.
	 * @param  int    $per_page
	 * @param  int    $current_page
	 * @return array
	 */
	public static function paginate_query( $query, $per_page = 20, $current_page = 1 ) {
		global $wpdb;
		$per_page     = max( 1, (int) $per_page );
		$current_page = max( 1, (int) $current_page );
		$offset       = ( $current_page - 1 ) * $per_page;

		// Count
		$count_query = preg_replace( '/SELECT .+ FROM/iU', 'SELECT COUNT(*) FROM', $query );
		$total       = (int) $wpdb->get_var( $count_query ); // phpcs:ignore

		// Fetch page
		$items = $wpdb->get_results( $query . " LIMIT {$per_page} OFFSET {$offset}" ); // phpcs:ignore

		return array(
			'items'        => $items,
			'total'        => $total,
			'pages'        => (int) ceil( $total / $per_page ),
			'current_page' => $current_page,
			'per_page'     => $per_page,
		);
	}

	/**
	 * Sanitize and return POST value.
	 *
	 * @param  string $key
	 * @param  string $type  text|email|int|float|html
	 * @param  mixed  $default
	 * @return mixed
	 */
	public static function post( $key, $type = 'text', $default = '' ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			return $default;
		}
		$value = wp_unslash( $_POST[ $key ] );
		switch ( $type ) {
			case 'email':
				return sanitize_email( $value );
			case 'int':
				return (int) $value;
			case 'float':
				return (float) $value;
			case 'html':
				return wp_kses_post( $value );
			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Send a JSON success/error response.
	 *
	 * @param  bool   $success
	 * @param  string $message
	 * @param  array  $data
	 */
	public static function json_response( $success, $message = '', $data = array() ) {
		wp_send_json( array(
			'success' => (bool) $success,
			'message' => $message,
			'data'    => $data,
		) );
	}
}
