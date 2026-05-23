<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes" );
$students = $wpdb->get_results( "SELECT * FROM {$p}students WHERE status='active' LIMIT 60" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Student Attendance', 'Mark daily attendance per class', 'dashicons-yes-alt' ); ?>

    <div class="ssm-card">
        <div class="ssm-card-header">
            <div class="ssm-card-title"><span class="dashicons dashicons-calendar"></span> Attendance — <?php echo esc_html( date('d M Y') ); ?></div>
            <div class="ssm-toolbar-filters">
                <select class="ssm-select" style="min-width:160px"><option>Select Class</option>
                    <?php foreach ( $classes as $c ) : ?><option><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?>
                </select>
                <input class="ssm-input" type="date" value="<?php echo esc_attr( date('Y-m-d') ); ?>">
                <button class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-saved"></span> Mark All Present</button>
            </div>
        </div>
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>Roll</th><th>Student</th><th>Adm No</th><th>Status</th><th>Note</th></tr></thead>
                <tbody>
                <?php if ( $students ) : foreach ( $students as $s ) : $name = trim( $s->first_name . ' ' . $s->last_name ); ?>
                    <tr>
                        <td><?php echo esc_html( $s->roll_no ); ?></td>
                        <td><div class="ssm-row-name"><?php echo SSM_Helper::avatar( $name ); ?><?php echo esc_html( $name ); ?></div></td>
                        <td><?php echo esc_html( $s->admission_no ); ?></td>
                        <td>
                            <select class="ssm-select"><option>Present</option><option>Absent</option><option>Late</option><option>Leave</option></select>
                        </td>
                        <td><input class="ssm-input" placeholder="optional"></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="5" class="ssm-muted" style="text-align:center;padding:30px">No active students.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top:14px;text-align:right"><button class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-saved"></span> Save Attendance</button></div>
    </div>
</div>
