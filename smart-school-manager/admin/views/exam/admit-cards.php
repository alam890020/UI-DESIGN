<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$students = $wpdb->get_results( "SELECT * FROM {$p}students ORDER BY id DESC LIMIT 8" );
$school = SSM_Helper::get_setting( 'school_name', 'Smart School' );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Admit Cards', 'Generate individual or bulk admit cards', 'dashicons-tickets-alt',
        array( array( 'href' => '#', 'label' => 'Bulk Print', 'icon' => 'dashicons-printer' ) ) ); ?>

    <div class="ssm-card">
        <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-id"></span> Previews</div></div>
        <?php if ( $students ) : ?>
        <div style="display:flex;flex-wrap:wrap;gap:18px">
            <?php foreach ( $students as $s ) : $name = trim( $s->first_name . ' ' . $s->last_name ); ?>
                <div class="ssm-id-card" style="width:360px;height:220px;background:linear-gradient(135deg,#7c3aed,#ec4899)">
                    <div class="ssm-id-card-photo" style="width:90px;height:110px"><?php echo esc_html( strtoupper( substr( $name, 0, 1 ) ) ); ?></div>
                    <div class="ssm-id-card-info">
                        <div class="ssm-id-card-school"><?php echo esc_html( $school ); ?></div>
                        <strong style="font-size:15px"><?php echo esc_html( $name ); ?></strong>
                        Adm No: <?php echo esc_html( $s->admission_no ); ?><br>
                        Roll: <?php echo esc_html( $s->roll_no ); ?><br>
                        Class: <?php echo (int) $s->class_id; ?><br>
                        <span style="display:inline-block;margin-top:6px;padding:3px 10px;border-radius:6px;background:rgba(255,255,255,.25);font-weight:700">ADMIT CARD</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else : ?><p class="ssm-muted">Add students to generate admit cards.</p><?php endif; ?>
    </div>
</div>
