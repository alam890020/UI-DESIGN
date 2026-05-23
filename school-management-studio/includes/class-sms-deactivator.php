<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_Deactivator {
    public static function deactivate() {
        wp_clear_scheduled_hook( 'sms_daily_cron' );
        wp_clear_scheduled_hook( 'sms_hourly_cron' );
        flush_rewrite_rules();
    }
}
