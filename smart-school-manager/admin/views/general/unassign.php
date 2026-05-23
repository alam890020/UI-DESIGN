<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Unassign Class', 'Remove students from their currently assigned class', 'dashicons-trash' ); ?>
    <div class="ssm-card">
        <div class="ssm-form-grid">
            <div class="ssm-field"><label>Class</label>
                <select class="ssm-select"><?php foreach ( $classes as $c ) : ?><option><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?></select>
            </div>
            <div class="ssm-field"><label>Reason</label><input class="ssm-input" placeholder="Optional reason"></div>
        </div>
        <button class="ssm-btn ssm-btn-danger" style="margin-top:14px"><span class="dashicons dashicons-warning"></span> Unassign Selected</button>
    </div>
</div>
