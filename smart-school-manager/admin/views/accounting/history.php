<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT pay.*, CONCAT(s.first_name,' ',s.last_name) AS student FROM {$p}payments pay LEFT JOIN {$p}students s ON s.id=pay.student_id ORDER BY pay.id DESC LIMIT 100" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Payment History', 'All transactions across the school', 'dashicons-backup' ); ?>
    <div class="ssm-card">
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>When</th><th>Student</th><th>Amount</th><th>Method</th><th>Ref</th></tr></thead>
                <tbody>
                <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                    <tr>
                        <td><?php echo esc_html( $r->paid_at ); ?></td>
                        <td><?php echo esc_html( $r->student ); ?></td>
                        <td><strong><?php echo SSM_Helper::money( $r->amount ); ?></strong></td>
                        <td><span class="ssm-badge ssm-badge-info"><?php echo esc_html( $r->method ); ?></span></td>
                        <td><code><?php echo esc_html( $r->ref_no ); ?></code></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="5" class="ssm-muted" style="text-align:center;padding:30px">No transactions.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
