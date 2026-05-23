<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Assessment', 'Configure assessment & grading scales', 'dashicons-chart-bar' ); ?>

    <div class="ssm-card">
        <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-shield"></span> Default Grade Scale</div></div>
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>Grade</th><th>From %</th><th>To %</th><th>Remarks</th></tr></thead>
                <tbody>
                <?php $scale = array(
                    array('A+',91,100,'Outstanding'),
                    array('A',81,90,'Excellent'),
                    array('B+',71,80,'Very Good'),
                    array('B',61,70,'Good'),
                    array('C',51,60,'Average'),
                    array('D',40,50,'Pass'),
                    array('F',0,39,'Fail'),
                );
                foreach ( $scale as $g ) : ?>
                    <tr>
                        <td><span class="ssm-badge ssm-badge-success"><?php echo esc_html( $g[0] ); ?></span></td>
                        <td><?php echo (int) $g[1]; ?>%</td>
                        <td><?php echo (int) $g[2]; ?>%</td>
                        <td><?php echo esc_html( $g[3] ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button class="ssm-btn ssm-btn-primary" style="margin-top:14px"><span class="dashicons dashicons-edit"></span> Customize Scale</button>
    </div>
</div>
