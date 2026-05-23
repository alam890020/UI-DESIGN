<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$opts = get_option( 'sms_settings', array() );
?>
<div class="sms-doc">
    <header class="sms-doc-head">
        <div>
            <h1><?php echo esc_html( $school_name ); ?></h1>
            <small><?php echo esc_html( $opts['address'] ?? '' ); ?> · <?php echo esc_html( $opts['phone'] ?? '' ); ?> · <?php echo esc_html( $opts['email'] ?? '' ); ?></small>
        </div>
        <div class="sms-doc-meta">
            <h2>INVOICE</h2>
            <strong>#<?php echo esc_html( $invoice ? $invoice->invoice_no : 'N/A' ); ?></strong><br>
            <small>Issued: <?php echo esc_html( $invoice ? $invoice->created_at : '' ); ?></small><br>
            <small>Due: <?php echo esc_html( $invoice ? $invoice->due_date : '' ); ?></small>
        </div>
    </header>

    <section class="sms-doc-grid">
        <div>
            <h3>Bill To</h3>
            <strong><?php echo esc_html( trim( ( $student->first_name ?? '' ) . ' ' . ( $student->last_name ?? '' ) ) ); ?></strong><br>
            <small>Adm No: <?php echo esc_html( $student->admission_no ?? '' ); ?></small><br>
            <small><?php echo esc_html( $student->phone ?? '' ); ?></small>
        </div>
        <div>
            <h3>Status</h3>
            <span class="sms-doc-pill"><?php echo esc_html( ucfirst( $invoice->status ?? 'pending' ) ); ?></span>
        </div>
    </section>

    <table class="sms-doc-table">
        <thead><tr><th>Description</th><th class="r">Amount</th></tr></thead>
        <tbody>
            <tr><td>Tuition / Fees</td><td class="r"><?php echo esc_html( SMS_Helper::money( $invoice->amount ?? 0 ) ); ?></td></tr>
            <tr><td>Paid</td><td class="r">- <?php echo esc_html( SMS_Helper::money( $invoice->paid ?? 0 ) ); ?></td></tr>
            <tr class="total"><td><strong>Balance Due</strong></td><td class="r"><strong><?php echo esc_html( SMS_Helper::money( ( $invoice->amount ?? 0 ) - ( $invoice->paid ?? 0 ) ) ); ?></strong></td></tr>
        </tbody>
    </table>

    <?php if ( ! empty( $payments ) ) : ?>
        <h3>Payment History</h3>
        <table class="sms-doc-table">
            <thead><tr><th>Date</th><th>Method</th><th>Reference</th><th class="r">Amount</th></tr></thead>
            <tbody>
            <?php foreach ( $payments as $pp ) : ?>
                <tr>
                    <td><?php echo esc_html( $pp->paid_at ); ?></td>
                    <td><?php echo esc_html( $pp->method ); ?></td>
                    <td><?php echo esc_html( $pp->ref_no ); ?></td>
                    <td class="r"><?php echo esc_html( SMS_Helper::money( $pp->amount ) ); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <footer class="sms-doc-foot">
        <small>Thank you for your prompt payment. This is a computer-generated invoice.</small>
    </footer>
</div>
