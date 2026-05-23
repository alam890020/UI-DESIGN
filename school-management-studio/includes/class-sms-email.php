<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
class SMS_Email {
    public static function send( $to, $subject, $body, $attachments = array() ) {
        $school = SMS_Helper::get_setting( 'school_name', get_bloginfo( 'name' ) );
        $from   = SMS_Helper::get_setting( 'email', get_bloginfo( 'admin_email' ) );
        $headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $school . ' <' . $from . '>' );
        return wp_mail( $to, $subject, self::wrap( $subject, $body ), $headers, $attachments );
    }
    public static function wrap( $title, $body ) {
        $school = esc_html( SMS_Helper::get_setting( 'school_name', get_bloginfo( 'name' ) ) );
        $color  = esc_attr( SMS_Helper::get_setting( 'theme_color', '#7c3aed' ) );
        $html  = '<!doctype html><html><body style="margin:0;background:#f4f5fb;font-family:Arial,Helvetica,sans-serif">';
        $html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5fb;padding:24px"><tr><td align="center"><table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(11,16,32,.07)">';
        $html .= '<tr><td style="background:linear-gradient(135deg,' . $color . ',#06b6d4);padding:20px 24px;color:#fff;font-weight:700;font-size:18px">' . $school . '</td></tr>';
        $html .= '<tr><td style="padding:24px;color:#0b1020;font-size:14px;line-height:1.6"><h2 style="margin:0 0 12px;font-size:18px">' . esc_html( $title ) . '</h2>' . wpautop( wp_kses_post( $body ) ) . '</td></tr>';
        $html .= '<tr><td style="padding:14px 24px;background:#fafbff;color:#5b6175;font-size:11px">Sent by ' . $school . ' · School Management Studio</td></tr>';
        $html .= '</table></td></tr></table></body></html>';
        return $html;
    }
}
