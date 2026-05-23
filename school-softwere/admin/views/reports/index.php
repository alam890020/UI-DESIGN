<?php defined( 'ABSPATH' ) || exit;
global $wpdb;
$school_id  = GMA_Helper::get_current_school_id();
$session_id = GMA_Helper::get_active_session_id($school_id);
$from = isset($_GET['from']) ? sanitize_text_field($_GET['from']) : date('Y-m-01');
$to   = isset($_GET['to'])   ? sanitize_text_field($_GET['to'])   : date('Y-m-t');

$total_income   = (float)$wpdb->get_var($wpdb->prepare("SELECT COALESCE(SUM(amount),0) FROM " . GMA_TABLE_INCOME . " WHERE school_id=%d AND date BETWEEN %s AND %s", $school_id, $from, $to));
$total_expenses = (float)$wpdb->get_var($wpdb->prepare("SELECT COALESCE(SUM(amount),0) FROM " . GMA_TABLE_EXPENSES . " WHERE school_id=%d AND date BETWEEN %s AND %s", $school_id, $from, $to));
$total_fees_col = (float)$wpdb->get_var($wpdb->prepare("SELECT COALESCE(SUM(p.amount),0) FROM " . GMA_TABLE_PAYMENTS . " p INNER JOIN " . GMA_TABLE_INVOICES . " i ON p.invoice_id=i.ID WHERE i.school_id=%d AND p.payment_date BETWEEN %s AND %s", $school_id, $from, $to));
$total_due      = (float)$wpdb->get_var($wpdb->prepare("SELECT COALESCE(SUM(due_amount),0) FROM " . GMA_TABLE_INVOICES . " WHERE school_id=%d AND status!='paid'", $school_id));
$att_rate       = $wpdb->get_var($wpdb->prepare("SELECT ROUND(COUNT(CASE WHEN status='present' THEN 1 END)*100.0/NULLIF(COUNT(*),0),1) FROM " . GMA_TABLE_ATTENDANCE . " a INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON a.student_record_id=sr.ID WHERE sr.school_id=%d AND a.date BETWEEN %s AND %s", $school_id, $from, $to));
?>
<div class="gma-wrap">
  <?php include GMA_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
  <div class="gma-page-content">
    <div class="gma-page-title-row">
      <div><h1 class="gma-page-title">📊 Reports</h1><p class="gma-breadcrumb"><a href="<?php echo esc_url(admin_url('admin.php?page=gma-school')); ?>">Dashboard</a> › Reports</p></div>
    </div>
    <div class="gma-card" style="margin-bottom:20px;">
      <form method="get" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        <input type="hidden" name="page" value="gma-reports">
        <div class="gma-form-group"><label>From</label><input type="text" name="from" value="<?php echo esc_attr($from); ?>" class="gma-datepicker"></div>
        <div class="gma-form-group"><label>To</label><input type="text" name="to" value="<?php echo esc_attr($to); ?>" class="gma-datepicker"></div>
        <button type="submit" class="gma-btn gma-btn-primary">📊 Generate Report</button>
      </form>
    </div>
    <div class="gma-stats-grid">
      <div class="gma-stat-card success"><div class="gma-stat-icon">📥</div><div><div class="gma-stat-number"><?php echo GMA_Helper::format_currency($total_income); ?></div><div class="gma-stat-label">Total Income</div></div></div>
      <div class="gma-stat-card danger"><div class="gma-stat-icon">📤</div><div><div class="gma-stat-number"><?php echo GMA_Helper::format_currency($total_expenses); ?></div><div class="gma-stat-label">Total Expenses</div></div></div>
      <div class="gma-stat-card warning"><div class="gma-stat-icon">💰</div><div><div class="gma-stat-number"><?php echo GMA_Helper::format_currency($total_fees_col); ?></div><div class="gma-stat-label">Fee Collected</div></div></div>
      <div class="gma-stat-card info"><div class="gma-stat-icon">⚠️</div><div><div class="gma-stat-number"><?php echo GMA_Helper::format_currency($total_due); ?></div><div class="gma-stat-label">Fee Due</div></div></div>
      <div class="gma-stat-card"><div class="gma-stat-icon">✅</div><div><div class="gma-stat-number"><?php echo esc_html($att_rate ?: 0); ?>%</div><div class="gma-stat-label">Attendance Rate</div><div class="gma-stat-trend up">↑ Period</div></div></div>
      <div class="gma-stat-card success"><div class="gma-stat-icon">📈</div><div><div class="gma-stat-number"><?php echo GMA_Helper::format_currency(max(0,$total_income+$total_fees_col-$total_expenses)); ?></div><div class="gma-stat-label">Net Surplus</div></div></div>
    </div>
    <div class="gma-card">
      <h4 class="gma-card-title">🔗 Quick Report Links</h4>
      <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:12px;">
        <?php
        $links = array(
          array('📋','Student Report',     admin_url('admin.php?page=gma-students')),
          array('👩‍🏫','Staff Report',      admin_url('admin.php?page=gma-staff')),
          array('💰','Fee Collection',    admin_url('admin.php?page=gma-payments')),
          array('🧾','Invoice Report',    admin_url('admin.php?page=gma-invoices')),
          array('✅','Attendance Report', admin_url('admin.php?page=gma-attendance')),
          array('📈','Academic Report',   admin_url('admin.php?page=gma-academic-report')),
          array('🚌','Transport Report',  admin_url('admin.php?page=gma-transport')),
          array('📚','Library Report',    admin_url('admin.php?page=gma-library')),
        );
        foreach($links as $l): ?>
        <a href="<?php echo esc_url($l[2]); ?>" class="gma-btn gma-btn-secondary"><?php echo $l[0].' '.esc_html($l[1]); ?></a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
