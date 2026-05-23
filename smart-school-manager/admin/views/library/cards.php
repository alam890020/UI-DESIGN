<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$students = $wpdb->get_results( "SELECT * FROM {$p}students ORDER BY id DESC LIMIT 12" );
$school = SSM_Helper::get_setting( 'school_name', 'Smart School' );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Library Cards', 'Generate library membership cards', 'dashicons-id' ); ?>
    <div class="ssm-card">
        <?php if ( $students ) : ?>
        <div style="display:flex;flex-wrap:wrap;gap:18px">
            <?php foreach ( $students as $s ) : $name = trim( $s->first_name . ' ' . $s->last_name ); ?>
                <div class="ssm-id-card" style="background:linear-gradient(135deg,#8b5cf6,#06b6d4)">
                    <div class="ssm-id-card-photo"><span class="dashicons dashicons-book" style="color:#fff;font-size:28px"></span></div>
                    <div class="ssm-id-card-info">
                        <div class="ssm-id-card-school"><?php echo esc_html( $school ); ?> · LIBRARY</div>
                        <strong><?php echo esc_html( $name ); ?></strong>
                        Adm No: <?php echo esc_html( $s->admission_no ); ?><br>
                        Member: LIB-<?php echo (int) $s->id; ?><br>
                        Valid: 2025-26
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else : ?><p class="ssm-muted">Add students to generate library cards.</p><?php endif; ?>
    </div>
</div>
