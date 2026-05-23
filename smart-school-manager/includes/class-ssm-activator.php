<?php
/**
 * Plugin activation: create database tables and default data.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Activator {

    public static function activate() {
        require_once SSM_PLUGIN_DIR . 'includes/class-ssm-database.php';
        SSM_Database::install();

        // Default options.
        if ( false === get_option( 'ssm_settings' ) ) {
            add_option( 'ssm_settings', array(
                'school_name'    => get_bloginfo( 'name' ),
                'currency'       => 'USD',
                'currency_sign'  => '$',
                'date_format'    => 'Y-m-d',
                'theme_color'    => '#6366f1',
                'logo_url'       => '',
                'address'        => '',
                'phone'          => '',
                'email'          => get_bloginfo( 'admin_email' ),
                'session_id'     => 0,
            ) );
        }

        if ( false === get_option( 'ssm_db_version' ) ) {
            add_option( 'ssm_db_version', SSM_VERSION );
        }

        flush_rewrite_rules();
    }
}
