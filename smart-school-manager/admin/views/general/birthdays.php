<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$month = (int) date( 'm' );
$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$p}students WHERE MONTH(dob)=%d ORDER BY DAY(dob)", $month ) );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Student Birthdays', 'Birthday celebrations and notifications', 'dashicons-buddicons-buddypress-logo' ); ?>

    <div class="ssm-card">
        <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-calendar-alt"></span> This Month (<?php echo esc_html( date('F') ); ?>)</div></div>
        <div class="ssm-list">
        <?php if ( $rows ) : foreach ( $rows as $s ) : $name = trim( $s->first_name . ' ' . $s->last_name ); ?>
            <div class="ssm-list-item">
                <?php echo SSM_Helper::avatar( $name ); ?>
                <div style="flex:1">
                    <strong><?php echo esc_html( $name ); ?></strong>
                    <div class="meta">🎂 <?php echo esc_html( date( 'd M', strtotime( $s->dob ) ) ); ?></div>
                </div>
                <button class="ssm-btn ssm-btn-ghost ssm-btn-sm"><span class="dashicons dashicons-email"></span> Send Wish</button>
            </div>
        <?php endforeach; else : ?>
            <p class="ssm-muted">No birthdays this month.</p>
        <?php endif; ?>
        </div>
    </div>
</div>
