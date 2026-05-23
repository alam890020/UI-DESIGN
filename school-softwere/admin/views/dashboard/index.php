<?php defined( 'ABSPATH' ) || exit;
global $wpdb;
$school_id  = GMA_Helper::get_current_school_id();
$session_id = GMA_Helper::get_active_session_id( $school_id );
$school     = GMA_Helper::get_school( $school_id );

$total_students = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . GMA_TABLE_STUDENT_RECORDS . " WHERE school_id=%d AND is_active=1", $school_id ) );
$total_staff    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . GMA_TABLE_STAFF . " WHERE school_id=%d AND is_active=1", $school_id ) );
$total_revenue  = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(paid_amount),0) FROM " . GMA_TABLE_INVOICES . " WHERE school_id=%d", $school_id ) );
$pending_fees   = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(due_amount),0) FROM " . GMA_TABLE_INVOICES . " WHERE school_id=%d AND status!='paid'", $school_id ) );
$total_classes  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . GMA_TABLE_CLASS_SCHOOL . " WHERE school_id=%d AND session_id=%d", $school_id, $session_id ) );
$open_tickets   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . GMA_TABLE_TICKETS . " WHERE school_id=%d AND status='open'", $school_id ) );
$pending_adm    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . GMA_TABLE_ADMISSIONS . " WHERE school_id=%d AND status='pending'", $school_id ) );
$unread_notif   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . GMA_TABLE_NOTIFICATIONS . " WHERE school_id=%d AND is_read=0", $school_id ) );

$fee_months = $fee_amounts = array();
for ( $i = 5; $i >= 0; $i-- ) {
  $ms = date( 'Y-m-01', strtotime( "-{$i} months" ) );
  $me = date( 'Y-m-t',  strtotime( "-{$i} months" ) );
  $fee_months[]  = date( 'M Y', strtotime( "-{$i} months" ) );
  $fee_amounts[] = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(p.amount),0) FROM " . GMA_TABLE_PAYMENTS . " p INNER JOIN " . GMA_TABLE_INVOICES . " i ON p.invoice_id=i.ID WHERE i.school_id=%d AND p.payment_date BETWEEN %s AND %s", $school_id, $ms, $me ) );
}

$today         = current_time( 'Y-m-d' );
$att_present   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . GMA_TABLE_ATTENDANCE . " a INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON a.student_record_id=sr.ID WHERE sr.school_id=%d AND a.date=%s AND a.status='present'", $school_id, $today ) );
$att_absent    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . GMA_TABLE_ATTENDANCE . " a INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON a.student_record_id=sr.ID WHERE sr.school_id=%d AND a.date=%s AND a.status='absent'", $school_id, $today ) );
$att_late      = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . GMA_TABLE_ATTENDANCE . " a INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON a.student_record_id=sr.ID WHERE sr.school_id=%d AND a.date=%s AND a.status='late'", $school_id, $today ) );

$logs     = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_LOGS . " WHERE school_id=%d ORDER BY created_at DESC LIMIT 10", $school_id ) );
$events   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_EVENTS . " WHERE school_id=%d AND start_date>=%s ORDER BY start_date LIMIT 5", $school_id, $today ) );
$today_md = date( 'm-d' );
$bdstud   = $wpdb->get_results( $wpdb->prepare( "SELECT first_name, last_name, photo, 'Student' AS role FROM " . GMA_TABLE_STUDENT_RECORDS . " WHERE school_id=%d AND DATE_FORMAT(dob,'%%m-%%d')=%s AND is_active=1 LIMIT 5", $school_id, $today_md ) );
$bdstaff  = $wpdb->get_results( $wpdb->prepare( "SELECT first_name, last_name, photo, 'Staff' AS role FROM " . GMA_TABLE_STAFF . " WHERE school_id=%d AND DATE_FORMAT(dob,'%%m-%%d')=%s AND is_active=1 LIMIT 5", $school_id, $today_md ) );
$birthdays = array_merge( $bdstud, $bdstaff );
$pending_invoices = $wpdb->get_results( $wpdb->prepare( "SELECT i.invoice_number, i.due_amount, i.due_date, CONCAT(sr.first_name,' ',sr.last_name) AS student_name, sr.admission_number FROM " . GMA_TABLE_INVOICES . " i INNER JOIN " . GMA_TABLE_STUDENT_RECORDS . " sr ON i.student_record_id=sr.ID WHERE i.school_id=%d AND i.status!='paid' ORDER BY i.due_date ASC LIMIT 8", $school_id ) );
$hour = (int) current_time( 'G' );
$greet = $hour < 12 ? 'Good Morning' : ( $hour < 17 ? 'Good Afternoon' : 'Good Evening' );
?>
<div class="gma-wrap">
  <?php include GMA_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
  <div class="gma-page-content">

    <!-- Welcome Banner -->
    <div class="gma-welcome-banner">
      <div>
        <h2 class="gma-welcome-title"><?php echo esc_html( $greet . ', ' . wp_get_current_user()->display_name . '! 👋' ); ?></h2>
        <p class="gma-welcome-sub"><?php echo esc_html( $school ? $school->label : 'Guidance Modern Academy' ); ?></p>
        <p class="gma-welcome-date">📅 <?php echo esc_html( date_i18n( 'l, F j, Y' ) ); ?></p>
      </div>
      <span class="gma-welcome-icon">🏫</span>
    </div>

    <!-- Stats Row 1 -->
    <div class="gma-stats-grid">
      <div class="gma-stat-card"><div class="gma-stat-icon">🎓</div><div><div class="gma-stat-number"><?php echo number_format( $total_students ); ?></div><div class="gma-stat-label">Total Students</div><div class="gma-stat-trend up">↑ Active</div></div></div>
      <div class="gma-stat-card success"><div class="gma-stat-icon">👩‍🏫</div><div><div class="gma-stat-number"><?php echo number_format( $total_staff ); ?></div><div class="gma-stat-label">Total Staff</div><div class="gma-stat-trend up">↑ Active</div></div></div>
      <div class="gma-stat-card warning"><div class="gma-stat-icon">💰</div><div><div class="gma-stat-number"><?php echo GMA_Helper::format_currency( $total_revenue ); ?></div><div class="gma-stat-label">Total Revenue</div><div class="gma-stat-trend up">↑ Collected</div></div></div>
      <div class="gma-stat-card danger"><div class="gma-stat-icon">⚠️</div><div><div class="gma-stat-number"><?php echo GMA_Helper::format_currency( $pending_fees ); ?></div><div class="gma-stat-label">Pending Fees</div><div class="gma-stat-trend down">↓ Outstanding</div></div></div>
      <div class="gma-stat-card info"><div class="gma-stat-icon">📚</div><div><div class="gma-stat-number"><?php echo number_format( $total_classes ); ?></div><div class="gma-stat-label">Active Classes</div></div></div>
      <div class="gma-stat-card"><div class="gma-stat-icon">📋</div><div><div class="gma-stat-number"><?php echo number_format( $pending_adm ); ?></div><div class="gma-stat-label">Pending Admissions</div></div></div>
      <div class="gma-stat-card warning"><div class="gma-stat-icon">🎫</div><div><div class="gma-stat-number"><?php echo number_format( $open_tickets ); ?></div><div class="gma-stat-label">Open Tickets</div></div></div>
      <div class="gma-stat-card info"><div class="gma-stat-icon">🔔</div><div><div class="gma-stat-number"><?php echo number_format( $unread_notif ); ?></div><div class="gma-stat-label">Unread Notifications</div></div></div>
    </div>

    <!-- Quick Actions -->
    <div class="gma-card">
      <div class="gma-card-header"><h4 class="gma-card-title">⚡ Quick Actions</h4></div>
      <div class="gma-quick-actions">
        <?php
        $actions = array(
          array( 'icon' => '➕', 'label' => 'Add Student',     'url' => admin_url( 'admin.php?page=gma-students' ) ),
          array( 'icon' => '💳', 'label' => 'Collect Fee',      'url' => admin_url( 'admin.php?page=gma-payments' ) ),
          array( 'icon' => '✅', 'label' => 'Mark Attendance',  'url' => admin_url( 'admin.php?page=gma-attendance' ) ),
          array( 'icon' => '📝', 'label' => 'Create Exam',      'url' => admin_url( 'admin.php?page=gma-exams' ) ),
          array( 'icon' => '👤', 'label' => 'Add Staff',        'url' => admin_url( 'admin.php?page=gma-staff' ) ),
          array( 'icon' => '📋', 'label' => 'New Admission',    'url' => admin_url( 'admin.php?page=gma-admissions' ) ),
          array( 'icon' => '📢', 'label' => 'Post Notice',      'url' => admin_url( 'admin.php?page=gma-notices' ) ),
          array( 'icon' => '📊', 'label' => 'View Reports',     'url' => admin_url( 'admin.php?page=gma-reports' ) ),
        );
        foreach ( $actions as $a ) : ?>
          <a href="<?php echo esc_url( $a['url'] ); ?>" class="gma-quick-action-btn">
            <span class="gma-quick-action-icon"><?php echo $a['icon']; ?></span>
            <span class="gma-quick-action-label"><?php echo esc_html( $a['label'] ); ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Charts -->
    <div class="gma-charts-grid">
      <div class="gma-chart-card"><h4 class="gma-chart-title">📈 Fee Collection (Last 6 Months)</h4><div style="height:240px;"><canvas id="gma-fee-chart" class="gma-chart-canvas"></canvas></div></div>
      <div class="gma-chart-card"><h4 class="gma-chart-title">🎯 Today's Attendance</h4><div style="height:240px;"><canvas id="gma-att-chart" class="gma-chart-canvas"></canvas></div></div>
    </div>

    <!-- Grid: Activity + Events + Birthdays + Pending Fees -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));gap:20px;margin-bottom:28px;">
      <div class="gma-card">
        <div class="gma-card-header"><h4 class="gma-card-title">📋 Recent Activity</h4><a href="<?php echo esc_url( admin_url( 'admin.php?page=gma-logs' ) ); ?>" class="gma-btn gma-btn-secondary gma-btn-sm">View All</a></div>
        <div class="gma-activity-feed">
          <?php if ( $logs ) : foreach ( $logs as $l ) : ?>
          <div class="gma-activity-item"><div class="gma-activity-dot"></div><div class="gma-activity-text"><?php echo esc_html( $l->action ); echo $l->details ? ' — ' . esc_html( wp_trim_words( $l->details, 6 ) ) : ''; ?></div><div class="gma-activity-time"><?php echo esc_html( human_time_diff( strtotime( $l->created_at ) ) ); ?> ago</div></div>
          <?php endforeach; else : ?><p style="color:var(--gma-text-muted);font-size:13px;">No recent activity.</p><?php endif; ?>
        </div>
      </div>
      <div class="gma-card">
        <div class="gma-card-header"><h4 class="gma-card-title">🗓️ Upcoming Events</h4><a href="<?php echo esc_url( admin_url( 'admin.php?page=gma-events' ) ); ?>" class="gma-btn gma-btn-secondary gma-btn-sm">View All</a></div>
        <?php if ( $events ) : foreach ( $events as $ev ) : ?>
        <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--gma-border);">
          <div style="background:var(--gma-primary-light);border-radius:8px;padding:8px 12px;text-align:center;min-width:46px;"><div style="font-weight:800;font-size:18px;color:var(--gma-primary);"><?php echo date('d', strtotime($ev->start_date)); ?></div><div style="font-size:10px;font-weight:600;text-transform:uppercase;color:var(--gma-text-muted);"><?php echo date('M', strtotime($ev->start_date)); ?></div></div>
          <div><div style="font-weight:600;font-size:13px;"><?php echo esc_html( $ev->title ); ?></div><?php if ( $ev->venue ) : ?><div style="font-size:12px;color:var(--gma-text-muted);">📍 <?php echo esc_html( $ev->venue ); ?></div><?php endif; ?></div>
        </div>
        <?php endforeach; else : ?><p style="color:var(--gma-text-muted);font-size:13px;">No upcoming events.</p><?php endif; ?>
      </div>
      <div class="gma-card">
        <div class="gma-card-header"><h4 class="gma-card-title">🎂 Today's Birthdays</h4></div>
        <?php if ( $birthdays ) : ?>
        <div class="gma-birthday-list">
          <?php foreach ( $birthdays as $b ) : ?>
          <div class="gma-birthday-item">
            <img src="<?php echo $b->photo ? esc_url($b->photo) : esc_url(GMA_PLUGIN_URL.'assets/images/default-avatar.png'); ?>" class="gma-birthday-avatar" alt="">
            <div><div class="gma-birthday-name"><?php echo esc_html($b->first_name.' '.$b->last_name); ?></div><div class="gma-birthday-role"><?php echo esc_html($b->role); ?></div></div>
            <span style="margin-left:auto;">🎂</span>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else : ?><p style="color:var(--gma-text-muted);font-size:13px;">No birthdays today.</p><?php endif; ?>
      </div>
      <div class="gma-card">
        <div class="gma-card-header"><h4 class="gma-card-title">💸 Pending Fee Reminders</h4><a href="<?php echo esc_url( admin_url('admin.php?page=gma-invoices') ); ?>" class="gma-btn gma-btn-secondary gma-btn-sm">View All</a></div>
        <?php if ( $pending_invoices ) : ?>
        <table class="gma-table" style="margin:0;">
          <thead><tr><th>Student</th><th>Invoice</th><th>Due</th><th>Amount</th></tr></thead>
          <tbody>
          <?php foreach ( $pending_invoices as $pi ) : ?>
          <tr>
            <td><?php echo esc_html($pi->student_name); ?></td>
            <td><code><?php echo esc_html($pi->invoice_number); ?></code></td>
            <td><?php echo esc_html( GMA_Helper::format_date( $pi->due_date ) ); ?></td>
            <td><?php echo esc_html( GMA_Helper::format_currency($pi->due_amount) ); ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php else : ?><p style="color:var(--gma-text-muted);font-size:13px;">🎉 All fees are cleared!</p><?php endif; ?>
      </div>
    </div>

  </div><!-- .gma-page-content -->
</div><!-- .gma-wrap -->
<script>
jQuery(document).on('gma:charts:init', function () {
  GMA_Charts.line('gma-fee-chart', <?php echo wp_json_encode($fee_months); ?>, [{
    label: 'Fee Collected', data: <?php echo wp_json_encode($fee_amounts); ?>,
    borderColor: '#4F46E5', backgroundColor: 'rgba(79,70,229,.08)', tension: 0.4, fill: true, pointBackgroundColor: '#4F46E5'
  }]);
  GMA_Charts.doughnut('gma-att-chart',
    ['Present','Absent','Late'],
    [<?php echo (int)$att_present; ?>, <?php echo (int)$att_absent; ?>, <?php echo (int)$att_late; ?>],
    ['#10B981','#EF4444','#F59E0B']
  );
});
</script>
