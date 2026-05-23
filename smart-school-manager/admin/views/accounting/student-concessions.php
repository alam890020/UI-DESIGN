<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT sc.*, CONCAT(s.first_name,' ',s.last_name) AS student, c.name AS concession FROM {$p}student_concessions sc LEFT JOIN {$p}students s ON s.id=sc.student_id LEFT JOIN {$p}concession_types c ON c.id=sc.concession_type_id ORDER BY sc.id DESC LIMIT 100" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Students Concession', 'Apply concessions to specific students', 'dashicons-buddicons-friends' ); ?>
    <div class="ssm-card">
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>Student</th><th>Concession</th><th>Note</th></tr></thead>
                <tbody>
                <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                    <tr>
                        <td><?php echo esc_html( $r->student ); ?></td>
                        <td><span class="ssm-badge ssm-badge-success"><?php echo esc_html( $r->concession ); ?></span></td>
                        <td><?php echo esc_html( $r->note ); ?></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="3" class="ssm-muted" style="text-align:center;padding:30px">No concessions applied yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
