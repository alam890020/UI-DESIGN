<?php
/**
 * Activation hook: install schema + default options.
 *
 * @package SchoolManagementStudio
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_Activator {
    public static function activate() {
        SMS_Database::install();

        if ( false === get_option( 'sms_settings' ) ) {
            add_option( 'sms_settings', array(
                'school_name'   => get_bloginfo( 'name' ),
                'currency'      => 'USD',
                'currency_sign' => '$',
                'date_format'   => 'Y-m-d',
                'theme_color'   => '#7c3aed',
                'accent_color'  => '#06b6d4',
                'logo_url'      => '',
                'address'       => '',
                'phone'         => '',
                'email'         => get_bloginfo( 'admin_email' ),
                'dark_mode'     => 0,
                'load_cdn_libs' => 1,
            ) );
        }
        add_option( 'sms_db_version', SMS_VERSION );
        flush_rewrite_rules();
    }
}
