<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$students = $wpdb->get_results( "SELECT * FROM {$p}students ORDER BY id DESC LIMIT 12" );
$school = SSM_Helper::get_setting( 'school_name', 'Smart School' );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Student ID Cards', 'Preview and bulk-print student ID cards', 'dashicons-id',
        array( array( 'href' => '#', 'label' => 'Bulk Print', 'icon' => 'dashicons-printer' ) ) ); ?>

    <div class="ssm-card">
        <div class="ssm-card-header">
            <div class="ssm-card-title"><span class="dashicons dashicons-id"></span> Live Previews</div>
            <select class="ssm-select" style="max-width:200px"><option>All Classes</option></select>
        </div>
        <?php if ( $students ) : ?>
            <div style="display:flex;flex-wrap:wrap;gap:18px">
                <?php foreach ( $students as $s ) : $name = trim( $s->first_name . ' ' . $s->last_name ); ?>
                    <div class="ssm-id-card">
                        <div class="ssm-id-card-photo"><?php echo esc_html( strtoupper( substr( $name, 0, 1 ) ) ); ?></div>
                        <div class="ssm-id-card-info">
                            <div class="ssm-id-card-school"><?php echo esc_html( $school ); ?></div>
                            <strong><?php echo esc_html( $name ); ?></strong>
                            Adm No: <?php echo esc_html( $s->admission_no ); ?><br>
                            Class: <?php echo (int) $s->class_id; ?><br>
                            Phone: <?php echo esc_html( $s->phone ); ?><br>
                            <span style="display:inline-block;margin-top:6px;padding:2px 8px;border-radius:6px;background:rgba(255,255,255,.2);font-size:10px">Valid 2025-26</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="ssm-muted">Add students first to generate ID cards.</p>
        <?php endif; ?>
    </div>
</div>
