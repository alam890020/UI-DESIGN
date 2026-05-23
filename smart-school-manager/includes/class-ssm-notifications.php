<?php
/**
 * Notification dispatcher (in-app, email, optional sms/push hooks).
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Notifications {

    /**
     * Send a notification to recipients.
     *
     * @param array $args {
     *   @type string       channel     'inapp'|'email'|'sms'|'push'
     *   @type string       audience    'all'|'students'|'staff'|'class'|'parents'|'user'
     *   @type int          target_id   class_id or user_id depending on audience
     *   @type string       title
     *   @type string       message
     * }
     */
    public static function send( $args ) {
        $args = wp_parse_args( $args, array(
            'channel'   => 'inapp',
            'audience'  => 'all',
            'target_id' => 0,
            'title'     => '',
            'message'   => '',
        ) );

        global $wpdb;
        $p = $wpdb->prefix . 'ssm_';
        $wpdb->insert( $p . 'notifications', array(
            'channel'   => $args['channel'],
            'title'     => $args['title'],
            'message'   => $args['message'],
            'audience'  => $args['audience'],
            'target_id' => (int) $args['target_id'],
            'sent_at'   => current_time( 'mysql' ),
            'status'    => 'sent',
        ) );

        if ( 'email' === $args['channel'] ) {
            $emails = self::resolve_emails( $args['audience'], (int) $args['target_id'] );
            if ( $emails ) {
                SSM_Email::send( $emails, $args['title'], $args['message'] );
            }
        }

        do_action( 'ssm_notification_sent', $args );
        return true;
    }

    /**
     * Resolve audience -> list of email addresses.
     */
    public static function resolve_emails( $audience, $target_id = 0 ) {
        global $wpdb; $p = $wpdb->prefix . 'ssm_';
        $emails = array();

        switch ( $audience ) {
            case 'students':
                $emails = $wpdb->get_col( "SELECT email FROM {$p}students WHERE status='active' AND email<>''" );
                break;
            case 'staff':
                $emails = $wpdb->get_col( "SELECT email FROM {$p}staff WHERE status='active' AND email<>''" );
                break;
            case 'class':
                $emails = $wpdb->get_col( $wpdb->prepare(
                    "SELECT email FROM {$p}students WHERE class_id=%d AND status='active' AND email<>''",
                    $target_id
                ) );
                break;
            case 'all':
                $a = $wpdb->get_col( "SELECT email FROM {$p}students WHERE status='active' AND email<>''" );
                $b = $wpdb->get_col( "SELECT email FROM {$p}staff WHERE status='active' AND email<>''" );
                $emails = array_unique( array_merge( $a, $b ) );
                break;
            case 'user':
                $u = get_userdata( $target_id );
                if ( $u && $u->user_email ) $emails = array( $u->user_email );
                break;
        }
        return array_filter( $emails );
    }
}
