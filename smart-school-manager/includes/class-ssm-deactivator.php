<?php
/**
 * Plugin deactivation.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Deactivator {
    public static function deactivate() {
        wp_clear_scheduled_hook( 'ssm_daily_cron' );
        wp_clear_scheduled_hook( 'ssm_hourly_cron' );
        flush_rewrite_rules();
    }
}
