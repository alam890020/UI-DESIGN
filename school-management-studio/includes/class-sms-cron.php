<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_Cron {
    public function register() {
        add_action( 'sms_daily_cron',  array( $this, 'daily' ) );
        add_action( 'sms_hourly_cron', array( $this, 'hourly' ) );
        if ( ! wp_next_scheduled( 'sms_daily_cron' ) )  wp_schedule_event( time() + 60, 'daily',  'sms_daily_cron' );
        if ( ! wp_next_scheduled( 'sms_hourly_cron' ) ) wp_schedule_event( time() + 60, 'hourly', 'sms_hourly_cron' );
    }
    public function daily() {
        global $wpdb; $p = $wpdb->prefix . 'sms_';
        $today = current_time( 'm-d' );
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, first_name, last_name, email FROM {$p}students WHERE status='active' AND DATE_FORMAT(dob,'%%m-%%d')=%s", $today ) );
        foreach ( $rows as $s ) {
            $name = trim( $s->first_name . ' ' . $s->last_name );
            $title = '🎂 Happy Birthday, ' . $name . '!';
            $msg = "Wishing you a wonderful birthday from " . SMS_Helper::get_setting( 'school_name', 'School' ) . '.';
            $wpdb->insert( $p . 'notifications', array( 'channel' => 'inapp', 'title' => $title, 'message' => $msg, 'audience' => 'student', 'target_id' => $s->id, 'sent_at' => current_time( 'mysql' ), 'status' => 'sent' ) );
            if ( $s->email ) SMS_Email::send( $s->email, $title, $msg );
        }
    }
    public function hourly() {
        global $wpdb;
        $wpdb->insert( $wpdb->prefix . 'sms_logs', array( 'user_id' => 0, 'action' => 'tick', 'module' => 'cron-hourly', 'ip' => 'cron' ) );
    }
}
