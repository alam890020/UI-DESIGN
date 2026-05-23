<?php defined( 'ABSPATH' ) || exit;
global $wpdb;
$school_id = GMA_Helper::get_current_school_id();
$from = isset($_GET['from']) ? sanitize_text_field($_GET['from']) : date('Y-m-01');
$to   = isset($_GET['to'])   ? sanitize_text_field($_GET['to'])   : date('Y-m-t');

$fee_collected = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(p.amount),0) FROM " . GMA_TABLE_PAYMENTS . " p INNER JOIN " . GMA_TABLE_INVOICES . " i ON p.invoice_id=i.ID WHERE i.school_id=%d AND p.payment_date BETWEEN %s AND %s", $school_id, $from, $to ) );
$fee_due       = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(due_amount),0) FROM " . GMA_TABLE_INVOICES . " WHERE school_id=%d AND status!='paid'", $school_id ) );
$total_income  = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(amount),0) FROM " . GMA_TABLE_INCOME . " WHERE school_id=%d AND date BETWEEN %s AND %s", $school_id, $from, $to ) );
$total_expense = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(amount),0) FROM " . GMA_TABLE_EXPENSES . " WHERE school_id=%d AND date BETWEEN %s AND %s", $school_id, $from, $to ) );
$net           = $fee_collected + $total_income - $total_expense;

$monthly_fees  = $wpdb->get_results( $wpdb->prepare( "SELECT DATE_FORMAT(p.payment_date,'%%b %%Y') AS month, MONTH(p.payment_date) AS mo, YEAR(p.payment_date) AS yr, SUM(p.amount) AS total FROM " . GMA_TABLE_PAYMENTS . " p INNER JOIN " . GMA_TABLE_INVOICES . " i ON p.invoice_id=i.ID WHERE i.school_id=%d AND p.payment_date BETWEEN %s AND %s GROUP BY yr,mo ORDER BY yr,mo", $school_id, $from, $to ) );
$exp_cats      = $wpdb->get_results( $wpdb->prepare( "SELECT ec.label, SUM(e.amount) AS total FROM " . GMA_TABLE_EXPENSES . " e LEFT JOIN " . GMA_TABLE_EXPENSE_CATEGORIES . " ec ON e.expense_category_id=ec.ID WHERE e.school_id=%d AND e.date BETWEEN %s AND %s GROUP BY ec.label ORDER BY total DESC", $school_id, $from, $to ) );
?>
<div class="gma-wrap">
  <?php include GMA_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
  <div class="gma-page-content">
    <div class="gma-page-title-row">
      <div><h1 class="gma-page-title">📊 Finance Reports</h1><p class="gma-breadcrumb"><a href="<?php echo esc_url(admin_url('admin.php?page=gma-school')); ?>">Dashboard</a> › Finance Reports</p></div>
      <button class="gma-btn gma-btn-secondary" onclick="window.print()">🖨️ Print Report</button>
    </div>
    <div class="gma-card" style="margin-bottom:20px;">
      <form method="get" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        <input type="hidden" name="page" value="gma-finance-reports">
        <div class="gma-form-group"><label>From Date</label><input type="text" name="from" value="<?php echo esc_attr($from); ?>" class="gma-datepicker"></div>
        <div class="gma-form-group"><label>To Date</label><input type="text" name="to" value="<?php echo esc_attr($to); ?>" class="gma-datepicker"></div>
        <button type="submit" class="gma-btn gma-btn-primary">📊 Generate</button>
      </form>
    </div>
    <div class="gma-stats-grid" style="margin-bottom:24px;">
      <div class="gma-stat-card success"><div class="gma-stat-icon">💰</div><div><div class="gma-stat-number"><?php echo esc_html(GMA_Helper::format_currency($fee_collected)); ?></div><div class="gma-stat-label">Fees Collected</div></div></div>
      <div class="gma-stat-card danger"><div class="gma-stat-icon">⚠️</div><div><div class="gma-stat-number"><?php echo esc_html(GMA_Helper::format_currency($fee_due)); ?></div><div class="gma-stat-label">Fees Outstanding</div></div></div>
      <div class="gma-stat-card info"><div class="gma-stat-icon">📥</div><div><div class="gma-stat-number"><?php echo esc_html(GMA_Helper::format_currency($total_income)); ?></div><div class="gma-stat-label">Other Income</div></div></div>
      <div class="gma-stat-card warning"><div class="gma-stat-icon">📤</div><div><div class="gma-stat-number"><?php echo esc_html(GMA_Helper::format_currency($total_expense)); ?></div><div class="gma-stat-label">Total Expenses</div></div></div>
      <div class="gma-stat-card <?php echo $net >= 0 ? 'success' : 'danger'; ?>"><div class="gma-stat-icon">📈</div><div><div class="gma-stat-number"><?php echo esc_html(GMA_Helper::format_currency(abs($net))); ?></div><div class="gma-stat-label">Net <?php echo $net >= 0 ? 'Surplus' : 'Deficit'; ?></div></div></div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));gap:20px;">
      <div class="gma-card">
        <h4 class="gma-card-title">📈 Monthly Fee Collection</h4>
        <?php if($monthly_fees): ?>
        <table class="gma-table" style="margin:0;"><thead><tr><th>Month</th><th>Amount Collected</th></tr></thead><tbody>
        <?php foreach($monthly_fees as $m): ?>
        <tr><td><?php echo esc_html($m->month); ?></td><td><strong><?php echo esc_html(GMA_Helper::format_currency($m->total)); ?></strong></td></tr>
        <?php endforeach; ?>
        </tbody></table>
        <?php else: ?><p style="color:var(--gma-text-muted);font-size:13px;">No collections in this period.</p><?php endif; ?>
      </div>
      <div class="gma-card">
        <h4 class="gma-card-title">📤 Expenses by Category</h4>
        <?php if($exp_cats): ?>
        <table class="gma-table" style="margin:0;"><thead><tr><th>Category</th><th>Amount</th></tr></thead><tbody>
        <?php foreach($exp_cats as $e): ?>
        <tr><td><?php echo esc_html($e->label ?: 'Uncategorized'); ?></td><td><strong><?php echo esc_html(GMA_Helper::format_currency($e->total)); ?></strong></td></tr>
        <?php endforeach; ?>
        </tbody></table>
        <?php else: ?><p style="color:var(--gma-text-muted);font-size:13px;">No expenses in this period.</p><?php endif; ?>
      </div>
    </div>
  </div>
</div>
