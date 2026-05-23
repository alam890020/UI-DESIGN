<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$students = $wpdb->get_results( "SELECT * FROM {$p}students ORDER BY first_name LIMIT 200" );
$classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes ORDER BY name" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Transfer Student', 'Move students between classes or schools', 'dashicons-randomize' ); ?>

    <div class="ssm-card">
        <form>
            <div class="ssm-form-grid-3">
                <div class="ssm-field"><label>Student</label>
                    <select class="ssm-select">
                        <?php foreach ( $students as $s ) : ?>
                            <option><?php echo esc_html( trim( $s->first_name . ' ' . $s->last_name ) . ' (' . $s->admission_no . ')' ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="ssm-field"><label>From Class</label>
                    <select class="ssm-select">
                        <?php foreach ( $classes as $c ) : ?><option><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="ssm-field"><label>To Class</label>
                    <select class="ssm-select">
                        <?php foreach ( $classes as $c ) : ?><option><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="ssm-field" style="margin-top:12px"><label>Reason</label><textarea class="ssm-textarea"></textarea></div>
            <button class="ssm-btn ssm-btn-primary" style="margin-top:14px"><span class="dashicons dashicons-controls-skipforward"></span> Transfer</button>
        </form>
    </div>
</div>
