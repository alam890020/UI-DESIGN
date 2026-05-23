<?php
/**
 * Generic helpers used by the admin UI.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Helper {

    /**
     * Settings option getter.
     */
    public static function get_setting( $key, $default = '' ) {
        $opts = get_option( 'ssm_settings', array() );
        return isset( $opts[ $key ] ) ? $opts[ $key ] : $default;
    }

    /**
     * Build admin URL for a SSM page.
     */
    public static function admin_url( $slug, $args = array() ) {
        $args = array_merge( array( 'page' => $slug ), $args );
        return add_query_arg( $args, admin_url( 'admin.php' ) );
    }

    /**
     * Format currency.
     */
    public static function money( $amount ) {
        $sign = self::get_setting( 'currency_sign', '$' );
        return $sign . number_format( (float) $amount, 2 );
    }

    /**
     * Render avatar with initials.
     */
    public static function avatar( $name, $url = '' ) {
        if ( $url ) {
            return '<img class="ssm-avatar" src="' . esc_url( $url ) . '" alt="">';
        }
        $initials = '';
        $parts = preg_split( '/\s+/', trim( (string) $name ) );
        foreach ( $parts as $w ) {
            if ( $w !== '' ) { $initials .= strtoupper( $w[0] ); }
            if ( strlen( $initials ) >= 2 ) break;
        }
        if ( $initials === '' ) $initials = 'S';
        $colors = array( '#6366f1', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899' );
        $idx = abs( crc32( $name ) ) % count( $colors );
        return '<span class="ssm-avatar" style="background:' . $colors[ $idx ] . '">' . esc_html( $initials ) . '</span>';
    }

    /**
     * Stat card markup.
     */
    public static function stat_card( $label, $value, $icon = 'dashicons-chart-bar', $color = 'indigo', $trend = '' ) {
        ob_start(); ?>
        <div class="ssm-stat-card ssm-grad-<?php echo esc_attr( $color ); ?>">
            <div class="ssm-stat-icon"><span class="dashicons <?php echo esc_attr( $icon ); ?>"></span></div>
            <div class="ssm-stat-body">
                <div class="ssm-stat-value"><?php echo esc_html( $value ); ?></div>
                <div class="ssm-stat-label"><?php echo esc_html( $label ); ?></div>
                <?php if ( $trend ) : ?><div class="ssm-stat-trend"><?php echo esc_html( $trend ); ?></div><?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Status badge.
     */
    public static function badge( $status ) {
        $map = array(
            'active'   => 'success',
            'inactive' => 'muted',
            'paid'     => 'success',
            'unpaid'   => 'danger',
            'partial'  => 'warning',
            'present'  => 'success',
            'absent'   => 'danger',
            'leave'    => 'warning',
            'pending'  => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'open'     => 'info',
            'closed'   => 'muted',
            'new'      => 'info',
        );
        $cls = isset( $map[ strtolower( $status ) ] ) ? $map[ strtolower( $status ) ] : 'info';
        return '<span class="ssm-badge ssm-badge-' . esc_attr( $cls ) . '">' . esc_html( ucfirst( $status ) ) . '</span>';
    }

    /**
     * Render a placeholder page for modules under construction.
     */
    public static function render_placeholder( $title, $description, $icon = 'dashicons-admin-generic', $features = array() ) {
        ?>
        <div class="ssm-page">
            <?php SSM_Helper::page_header( $title, $description, $icon ); ?>
            <div class="ssm-card ssm-empty">
                <div class="ssm-empty-icon"><span class="dashicons <?php echo esc_attr( $icon ); ?>"></span></div>
                <h2><?php echo esc_html( $title ); ?></h2>
                <p><?php echo esc_html( $description ); ?></p>
                <?php if ( ! empty( $features ) ) : ?>
                <div class="ssm-feature-grid">
                    <?php foreach ( $features as $f ) : ?>
                        <div class="ssm-feature">
                            <span class="dashicons <?php echo esc_attr( isset($f['icon']) ? $f['icon'] : 'dashicons-yes' ); ?>"></span>
                            <div>
                                <strong><?php echo esc_html( $f['title'] ); ?></strong>
                                <p><?php echo esc_html( isset($f['desc']) ? $f['desc'] : '' ); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="ssm-empty-actions">
                    <button type="button" class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-plus"></span> Add New</button>
                    <button type="button" class="ssm-btn ssm-btn-ghost"><span class="dashicons dashicons-admin-tools"></span> Configure</button>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Page header with title, breadcrumb, action buttons.
     */
    public static function page_header( $title, $subtitle = '', $icon = 'dashicons-welcome-learn-more', $actions = array() ) {
        $brand = self::get_setting( 'school_name', get_bloginfo( 'name' ) );
        ?>
        <div class="ssm-page-header">
            <div class="ssm-page-title-wrap">
                <div class="ssm-page-icon"><span class="dashicons <?php echo esc_attr( $icon ); ?>"></span></div>
                <div>
                    <h1 class="ssm-page-title"><?php echo esc_html( $title ); ?></h1>
                    <?php if ( $subtitle ) : ?>
                        <p class="ssm-page-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                    <?php else : ?>
                        <p class="ssm-page-subtitle"><?php echo esc_html( $brand ); ?> &middot; Smart School Manager</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ( ! empty( $actions ) ) : ?>
                <div class="ssm-page-actions">
                    <?php foreach ( $actions as $a ) : ?>
                        <a href="<?php echo esc_url( $a['href'] ); ?>" class="ssm-btn <?php echo esc_attr( isset($a['class']) ? $a['class'] : 'ssm-btn-primary' ); ?>">
                            <?php if ( ! empty( $a['icon'] ) ) : ?><span class="dashicons <?php echo esc_attr( $a['icon'] ); ?>"></span><?php endif; ?>
                            <?php echo esc_html( $a['label'] ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
