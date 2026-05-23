<?php
/**
 * Lightweight email helper.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Email {

    /**
     * Send an HTML email branded with the school's name.
     *
     * @param string|array $to
     * @param string       $subject
     * @param string       $body
     * @param array        $attachments
     * @return bool
     */
    public static function send( $to, $subject, $body, $attachments = array() ) {
        $school = SSM_Helper::get_setting( 'school_name', get_bloginfo( 'name' ) );
        $from   = SSM_Helper::get_setting( 'email', get_bloginfo( 'admin_email' ) );

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $school . ' <' . $from . '>',
        );

        $html = self::wrap( $subject, $body );

        return wp_mail( $to, $subject, $html, $headers, $attachments );
    }

    /**
     * Wrap a message in a simple branded HTML template.
     */
    public static function wrap( $title, $body ) {
        $school = esc_html( SSM_Helper::get_setting( 'school_name', get_bloginfo( 'name' ) ) );
        $color  = esc_attr( SSM_Helper::get_setting( 'theme_color', '#6366f1' ) );

        $html  = '<!doctype html><html><body style="margin:0;background:#f4f6fb;font-family:Arial,Helvetica,sans-serif">';
        $html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:24px">';
        $html .= '<tr><td align="center"><table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 6px 18px rgba(15,23,42,.06)">';
        $html .= '<tr><td style="background:' . $color . ';padding:20px 24px;color:#fff;font-weight:700;font-size:18px">' . $school . '</td></tr>';
        $html .= '<tr><td style="padding:24px;color:#0f172a;font-size:14px;line-height:1.6">';
        $html .= '<h2 style="margin:0 0 12px;font-size:18px;color:#0f172a">' . esc_html( $title ) . '</h2>';
        $html .= wpautop( wp_kses_post( $body ) );
        $html .= '</td></tr>';
        $html .= '<tr><td style="padding:14px 24px;background:#f8fafc;color:#64748b;font-size:11px">Sent by ' . $school . ' &middot; Smart School Manager</td></tr>';
        $html .= '</table></td></tr></table></body></html>';

        return $html;
    }
}
