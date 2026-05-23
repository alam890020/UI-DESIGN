<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$students = $wpdb->get_results( "SELECT * FROM {$p}students ORDER BY id DESC LIMIT 50" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Transfer Certificates', 'Generate official Transfer Certificates (TC)', 'dashicons-migrate' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Eligible Students</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Student</th><th>Adm No</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ( $students as $s ) : $name = trim( $s->first_name . ' ' . $s->last_name ); ?>
                        <tr>
                            <td><div class="ssm-row-name"><?php echo SSM_Helper::avatar( $name ); ?><?php echo esc_html( $name ); ?></div></td>
                            <td><?php echo esc_html( $s->admission_no ); ?></td>
                            <td><?php echo SSM_Helper::badge( $s->status ); ?></td>
                            <td><button class="ssm-btn ssm-btn-primary ssm-btn-sm"><span class="dashicons dashicons-printer"></span> Generate TC</button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-title"><span class="dashicons dashicons-info"></span> TC Template</div>
            <p class="ssm-muted" style="margin-top:8px">A polished printable TC is generated with school name, student details, dates, conduct remarks and signature blocks. Customize the template under General &rarr; School Settings.</p>
            <button class="ssm-btn ssm-btn-ghost" style="margin-top:14px"><span class="dashicons dashicons-edit"></span> Edit Template</button>
        </div>
    </div>
</div>
