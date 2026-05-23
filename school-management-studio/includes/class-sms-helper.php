<?php
/**
 * Helper functions and reusable UI snippets.
 *
 * @package SchoolManagementStudio
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_Helper {

    public static function get_setting( $key, $default = '' ) {
        $opts = get_option( 'sms_settings', array() );
        return isset( $opts[ $key ] ) ? $opts[ $key ] : $default;
    }

    public static function admin_url( $slug, $args = array() ) {
        $args = array_merge( array( 'page' => $slug ), $args );
        return add_query_arg( $args, admin_url( 'admin.php' ) );
    }

    public static function print_url( $template, $id = 0, $autoprint = false ) {
        return add_query_arg( array(
            'action'    => 'sms_print',
            't'         => $template,
            'id'        => $id,
            'autoprint' => $autoprint ? 1 : 0,
        ), admin_url( 'admin-post.php' ) );
    }

    public static function money( $amount ) {
        $sign = self::get_setting( 'currency_sign', '$' );
        return $sign . number_format( (float) $amount, 2 );
    }

    public static function avatar( $name, $url = '' ) {
        if ( $url ) {
            return '<img class="sms-avatar" src="' . esc_url( $url ) . '" alt="">';
        }
        $initials = '';
        foreach ( preg_split( '/\s+/', trim( (string) $name ) ) as $w ) {
            if ( $w !== '' ) $initials .= strtoupper( $w[0] );
            if ( strlen( $initials ) >= 2 ) break;
        }
        if ( $initials === '' ) $initials = 'S';
        $colors = array( '#7c3aed', '#06b6d4', '#10b981', '#f59e0b', '#f43f5e', '#3b82f6', '#ec4899', '#14b8a6' );
        $idx = abs( crc32( $name ) ) % count( $colors );
        return '<span class="sms-avatar" style="background:' . $colors[ $idx ] . '">' . esc_html( $initials ) . '</span>';
    }

    public static function badge( $status ) {
        $map = array(
            'active' => 'success', 'inactive' => 'muted',
            'paid' => 'success', 'unpaid' => 'danger', 'partial' => 'warn',
            'present' => 'success', 'absent' => 'danger', 'leave' => 'warn',
            'pending' => 'warn', 'approved' => 'success', 'rejected' => 'danger',
            'open' => 'info', 'closed' => 'muted', 'new' => 'info',
        );
        $cls = isset( $map[ strtolower( $status ) ] ) ? $map[ strtolower( $status ) ] : 'info';
        return '<span class="sms-pill sms-pill-' . esc_attr( $cls ) . '">' . esc_html( ucfirst( $status ) ) . '</span>';
    }

    /**
     * Stat card.
     */
    public static function stat( $label, $value, $icon = 'chart-line', $tone = 'violet', $delta = '' ) {
        ob_start(); ?>
        <div class="sms-stat sms-tone-<?php echo esc_attr( $tone ); ?>">
            <div class="sms-stat-icon"><?php echo SMS_Icons::svg( $icon, 22 ); ?></div>
            <div class="sms-stat-body">
                <div class="sms-stat-num"><?php echo esc_html( $value ); ?></div>
                <div class="sms-stat-lbl"><?php echo esc_html( $label ); ?></div>
                <?php if ( $delta ) : ?><div class="sms-stat-delta"><?php echo esc_html( $delta ); ?></div><?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Page header (used at the top of every admin view).
     */
    public static function page_header( $title, $subtitle = '', $icon = 'dashboard', $actions = array() ) {
        ?>
        <div class="sms-page-head">
            <div class="sms-page-head-l">
                <div class="sms-page-icon"><?php echo SMS_Icons::svg( $icon, 22 ); ?></div>
                <div>
                    <h1 class="sms-page-title"><?php echo esc_html( $title ); ?></h1>
                    <?php if ( $subtitle ) : ?><p class="sms-page-sub"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
                </div>
            </div>
            <?php if ( ! empty( $actions ) ) : ?>
                <div class="sms-page-head-r">
                    <?php foreach ( $actions as $a ) : ?>
                        <a href="<?php echo esc_url( $a['href'] ); ?>" class="sms-btn <?php echo esc_attr( isset($a['class']) ? $a['class'] : 'sms-btn-primary' ); ?>">
                            <?php if ( ! empty( $a['icon'] ) ) echo SMS_Icons::svg( $a['icon'], 16 ); ?>
                            <span><?php echo esc_html( $a['label'] ); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
