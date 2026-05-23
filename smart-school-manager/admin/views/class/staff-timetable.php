<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$days = array('Mon','Tue','Wed','Thu','Fri','Sat');
$periods = array('P1','P2','P3','P4','P5','P6');
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Staff Timetable', 'Period-wise schedule for each staff member', 'dashicons-businesswoman' ); ?>

    <div class="ssm-card">
        <div class="ssm-card-header">
            <div class="ssm-card-title"><span class="dashicons dashicons-clock"></span> Mr. R. Sharma — Weekly</div>
            <select class="ssm-select" style="max-width:220px"><option>Mr. R. Sharma</option><option>Ms. K. Verma</option></select>
        </div>
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>Day</th><?php foreach ( $periods as $pe ) : ?><th><?php echo esc_html( $pe ); ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                <?php foreach ( $days as $d ) : ?>
                    <tr>
                        <td><strong><?php echo esc_html( $d ); ?></strong></td>
                        <?php foreach ( $periods as $i => $pe ) : ?>
                            <td><span class="ssm-badge ssm-badge-success">Class 5-A · Math</span></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
