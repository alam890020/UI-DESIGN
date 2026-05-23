<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT bi.*, b.title AS book_title, CONCAT(s.first_name,' ',s.last_name) AS student FROM {$p}books_issued bi LEFT JOIN {$p}books b ON b.id=bi.book_id LEFT JOIN {$p}students s ON s.id=bi.student_id ORDER BY bi.id DESC LIMIT 100" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Books Issued', 'Track borrowing and returns', 'dashicons-update' ); ?>
    <div class="ssm-card">
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>Book</th><th>Borrower</th><th>Issued</th><th>Due</th><th>Returned</th><th>Status</th></tr></thead>
                <tbody>
                <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                    <tr>
                        <td><strong><?php echo esc_html( $r->book_title ); ?></strong></td>
                        <td><?php echo esc_html( $r->student ); ?></td>
                        <td><?php echo esc_html( $r->issued_at ); ?></td>
                        <td><?php echo esc_html( $r->return_due ); ?></td>
                        <td><?php echo esc_html( $r->returned_at ?: '—' ); ?></td>
                        <td><?php echo SSM_Helper::badge( $r->status ); ?></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="6" class="ssm-muted" style="text-align:center;padding:30px">No books issued yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
