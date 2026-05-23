<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes ORDER BY name" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Promote Students', 'Bulk-promote students to the next class for a new session', 'dashicons-controls-fastforward' ); ?>

    <div class="ssm-card">
        <div class="ssm-form-grid-3">
            <div class="ssm-field"><label>From Class</label>
                <select class="ssm-select"><?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?></select>
            </div>
            <div class="ssm-field"><label>To Class</label>
                <select class="ssm-select"><?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?></select>
            </div>
            <div class="ssm-field"><label>New Session</label><input class="ssm-input" placeholder="2025-2026"></div>
        </div>
        <div style="margin-top:14px"><button class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-search"></span> Load Students</button></div>
    </div>

    <div class="ssm-card">
        <div class="ssm-card-title"><span class="dashicons dashicons-info"></span> How promotion works</div>
        <p class="ssm-muted" style="margin-top:8px">Selected students keep their admission numbers and history; only their <strong>class</strong>, <strong>section</strong> and <strong>session</strong> are updated. Failed/repeating students can be unchecked before promotion.</p>
    </div>
</div>
