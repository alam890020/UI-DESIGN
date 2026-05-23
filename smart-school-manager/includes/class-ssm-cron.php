<?php
/**
 * Scheduled tasks (WP-Cron).
 *
 * - Daily: birthday wishes, fee reminders, due homework follow-up
 * - Hourly: maintenance and lightweight cleanup
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Cron {

    public function register() {
        add_action( 'ssm_daily_cron',  array( $this, 'daily' ) );
        add_action( 'ssm_hourly_cron', array( $this, 'hourly' ) );
        add_filter( 'cron_schedules',  array( $this, 'schedules' ) );

        // Schedule on plugin load if not yet scheduled.
        if ( ! wp_next_scheduled( 'ssm_daily_cron' ) ) {
            wp_schedule_event( time() + 60, 'daily', 'ssm_daily_cron' );
        }
        if ( ! wp_next_scheduled( 'ssm_hourly_cron' ) ) {
            wp_schedule_event( time() + 60, 'hourly', 'ssm_hourly_cron' );
        }
    }

    public function schedules( $schedules ) {
        $schedules['ssm_5min'] = array( 'interval' => 5 * MINUTE_IN_SECONDS, 'display' => 'Every 5 minutes (SSM)' );
        return $schedules;
    }

    public function daily() {
        $this->birthday_notifications();
        $this->fee_reminders();
        $this->log( 'daily-cron', 'completed' );
    }

    public function hourly() {
        $this->log( 'hourly-cron', 'tick' );
    }

    private function birthday_notifications() {
        global $wpdb;
        $p   = $wpdb->prefix . 'ssm_';
        $today = current_time( 'm-d' );
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, first_name, last_name, email FROM {$p}students WHERE status='active' AND DATE_FORMAT(dob,'%%m-%%d')=%s",
            $today
        ) );

        foreach ( $rows as $s ) {
            $name = trim( $s->first_name . ' ' . $s->last_name );
            $title = '🎂 Happy Birthday, ' . $name . '!';
            $msg   = "Wishing you a fantastic birthday from all of us at " . SSM_Helper::get_setting( 'school_name', 'School' ) . "!";

            $wpdb->insert( $p . 'notifications', array(
                'channel'   => 'inapp',
                'title'     => $title,
                'message'   => $msg,
                'audience'  => 'student',
                'target_id' => $s->id,
                'sent_at'   => current_time( 'mysql' ),
                'status'    => 'sent',
            ) );

            if ( $s->email ) {
                SSM_Email::send( $s->email, $title, $msg );
            }
        }
    }

    private function fee_reminders() {
        global $wpdb;
        $p = $wpdb->prefix . 'ssm_';
        $rows = $wpdb->get_results( "SELECT i.*, s.email, s.first_name, s.last_name FROM {$p}invoices i LEFT JOIN {$p}students s ON s.id=i.student_id WHERE i.status='unpaid' AND i.due_date <= CURDATE() LIMIT 200" );
        foreach ( $rows as $r ) {
            $name = trim( $r->first_name . ' ' . $r->last_name );
            $title = 'Fee Payment Reminder';
            $msg = "Dear $name, invoice #{$r->invoice_no} of " . SSM_Helper::money( $r->amount ) . " is overdue. Please pay at your earliest convenience.";
            $wpdb->insert( $p . 'notifications', array(
                'channel'   => 'inapp',
                'title'     => $title,
                'message'   => $msg,
                'audience'  => 'student',
                'target_id' => $r->student_id,
                'sent_at'   => current_time( 'mysql' ),
                'status'    => 'sent',
            ) );
            if ( $r->email ) {
                SSM_Email::send( $r->email, $title, $msg );
            }
        }
    }

    private function log( $module, $action ) {
        global $wpdb;
        $wpdb->insert( $wpdb->prefix . 'ssm_logs', array(
            'user_id' => 0,
            'action'  => $action,
            'module'  => $module,
            'ip'      => 'cron',
        ) );
    }
}
