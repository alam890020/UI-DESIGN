<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$days = array('Mon','Tue','Wed','Thu','Fri','Sat');
$periods = array('8:00 - 9:00','9:00 - 10:00','10:00 - 11:00','11:30 - 12:30','12:30 - 1:30','2:30 - 3:30');
$subs = array('Math','Science','English','History','Computer','PE','Art','Music','Hindi','Geography');
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Class Routines / Timetable', 'Create and visualise class timetables', 'dashicons-clock',
        array( array( 'href' => '#', 'label' => 'Print Timetable', 'icon' => 'dashicons-printer', 'class' => 'ssm-btn-ghost' ) ) ); ?>

    <div class="ssm-card">
        <div class="ssm-card-header">
            <div class="ssm-card-title"><span class="dashicons dashicons-calendar-alt"></span> Class 5-A Timetable</div>
            <select class="ssm-select" style="max-width:200px"><option>Class 5-A</option><option>Class 5-B</option></select>
        </div>
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>Day</th><?php foreach ( $periods as $pe ) : ?><th><?php echo esc_html( $pe ); ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                <?php foreach ( $days as $d ) : ?>
                    <tr>
                        <td><strong><?php echo esc_html( $d ); ?></strong></td>
                        <?php foreach ( $periods as $i => $pe ) : $sub = $subs[ ( crc32( $d . $i ) & 0x7fffffff ) % count( $subs ) ]; ?>
                            <td><span class="ssm-badge ssm-badge-info" style="background:linear-gradient(135deg,#dbeafe,#fce7f3);color:#1e3a8a"><?php echo esc_html( $sub ); ?></span></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
