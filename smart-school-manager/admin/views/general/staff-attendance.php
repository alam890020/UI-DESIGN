<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$staff = $wpdb->get_results( "SELECT * FROM {$p}staff WHERE status='active' LIMIT 50" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Staff Attendance', 'Mark and review staff attendance daily', 'dashicons-yes-alt' ); ?>

    <div class="ssm-card">
        <div class="ssm-card-header">
            <div class="ssm-card-title"><span class="dashicons dashicons-calendar"></span> Today &mdash; <?php echo esc_html( date( 'd M Y' ) ); ?></div>
            <input type="date" class="ssm-input" style="max-width:180px" value="<?php echo esc_attr( date('Y-m-d') ); ?>">
        </div>
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>Staff</th><th>Designation</th><th>Status</th><th>In</th><th>Out</th><th></th></tr></thead>
                <tbody>
                <?php if ( $staff ) : foreach ( $staff as $s ) : $name = trim( $s->first_name . ' ' . $s->last_name ); ?>
                    <tr>
                        <td><div class="ssm-row-name"><?php echo SSM_Helper::avatar( $name ); ?><?php echo esc_html( $name ); ?></div></td>
                        <td><?php echo esc_html( $s->designation ); ?></td>
                        <td>
                            <select class="ssm-select"><option>Present</option><option>Absent</option><option>Leave</option><option>Late</option></select>
                        </td>
                        <td><input class="ssm-input" type="time" value="09:00"></td>
                        <td><input class="ssm-input" type="time" value="17:00"></td>
                        <td><button class="ssm-btn ssm-btn-primary ssm-btn-sm">Save</button></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="6" class="ssm-muted" style="text-align:center;padding:30px">No active staff.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
