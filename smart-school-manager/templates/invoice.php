<?php
/**
 * Print template: Invoice.
 *
 * @package SmartSchoolManager
 *
 * Variables in scope:
 *   $invoice (object)   row from {$wpdb->prefix}ssm_invoices
 *   $student (object)   row from {$wpdb->prefix}ssm_students
 *   $payments (array)   rows from {$wpdb->prefix}ssm_payments
 *   $school_name, $theme_color
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$opts = get_option( 'ssm_settings', array() );
$addr = isset( $opts['address'] ) ? $opts['address'] : '';
$phone = isset( $opts['phone'] ) ? $opts['phone'] : '';
$email = isset( $opts['email'] ) ? $opts['email'] : '';
?>
<div class="ssm-doc">
    <header class="ssm-doc-head">
        <div>
            <h1><?php echo esc_html( $school_name ); ?></h1>
            <small><?php echo esc_html( $addr ); ?> &middot; <?php echo esc_html( $phone ); ?> &middot; <?php echo esc_html( $email ); ?></small>
        </div>
        <div class="ssm-doc-meta">
            <h2>INVOICE</h2>
            <strong>#<?php echo esc_html( $invoice ? $invoice->invoice_no : 'N/A' ); ?></strong><br>
            <small>Issued: <?php echo esc_html( $invoice ? $invoice->created_at : '' ); ?></small><br>
            <small>Due: <?php echo esc_html( $invoice ? $invoice->due_date : '' ); ?></small>
        </div>
    </header>

    <section class="ssm-doc-grid">
        <div>
            <h3>Bill To</h3>
            <strong><?php echo esc_html( trim( ( $student->first_name ?? '' ) . ' ' . ( $student->last_name ?? '' ) ) ); ?></strong><br>
            <small>Adm No: <?php echo esc_html( $student->admission_no ?? '' ); ?></small><br>
            <small><?php echo esc_html( $student->phone ?? '' ); ?></small>
        </div>
        <div>
            <h3>Status</h3>
            <span class="ssm-doc-pill ssm-pill-<?php echo esc_attr( $invoice->status ?? 'pending' ); ?>">
                <?php echo esc_html( ucfirst( $invoice->status ?? 'pending' ) ); ?>
            </span>
        </div>
    </section>

    <table class="ssm-doc-table">
        <thead><tr><th>Description</th><th class="r">Amount</th></tr></thead>
        <tbody>
            <tr><td>Tuition / Fees</td><td class="r"><?php echo esc_html( SSM_Helper::money( $invoice->amount ?? 0 ) ); ?></td></tr>
            <tr><td>Paid</td><td class="r">- <?php echo esc_html( SSM_Helper::money( $invoice->paid ?? 0 ) ); ?></td></tr>
            <tr class="total"><td><strong>Balance Due</strong></td><td class="r"><strong><?php echo esc_html( SSM_Helper::money( ( $invoice->amount ?? 0 ) - ( $invoice->paid ?? 0 ) ) ); ?></strong></td></tr>
        </tbody>
    </table>

    <?php if ( ! empty( $payments ) ) : ?>
        <h3>Payment History</h3>
        <table class="ssm-doc-table">
            <thead><tr><th>Date</th><th>Method</th><th>Reference</th><th class="r">Amount</th></tr></thead>
            <tbody>
            <?php foreach ( $payments as $p ) : ?>
                <tr>
                    <td><?php echo esc_html( $p->paid_at ); ?></td>
                    <td><?php echo esc_html( $p->method ); ?></td>
                    <td><?php echo esc_html( $p->ref_no ); ?></td>
                    <td class="r"><?php echo esc_html( SSM_Helper::money( $p->amount ) ); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <footer class="ssm-doc-foot">
        <small>Thank you for your prompt payment. This is a computer-generated invoice and does not require a signature.</small>
    </footer>
</div>
