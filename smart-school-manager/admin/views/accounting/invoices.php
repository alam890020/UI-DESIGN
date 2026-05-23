<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT i.*, CONCAT(s.first_name,' ',s.last_name) AS student FROM {$p}invoices i LEFT JOIN {$p}students s ON s.id=i.student_id ORDER BY i.id DESC LIMIT 100" );
$total = (float) $wpdb->get_var( "SELECT COALESCE(SUM(amount),0) FROM {$p}invoices" );
$paid = (float) $wpdb->get_var( "SELECT COALESCE(SUM(paid),0) FROM {$p}invoices" );
$due = $total - $paid;
$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}invoices" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Invoices', 'Generate and manage student invoices', 'dashicons-media-document' ); ?>
    <div class="ssm-stats-grid">
        <?php
        echo SSM_Helper::stat_card( 'Total Invoiced', SSM_Helper::money( $total ), 'dashicons-media-document', 'indigo' );
        echo SSM_Helper::stat_card( 'Collected', SSM_Helper::money( $paid ), 'dashicons-yes-alt', 'green' );
        echo SSM_Helper::stat_card( 'Outstanding', SSM_Helper::money( $due ), 'dashicons-warning', 'red' );
        echo SSM_Helper::stat_card( 'Invoices', number_format( $count ), 'dashicons-list-view', 'cyan' );
        ?>
    </div>
    <div class="ssm-card">
        <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Recent Invoices</div></div>
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>Invoice #</th><th>Student</th><th>Amount</th><th>Paid</th><th>Due Date</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                    <tr>
                        <td><strong>#<?php echo esc_html( $r->invoice_no ); ?></strong></td>
                        <td><?php echo esc_html( $r->student ); ?></td>
                        <td><?php echo SSM_Helper::money( $r->amount ); ?></td>
                        <td><?php echo SSM_Helper::money( $r->paid ); ?></td>
                        <td><?php echo esc_html( $r->due_date ); ?></td>
                        <td><?php echo SSM_Helper::badge( $r->status ); ?></td>
                        <td><div class="ssm-row-actions">
                            <a href="#"><span class="dashicons dashicons-printer"></span></a>
                            <a href="#"><span class="dashicons dashicons-money-alt"></span></a>
                        </div></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="7" class="ssm-muted" style="text-align:center;padding:30px">No invoices yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
