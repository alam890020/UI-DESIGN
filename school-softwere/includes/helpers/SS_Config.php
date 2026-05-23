<?php
/**
 * SS_Config — Plugin configuration values.
 *
 * @package School_Softwere
 */

defined( 'ABSPATH' ) || die();

class SS_Config {

	/** @var array Cached settings */
	private static $cache = array();

	/**
	 * Get a setting value from wp_ss_settings for a school.
	 *
	 * @param  string $key
	 * @param  int    $school_id
	 * @param  mixed  $default
	 * @return mixed
	 */
	public static function get( $key, $school_id = 0, $default = '' ) {
		if ( ! $school_id ) {
			$school_id = SS_Helper::get_current_school_id();
		}
		$cache_key = $school_id . ':' . $key;
		if ( isset( self::$cache[ $cache_key ] ) ) {
			return self::$cache[ $cache_key ];
		}
		global $wpdb;
		$value = $wpdb->get_var( $wpdb->prepare(
			"SELECT setting_value FROM {$wpdb->prefix}ss_settings WHERE school_id = %d AND setting_key = %s LIMIT 1",
			$school_id,
			$key
		) );
		$result = ( null !== $value ) ? $value : $default;
		self::$cache[ $cache_key ] = $result;
		return $result;
	}

	/**
	 * Save a setting value.
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 * @param  int    $school_id
	 */
	public static function set( $key, $value, $school_id = 0 ) {
		if ( ! $school_id ) {
			$school_id = SS_Helper::get_current_school_id();
		}
		global $wpdb;
		$existing = $wpdb->get_var( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->prefix}ss_settings WHERE school_id = %d AND setting_key = %s LIMIT 1",
			$school_id,
			$key
		) );
		if ( $existing ) {
			$wpdb->update(
				$wpdb->prefix . 'ss_settings',
				array( 'setting_value' => $value ),
				array( 'school_id' => $school_id, 'setting_key' => $key )
			);
		} else {
			$wpdb->insert( $wpdb->prefix . 'ss_settings', array(
				'school_id'    => $school_id,
				'setting_key'  => $key,
				'setting_value'=> $value,
			) );
		}
		// Invalidate cache
		unset( self::$cache[ $school_id . ':' . $key ] );
	}

	/**
	 * Get all settings for a school as an associative array.
	 *
	 * @param  int $school_id
	 * @return array
	 */
	public static function get_all( $school_id = 0 ) {
		if ( ! $school_id ) {
			$school_id = SS_Helper::get_current_school_id();
		}
		global $wpdb;
		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT setting_key, setting_value FROM {$wpdb->prefix}ss_settings WHERE school_id = %d",
			$school_id
		) );
		$settings = array();
		foreach ( $rows as $row ) {
			$settings[ $row->setting_key ] = $row->setting_value;
		}
		return $settings;
	}

	/**
	 * Return plugin default settings definition.
	 *
	 * @return array
	 */
	public static function defaults() {
		return array(
			'school_name'          => 'School Softwere',
			'school_tagline'       => 'Empowering Education',
			'currency_symbol'      => '$',
			'date_format'          => 'd/m/Y',
			'academic_year_month'  => '4',
			'invoice_prefix'       => 'INV',
			'invoice_padding'      => '6',
			'late_fee_amount'      => '0',
			'late_fee_after_days'  => '7',
			'smtp_host'            => '',
			'smtp_port'            => '587',
			'smtp_user'            => '',
			'smtp_pass'            => '',
			'from_email'           => get_option( 'admin_email' ),
			'from_name'            => get_option( 'blogname' ),
			'sms_api_key'          => '',
			'sms_sender_id'        => '',
			'print_page_size'      => 'A4',
			'print_orientation'    => 'portrait',
			'print_watermark'      => '0',
			'working_days'         => 'Mon,Tue,Wed,Thu,Fri',
		);
	}
}
