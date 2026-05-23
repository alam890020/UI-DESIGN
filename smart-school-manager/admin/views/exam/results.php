<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT r.*, CONCAT(s.first_name,' ',s.last_name) AS student, e.name AS exam_name, sub.name AS subject FROM {$p}exam_results r LEFT JOIN {$p}students s ON s.id=r.student_id LEFT JOIN {$p}exams e ON e.id=r.exam_id LEFT JOIN {$p}subjects sub ON sub.id=r.subject_id ORDER BY r.id DESC LIMIT 50" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Exam Results', 'Enter, manage and view results', 'dashicons-chart-line',
        array( array( 'href' => SSM_Helper::admin_url('ssm-bulk-print-results'), 'label' => 'Bulk Print', 'icon' => 'dashicons-printer', 'class' => 'ssm-btn-ghost' ) ) ); ?>

    <div class="ssm-card">
        <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Recent Results</div></div>
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>Student</th><th>Exam</th><th>Subject</th><th>Marks</th><th>Grade</th></tr></thead>
                <tbody>
                <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                    <tr>
                        <td><div class="ssm-row-name"><?php echo SSM_Helper::avatar( $r->student ); ?><?php echo esc_html( $r->student ); ?></div></td>
                        <td><?php echo esc_html( $r->exam_name ); ?></td>
                        <td><?php echo esc_html( $r->subject ); ?></td>
                        <td><strong><?php echo esc_html( $r->marks ); ?></strong></td>
                        <td><span class="ssm-badge ssm-badge-success"><?php echo esc_html( $r->grade ); ?></span></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="5" class="ssm-muted" style="text-align:center;padding:30px">No results yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
