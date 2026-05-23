<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT l.*, CONCAT(s.first_name,' ',s.last_name) AS staff_name FROM {$p}staff_leaves l LEFT JOIN {$p}staff s ON s.id=l.staff_id ORDER BY l.id DESC LIMIT 100" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Staff Leaves & Leave Requests', 'Review and approve staff leave applications', 'dashicons-clipboard' ); ?>

    <div class="ssm-card">
        <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Leave Requests</div></div>
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>Staff</th><th>Type</th><th>From</th><th>To</th><th>Reason</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                    <tr>
                        <td><div class="ssm-row-name"><?php echo SSM_Helper::avatar( $r->staff_name ); ?><?php echo esc_html( $r->staff_name ); ?></div></td>
                        <td><?php echo esc_html( $r->leave_type ); ?></td>
                        <td><?php echo esc_html( $r->from_date ); ?></td>
                        <td><?php echo esc_html( $r->to_date ); ?></td>
                        <td><?php echo esc_html( wp_trim_words( $r->reason, 8 ) ); ?></td>
                        <td><?php echo SSM_Helper::badge( $r->status ); ?></td>
                        <td><div class="ssm-row-actions">
                            <a href="#" title="Approve" style="background:#dcfce7;color:#166534"><span class="dashicons dashicons-yes"></span></a>
                            <a href="#" title="Reject" style="background:#fee2e2;color:#991b1b"><span class="dashicons dashicons-no"></span></a>
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
