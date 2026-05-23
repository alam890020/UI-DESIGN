<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT r.*, CONCAT(s.first_name,' ',s.last_name) AS student_name FROM {$p}ratings r LEFT JOIN {$p}students s ON s.id=r.student_id ORDER BY r.id DESC LIMIT 50" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Rating', 'Performance and behaviour ratings', 'dashicons-star-filled' ); ?>
    <div class="ssm-card">
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>Student / Class</th><th>Score</th><th>Stars</th><th>Remarks</th><th>Date</th></tr></thead>
                <tbody>
                <?php if ( $rows ) : foreach ( $rows as $r ) : $s = (int) round( $r->score / 2 ); ?>
                    <tr>
                        <td><strong><?php echo esc_html( $r->student_name ); ?></strong></td>
                        <td><?php echo esc_html( number_format( $r->score, 1 ) ); ?></td>
                        <td><?php for ( $i = 0; $i < 5; $i++ ) : ?>
                            <span class="dashicons dashicons-star-<?php echo $i < $s ? 'filled' : 'empty'; ?>" style="color:#f59e0b"></span>
                        <?php endfor; ?></td>
                        <td><?php echo esc_html( $r->remarks ); ?></td>
                        <td><?php echo esc_html( $r->rated_at ); ?></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="5" class="ssm-muted" style="text-align:center;padding:30px">No ratings yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
