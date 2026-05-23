<?php
/**
 * Print Invoice — standalone (no WP wrapper).
 *
 * @package School_Softwere
 */

// Bootstrap WordPress
$wp_root = dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/wp-load.php';
if ( ! file_exists( $wp_root ) ) {
	$wp_root = dirname( dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) ) . '/wp-load.php';
}
if ( file_exists( $wp_root ) ) {
	require_once $wp_root;
} else {
	die( 'WordPress not found.' );
}

if ( ! is_user_logged_in() ) wp_die( esc_html__( 'Please login.', 'school-softwere' ) );

$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
if ( ! $id ) wp_die( esc_html__( 'Invalid invoice.', 'school-softwere' ) );

global $wpdb;
$invoice = $wpdb->get_row( $wpdb->prepare(
	"SELECT i.*, CONCAT(sr.first_name,' ',sr.last_name) AS student_name, sr.admission_number, sr.phone AS student_phone, sr.email AS student_email
	 FROM {$wpdb->prefix}ss_invoices i
	 INNER JOIN {$wpdb->prefix}ss_student_records sr ON i.student_record_id=sr.ID
	 WHERE i.ID=%d",
	$id
) );
if ( ! $invoice ) wp_die( esc_html__( 'Invoice not found.', 'school-softwere' ) );

$school    = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_schools WHERE ID=%d", $invoice->school_id ) );
$payments  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_payments WHERE invoice_id=%d ORDER BY payment_date", $id ) );
$fee_items = $wpdb->get_results( $wpdb->prepare( "SELECT sf.*, f.label AS fee_label FROM {$wpdb->prefix}ss_student_fees sf INNER JOIN {$wpdb->prefix}ss_fees f ON sf.fee_id=f.ID WHERE sf.student_record_id=%d", $invoice->student_record_id ) );
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo esc_html( __( 'Invoice', 'school-softwere' ) . ' — ' . $invoice->invoice_number ); ?></title>
<style>
* { box-sizing:border-box; margin:0; padding:0; }
body { font-family:'Segoe UI',Arial,sans-serif; font-size:13px; color:#1E293B; background:#fff; }
.inv-wrap { max-width:680px; margin:20px auto; padding:32px; border:1px solid #E0E7FF; }
.inv-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:28px; border-bottom:2px solid #4F46E5; padding-bottom:16px; }
.inv-logo { font-size:22px; font-weight:800; color:#4F46E5; }
.inv-school-info p, .inv-school-info small { font-size:12px; color:#64748B; display:block; }
.inv-title { font-size:26px; font-weight:800; color:#1E1B4B; text-align:right; }
.inv-subtitle { font-size:12px; color:#64748B; text-align:right; }
.inv-meta { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px; }
.inv-section h4 { font-size:11px; text-transform:uppercase; letter-spacing:.05em; color:#64748B; font-weight:700; margin-bottom:8px; border-bottom:1px solid #E0E7FF; padding-bottom:4px; }
.inv-section p { font-size:13px; color:#1E293B; line-height:1.6; }
table { width:100%; border-collapse:collapse; margin-bottom:20px; }
th { background:#F8FAFF; padding:10px 12px; text-align:left; font-size:12px; font-weight:700; text-transform:uppercase; color:#64748B; border-bottom:2px solid #E0E7FF; }
td { padding:10px 12px; border-bottom:1px solid #F1F5F9; font-size:13px; }
.inv-totals { text-align:right; border-top:2px solid #4F46E5; padding-top:14px; }
.inv-totals-row { display:flex; justify-content:flex-end; gap:60px; margin-bottom:6px; }
.inv-totals-label { font-size:13px; color:#64748B; }
.inv-totals-value { font-size:13px; font-weight:600; color:#1E293B; min-width:80px; text-align:right; }
.inv-totals-row.grand .inv-totals-label, .inv-totals-row.grand .inv-totals-value { font-size:16px; font-weight:800; color:#4F46E5; }
.status-paid    { background:#D1FAE5; color:#065F46; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:700; }
.status-partial { background:#FEF3C7; color:#92400E; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:700; }
.status-unpaid  { background:#FEE2E2; color:#991B1B; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:700; }
.inv-footer { margin-top:28px; border-top:1px solid #E0E7FF; padding-top:14px; font-size:11px; color:#94A3B8; text-align:center; }
.no-print { text-align:center; margin-bottom:20px; }
.print-btn { background:#4F46E5; color:#fff; border:none; padding:10px 28px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; }
@media print { .no-print { display:none; } body { margin:0; } .inv-wrap { border:none; margin:0; } }
</style>
</head>
<body>
<div class="no-print"><button class="print-btn" onclick="window.print()">🖨️ <?php esc_html_e( 'Print Invoice', 'school-softwere' ); ?></button></div>
<div class="inv-wrap">
	<div class="inv-header">
		<div class="inv-school-info">
			<?php if ( $school && $school->logo ) : ?><img src="<?php echo esc_url( $school->logo ); ?>" style="height:48px;margin-bottom:6px;" alt=""><?php else : ?>
			<div class="inv-logo">🎓 <?php echo esc_html( $school ? $school->label : get_option( 'blogname' ) ); ?></div><?php endif; ?>
			<?php if ( $school ) : ?>
			<p><?php echo esc_html( $school->address ); ?></p>
			<small>📞 <?php echo esc_html( $school->phone ); ?> | ✉️ <?php echo esc_html( $school->email ); ?></small>
			<?php endif; ?>
		</div>
		<div>
			<div class="inv-title"><?php esc_html_e( 'INVOICE', 'school-softwere' ); ?></div>
			<div class="inv-subtitle"><?php echo esc_html( $invoice->invoice_number ); ?></div>
			<div style="margin-top:8px;"><span class="status-<?php echo esc_attr( $invoice->status ); ?>"><?php echo esc_html( ucfirst( $invoice->status ) ); ?></span></div>
		</div>
	</div>

	<div class="inv-meta">
		<div class="inv-section">
			<h4><?php esc_html_e( 'Bill To', 'school-softwere' ); ?></h4>
			<p><strong><?php echo esc_html( $invoice->student_name ); ?></strong></p>
			<p><?php esc_html_e( 'Adm. No:', 'school-softwere' ); ?> <?php echo esc_html( $invoice->admission_number ); ?></p>
			<p><?php echo esc_html( $invoice->student_phone ?: '' ); ?></p>
		</div>
		<div class="inv-section">
			<h4><?php esc_html_e( 'Invoice Details', 'school-softwere' ); ?></h4>
			<p><?php esc_html_e( 'Invoice Date:', 'school-softwere' ); ?> <?php echo esc_html( SS_Helper::format_date( $invoice->created_at ) ); ?></p>
			<p><?php esc_html_e( 'Due Date:', 'school-softwere' ); ?> <?php echo esc_html( SS_Helper::format_date( $invoice->due_date ) ); ?></p>
			<p><?php esc_html_e( 'Invoice #:', 'school-softwere' ); ?> <?php echo esc_html( $invoice->invoice_number ); ?></p>
		</div>
	</div>

	<table>
		<thead><tr><th>#</th><th><?php esc_html_e( 'Description', 'school-softwere' ); ?></th><th style="text-align:right;"><?php esc_html_e( 'Amount', 'school-softwere' ); ?></th></tr></thead>
		<tbody>
		<?php if ( $fee_items ) : $n=1; foreach ( $fee_items as $fi ) : ?>
		<tr><td><?php echo esc_html( $n++ ); ?></td><td><?php echo esc_html( $fi->fee_label ); ?></td><td style="text-align:right;"><?php echo esc_html( SS_Helper::format_currency( $fi->amount ) ); ?></td></tr>
		<?php endforeach; else : ?><tr><td colspan="3" style="text-align:center;color:#94A3B8;"><?php esc_html_e( 'Fee details not available.', 'school-softwere' ); ?></td></tr><?php endif; ?>
		</tbody>
	</table>

	<div class="inv-totals">
		<div class="inv-totals-row"><span class="inv-totals-label"><?php esc_html_e( 'Subtotal', 'school-softwere' ); ?></span><span class="inv-totals-value"><?php echo esc_html( SS_Helper::format_currency( $invoice->total_amount ) ); ?></span></div>
		<div class="inv-totals-row"><span class="inv-totals-label"><?php esc_html_e( 'Amount Paid', 'school-softwere' ); ?></span><span class="inv-totals-value" style="color:#10B981;"><?php echo esc_html( SS_Helper::format_currency( $invoice->paid_amount ) ); ?></span></div>
		<div class="inv-totals-row grand"><span class="inv-totals-label"><?php esc_html_e( 'Balance Due', 'school-softwere' ); ?></span><span class="inv-totals-value"><?php echo esc_html( SS_Helper::format_currency( $invoice->due_amount ) ); ?></span></div>
	</div>

	<?php if ( $payments ) : ?>
	<div style="margin-top:24px;">
		<h4 style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#64748B;margin-bottom:10px;"><?php esc_html_e( 'Payment History', 'school-softwere' ); ?></h4>
		<table>
			<thead><tr><th><?php esc_html_e( 'Date', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Method', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Amount', 'school-softwere' ); ?></th></tr></thead>
			<tbody><?php foreach ( $payments as $p ) : ?><tr><td><?php echo esc_html( SS_Helper::format_date( $p->payment_date ) ); ?></td><td><?php echo esc_html( ucfirst( $p->payment_method ) ); ?></td><td><?php echo esc_html( SS_Helper::format_currency( $p->amount ) ); ?></td></tr><?php endforeach; ?></tbody>
		</table>
	</div>
	<?php endif; ?>

	<div class="inv-footer">
		<p><?php echo esc_html( sprintf( __( 'Generated by School Softwere — %s', 'school-softwere' ), get_option( 'blogname' ) ) ); ?></p>
		<p style="margin-top:4px;"><?php esc_html_e( 'Thank you for your payment!', 'school-softwere' ); ?></p>
	</div>
</div>
</body>
</html>
<?php die(); ?>
