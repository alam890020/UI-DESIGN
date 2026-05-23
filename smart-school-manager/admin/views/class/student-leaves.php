<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT l.*, CONCAT(s.first_name,' ',s.last_name) AS student_name FROM {$p}student_leaves l LEFT JOIN {$p}students s ON s.id=l.student_id ORDER BY l.id DESC LIMIT 50" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Student Leaves', 'Approve and track student leave applications', 'dashicons-clipboard' ); ?>
    <div class="ssm-card">
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>Student</th><th>Type</th><th>From</th><th>To</th><th>Reason</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                    <tr>
                        <td><div class="ssm-row-name"><?php echo SSM_Helper::avatar( $r->student_name ); ?><?php echo esc_html( $r->student_name ); ?></div></td>
                        <td><?php echo esc_html( $r->leave_type ); ?></td>
                        <td><?php echo esc_html( $r->from_date ); ?></td>
                        <td><?php echo esc_html( $r->to_date ); ?></td>
                        <td><?php echo esc_html( wp_trim_words( $r->reason, 8 ) ); ?></td>
                        <td><?php echo SSM_Helper::badge( $r->status ); ?></td>
                        <td><div class="ssm-row-actions">
                            <a href="#" style="background:#dcfce7;color:#166534"><span class="dashicons dashicons-yes"></span></a>
                            <a href="#" style="background:#fee2e2;color:#991b1b"><span class="dashicons dashicons-no"></span></a>
                        </div></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="7" class="ssm-muted" style="text-align:center;padding:30px">No leave requests.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
