<?php
/**
 * GMA Print View — Renders printable documents.
 * Called via admin.php?page=gma-print&type=XXX&id=YYY
 *
 * @package GMA_School
 */
defined( 'ABSPATH' ) || exit;

$type = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';
$id   = isset( $_GET['id'] )   ? (int) $_GET['id'] : 0;
global $wpdb;

if ( ! $type || ! $id ) {
  echo '<div class="gma-wrap"><div class="gma-notice gma-notice-warning">Please specify a document type and ID.</div></div>';
  return;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>GMA — Print Document</title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
    .print-page { background: #fff; max-width: 800px; margin: 0 auto; padding: 32px; border-radius: 8px; box-shadow: 0 2px 16px rgba(0,0,0,.08); }
    .print-header { text-align: center; border-bottom: 2px solid #4F46E5; padding-bottom: 16px; margin-bottom: 20px; }
    .print-header h1 { color: #4F46E5; margin: 0; font-size: 22px; }
    .print-header p  { margin: 4px 0; color: #666; font-size: 13px; }
    .doc-title { font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #111; text-align: center; margin-bottom: 20px; }
    table.info { width: 100%; border-collapse: collapse; font-size: 13px; }
    table.info td { padding: 7px 10px; border: 1px solid #E5E7EB; }
    table.info td:first-child { font-weight: 600; background: #F8FAFC; width: 35%; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
    .badge-success { background: #D1FAE5; color: #065F46; }
    .badge-danger  { background: #FEE2E2; color: #991B1B; }
    .id-card { width: 320px; border: 2px solid #4F46E5; border-radius: 16px; overflow: hidden; margin: 20px auto; }
    .id-card-header { background: #4F46E5; color: #fff; padding: 16px; text-align: center; }
    .id-card-body   { padding: 20px; text-align: center; }
    .id-card-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #4F46E5; margin: 0 auto 12px; display: block; }
    .id-card-name   { font-size: 18px; font-weight: 800; margin-bottom: 4px; }
    .id-card-sub    { font-size: 12px; color: #666; }
    .no-print { margin-top: 20px; text-align: center; }
    .btn-print { background: #4F46E5; color: #fff; padding: 10px 24px; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; }
    @media print {
      body { background: #fff; padding: 0; }
      .print-page { box-shadow: none; padding: 0; }
      .no-print { display: none; }
    }
  </style>
</head>
<body>
<?php

switch ( $type ) {

  case 'invoice':
    $inv = $wpdb->get_row( $wpdb->prepare(
      "SELECT i.*, CONCAT(sr.first_name,' ',sr.last_name) AS student_name, sr.admission_number, sr.phone AS student_phone, c.label AS class_label, sc.label AS school_name, sc.address AS school_address, sc.phone AS school_phone, sc.logo AS school_logo FROM " . GMA_TABLE_INVOICES . " i INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON i.student_record_id=sr.ID LEFT JOIN " . GMA_TABLE_CLASS_SCHOOL . " cs ON sr.class_school_id=cs.ID LEFT JOIN " . GMA_TABLE_CLASSES . " c ON cs.class_id=c.ID LEFT JOIN " . GMA_TABLE_SCHOOLS . " sc ON i.school_id=sc.ID WHERE i.ID=%d", $id
    ) );
    if ( ! $inv ) { echo '<p>Invoice not found.</p>'; break; }
    $payments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_PAYMENTS . " WHERE invoice_id=%d ORDER BY payment_date", $id ) );
    ?>
    <div class="print-page">
      <div class="print-header">
        <?php if($inv->school_logo): ?><img src="<?php echo esc_url($inv->school_logo); ?>" style="height:50px;margin-bottom:8px;" alt=""><br><?php endif; ?>
        <h1><?php echo esc_html($inv->school_name); ?></h1>
        <p><?php echo esc_html($inv->school_address); ?></p>
      </div>
      <div class="doc-title">FEE INVOICE</div>
      <table class="info">
        <tr><td>Invoice No</td><td><strong><?php echo esc_html($inv->invoice_number); ?></strong></td><td>Date</td><td><?php echo esc_html(GMA_Helper::format_date($inv->created_at)); ?></td></tr>
        <tr><td>Student Name</td><td><?php echo esc_html($inv->student_name); ?></td><td>Admission No</td><td><?php echo esc_html($inv->admission_number); ?></td></tr>
        <tr><td>Class</td><td><?php echo esc_html($inv->class_label ?? '—'); ?></td><td>Due Date</td><td><?php echo esc_html(GMA_Helper::format_date($inv->due_date)); ?></td></tr>
        <tr><td>Total Amount</td><td><strong><?php echo esc_html(GMA_Helper::format_currency($inv->total_amount)); ?></strong></td><td>Status</td><td><?php echo wp_kses_post(GMA_Helper::status_badge($inv->status)); ?></td></tr>
        <tr><td>Paid Amount</td><td style="color:#059669;font-weight:700;"><?php echo esc_html(GMA_Helper::format_currency($inv->paid_amount)); ?></td><td>Due Amount</td><td style="color:#DC2626;font-weight:700;"><?php echo esc_html(GMA_Helper::format_currency($inv->due_amount)); ?></td></tr>
        <?php if($inv->discount): ?><tr><td>Discount</td><td><?php echo esc_html(GMA_Helper::format_currency($inv->discount)); ?></td><td></td><td></td></tr><?php endif; ?>
        <?php if($inv->note): ?><tr><td>Note</td><td colspan="3"><?php echo esc_html($inv->note); ?></td></tr><?php endif; ?>
      </table>
      <?php if($payments): ?>
      <h4 style="margin-top:20px;">Payment History</h4>
      <table class="info">
        <tr style="background:#F8FAFC;"><td><strong>Date</strong></td><td><strong>Amount</strong></td><td><strong>Method</strong></td><td><strong>Transaction ID</strong></td></tr>
        <?php foreach($payments as $p): ?>
        <tr><td><?php echo esc_html(GMA_Helper::format_date($p->payment_date)); ?></td><td><?php echo esc_html(GMA_Helper::format_currency($p->amount)); ?></td><td><?php echo esc_html(ucfirst($p->payment_method)); ?></td><td><?php echo esc_html($p->transaction_id ?: '—'); ?></td></tr>
        <?php endforeach; ?>
      </table>
      <?php endif; ?>
      <div style="margin-top:40px;display:flex;justify-content:space-between;font-size:12px;">
        <div>_________________<br>Student Signature</div>
        <div>_________________<br>Authorized Signature</div>
      </div>
    </div>
    <?php break;

  case 'student_id':
    $sr = $wpdb->get_row( $wpdb->prepare( "SELECT sr.*, c.label AS class_label, sec.label AS section_label, sc.label AS school_name, sc.logo FROM " . GMA_TABLE_STUDENT_RECORDS . " sr LEFT JOIN " . GMA_TABLE_CLASS_SCHOOL . " cs ON sr.class_school_id=cs.ID LEFT JOIN " . GMA_TABLE_CLASSES . " c ON cs.class_id=c.ID LEFT JOIN " . GMA_TABLE_SECTIONS . " sec ON sr.section_id=sec.ID LEFT JOIN " . GMA_TABLE_SCHOOLS . " sc ON sr.school_id=sc.ID WHERE sr.ID=%d", $id ) );
    if ( ! $sr ) { echo '<p>Student not found.</p>'; break; }
    ?>
    <div class="id-card">
      <div class="id-card-header">
        <?php if($sr->logo): ?><img src="<?php echo esc_url($sr->logo); ?>" style="height:32px;margin-bottom:6px;" alt=""><br><?php endif; ?>
        <strong><?php echo esc_html($sr->school_name); ?></strong><br>
        <small>STUDENT ID CARD</small>
      </div>
      <div class="id-card-body">
        <img src="<?php echo $sr->photo ? esc_url($sr->photo) : esc_url(GMA_PLUGIN_URL.'assets/images/default-avatar.png'); ?>" class="id-card-avatar" alt="">
        <div class="id-card-name"><?php echo esc_html($sr->first_name.' '.$sr->last_name); ?></div>
        <div class="id-card-sub"><?php echo esc_html(($sr->class_label ?? '').' '.($sr->section_label ?? '')); ?></div>
        <table class="info" style="margin-top:12px;text-align:left;">
          <tr><td>Admission No</td><td><strong><?php echo esc_html($sr->admission_number); ?></strong></td></tr>
          <tr><td>DOB</td><td><?php echo esc_html(GMA_Helper::format_date($sr->dob)); ?></td></tr>
          <tr><td>Gender</td><td><?php echo esc_html(ucfirst($sr->gender ?? '—')); ?></td></tr>
          <tr><td>Phone</td><td><?php echo esc_html($sr->phone ?: '—'); ?></td></tr>
        </table>
      </div>
    </div>
    <?php break;

  case 'staff_id':
    $st = $wpdb->get_row( $wpdb->prepare( "SELECT s.*, sc.label AS school_name, sc.logo FROM " . GMA_TABLE_STAFF . " s LEFT JOIN " . GMA_TABLE_SCHOOLS . " sc ON s.school_id=sc.ID WHERE s.ID=%d", $id ) );
    if ( ! $st ) { echo '<p>Staff not found.</p>'; break; }
    ?>
    <div class="id-card">
      <div class="id-card-header">
        <strong><?php echo esc_html($st->school_name); ?></strong><br><small>STAFF ID CARD</small>
      </div>
      <div class="id-card-body">
        <img src="<?php echo $st->photo ? esc_url($st->photo) : esc_url(GMA_PLUGIN_URL.'assets/images/default-avatar.png'); ?>" class="id-card-avatar" alt="">
        <div class="id-card-name"><?php echo esc_html($st->first_name.' '.$st->last_name); ?></div>
        <div class="id-card-sub"><?php echo esc_html($st->designation ?: 'Staff'); ?></div>
        <table class="info" style="margin-top:12px;text-align:left;">
          <tr><td>Phone</td><td><?php echo esc_html($st->phone ?: '—'); ?></td></tr>
          <tr><td>Email</td><td><?php echo esc_html($st->email ?: '—'); ?></td></tr>
          <tr><td>Joined</td><td><?php echo esc_html(GMA_Helper::format_date($st->joining_date)); ?></td></tr>
        </table>
      </div>
    </div>
    <?php break;

  default:
    echo '<div class="print-page"><p style="color:#999;text-align:center;">Print document type not found.</p></div>';
}
?>
<div class="no-print"><button class="btn-print" onclick="window.print()">🖨️ Print this Page</button></div>
</body>
</html>
