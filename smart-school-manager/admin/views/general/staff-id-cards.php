<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}staff ORDER BY id DESC LIMIT 12" );
$school = SSM_Helper::get_setting( 'school_name', 'Smart School' );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Staff ID Cards', 'Preview and print staff ID cards', 'dashicons-id-alt',
        array( array( 'href' => '#', 'label' => 'Bulk Print', 'icon' => 'dashicons-printer' ) ) ); ?>

    <div class="ssm-card">
        <?php if ( $rows ) : ?>
        <div style="display:flex;flex-wrap:wrap;gap:18px">
            <?php foreach ( $rows as $s ) : $name = trim( $s->first_name . ' ' . $s->last_name ); ?>
                <div class="ssm-id-card" style="background:linear-gradient(135deg,#10b981,#059669)">
                    <div class="ssm-id-card-photo"><?php echo esc_html( strtoupper( substr( $name, 0, 1 ) ) ); ?></div>
                    <div class="ssm-id-card-info">
                        <div class="ssm-id-card-school"><?php echo esc_html( $school ); ?> · STAFF</div>
                        <strong><?php echo esc_html( $name ); ?></strong>
                        Emp No: <?php echo esc_html( $s->employee_no ); ?><br>
                        <?php echo esc_html( $s->designation ); ?><br>
                        Phone: <?php echo esc_html( $s->phone ); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else : ?><p class="ssm-muted">Add staff to generate ID cards.</p><?php endif; ?>
    </div>
</div>
