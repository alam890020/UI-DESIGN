<?php
/**
 * Dashboard view.
 *
 * @package School_Softwere
 */

defined( 'ABSPATH' ) || die();

global $wpdb;

$school_id  = SS_Helper::get_current_school_id();
$session_id = SS_Helper::get_active_session_id( $school_id );

// ── Stats ──────────────────────────────────────────────────────────────────────
$total_students = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}ss_student_records WHERE school_id = %d AND is_active = 1", $school_id ) );
$total_staff    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}ss_staff WHERE school_id = %d AND is_active = 1", $school_id ) );
$total_revenue  = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(paid_amount),0) FROM {$wpdb->prefix}ss_invoices WHERE school_id = %d", $school_id ) );
$pending_fees   = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(due_amount),0) FROM {$wpdb->prefix}ss_invoices WHERE school_id = %d AND status != 'paid'", $school_id ) );

// ── Chart data — monthly fee collection (last 6 months)
$fee_months  = array();
$fee_amounts = array();
for ( $i = 5; $i >= 0; $i-- ) {
	$month_start = date( 'Y-m-01', strtotime( "-{$i} months" ) );
	$month_end   = date( 'Y-m-t',  strtotime( "-{$i} months" ) );
	$month_label = date( 'M Y',    strtotime( "-{$i} months" ) );
	$amount      = (float) $wpdb->get_var( $wpdb->prepare(
		"SELECT COALESCE(SUM(amount),0) FROM {$wpdb->prefix}ss_payments p
		 INNER JOIN {$wpdb->prefix}ss_invoices i ON p.invoice_id = i.ID
		 WHERE i.school_id = %d AND p.payment_date BETWEEN %s AND %s",
		$school_id, $month_start, $month_end
	) );
	$fee_months[]  = $month_label;
	$fee_amounts[] = $amount;
}

// ── Attendance doughnut data
$att_present = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}ss_attendance a INNER JOIN {$wpdb->prefix}ss_student_records sr ON a.student_record_id = sr.ID WHERE sr.school_id = %d AND a.date = %s AND a.status = 'present'", $school_id, current_time('Y-m-d') ) );
$att_absent  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}ss_attendance a INNER JOIN {$wpdb->prefix}ss_student_records sr ON a.student_record_id = sr.ID WHERE sr.school_id = %d AND a.date = %s AND a.status = 'absent'", $school_id, current_time('Y-m-d') ) );
$att_late    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}ss_attendance a INNER JOIN {$wpdb->prefix}ss_student_records sr ON a.student_record_id = sr.ID WHERE sr.school_id = %d AND a.date = %s AND a.status = 'late'", $school_id, current_time('Y-m-d') ) );

// ── Recent activity logs
$logs = $wpdb->get_results( $wpdb->prepare(
	"SELECT * FROM {$wpdb->prefix}ss_logs WHERE school_id = %d ORDER BY created_at DESC LIMIT 10",
	$school_id
) );

// ── Upcoming events
$events = $wpdb->get_results( $wpdb->prepare(
	"SELECT * FROM {$wpdb->prefix}ss_events WHERE school_id = %d AND start_date >= %s ORDER BY start_date ASC LIMIT 5",
	$school_id, current_time('Y-m-d')
) );

// ── Birthdays today
$today_md   = date( 'm-d' );
$birthdays_students = $wpdb->get_results( $wpdb->prepare(
	"SELECT first_name, last_name, photo, 'Student' AS role FROM {$wpdb->prefix}ss_student_records WHERE school_id = %d AND DATE_FORMAT(dob, '%%m-%%d') = %s AND is_active = 1 LIMIT 6",
	$school_id, $today_md
) );
$birthdays_staff = $wpdb->get_results( $wpdb->prepare(
	"SELECT first_name, last_name, photo, 'Staff' AS role FROM {$wpdb->prefix}ss_staff WHERE school_id = %d AND DATE_FORMAT(dob, '%%m-%%d') = %s AND is_active = 1 LIMIT 6",
	$school_id, $today_md
) );
$birthdays = array_merge( $birthdays_students, $birthdays_staff );

// ── Pending fee reminders
$pending_list = $wpdb->get_results( $wpdb->prepare(
	"SELECT sr.first_name, sr.last_name, sr.admission_number, i.invoice_number, i.due_amount, i.due_date
	 FROM {$wpdb->prefix}ss_invoices i
	 INNER JOIN {$wpdb->prefix}ss_student_records sr ON i.student_record_id = sr.ID
	 WHERE i.school_id = %d AND i.status != 'paid' ORDER BY i.due_date ASC LIMIT 8",
	$school_id
) );

$school = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_schools WHERE ID = %d", $school_id ) );
?>
<div class="ss-wrap">
	<!-- Header -->
	<div class="ss-header">
		<div class="ss-header-left">
			<h1 class="ss-header-title">🎓 School Softwere</h1>
			<span class="ss-school-pill"><?php echo esc_html( $school ? $school->label : __( 'No School', 'school-softwere' ) ); ?> ▾</span>
			<span class="ss-session-pill">📅 <?php echo esc_html( current_time( 'Y' ) ); ?></span>
		</div>
		<div class="ss-header-right">
			<button class="ss-notif-btn" title="<?php esc_attr_e( 'Notifications', 'school-softwere' ); ?>">
				🔔 <span class="ss-notif-badge"><?php echo esc_html( count( $events ) ); ?></span>
			</button>
			<div class="ss-user-info">
				<img src="<?php echo esc_url( get_avatar_url( get_current_user_id(), array( 'size' => 36 ) ) ); ?>" class="ss-avatar" alt="">
				<div>
					<div class="ss-user-name"><?php echo esc_html( wp_get_current_user()->display_name ); ?></div>
					<span class="ss-role-badge"><?php esc_html_e( 'Admin', 'school-softwere' ); ?></span>
				</div>
			</div>
		</div>
	</div>

	<div class="ss-page-content">
		<!-- Welcome Banner -->
		<div class="ss-welcome-banner">
			<div>
				<h2 class="ss-welcome-title">
					<?php
					$hour = (int) current_time( 'G' );
					if ( $hour < 12 )      $greet = __( 'Good Morning', 'school-softwere' );
					elseif ( $hour < 17 )  $greet = __( 'Good Afternoon', 'school-softwere' );
					else                   $greet = __( 'Good Evening', 'school-softwere' );
					echo esc_html( $greet . ', ' . wp_get_current_user()->display_name . '! 👋' );
					?>
				</h2>
				<p class="ss-welcome-sub"><?php echo esc_html( $school ? $school->label : __( 'School Softwere', 'school-softwere' ) ); ?></p>
				<p class="ss-welcome-date">📅 <?php echo esc_html( date_i18n( 'l, F j, Y' ) ); ?></p>
			</div>
			<span class="ss-welcome-icon">🏫</span>
		</div>

		<!-- Stats Cards -->
		<div class="ss-stats-grid">
			<div class="ss-stat-card">
				<div class="ss-stat-icon">🎓</div>
				<div>
					<div class="ss-stat-number"><?php echo esc_html( number_format( $total_students ) ); ?></div>
					<div class="ss-stat-label"><?php esc_html_e( 'Total Students', 'school-softwere' ); ?></div>
					<div class="ss-stat-trend up">↑ Active</div>
				</div>
			</div>
			<div class="ss-stat-card success">
				<div class="ss-stat-icon">👩‍🏫</div>
				<div>
					<div class="ss-stat-number"><?php echo esc_html( number_format( $total_staff ) ); ?></div>
					<div class="ss-stat-label"><?php esc_html_e( 'Total Staff', 'school-softwere' ); ?></div>
					<div class="ss-stat-trend up">↑ Active</div>
				</div>
			</div>
			<div class="ss-stat-card warning">
				<div class="ss-stat-icon">💰</div>
				<div>
					<div class="ss-stat-number"><?php echo esc_html( SS_Helper::format_currency( $total_revenue ) ); ?></div>
					<div class="ss-stat-label"><?php esc_html_e( 'Total Revenue', 'school-softwere' ); ?></div>
					<div class="ss-stat-trend up">↑ Collected</div>
				</div>
			</div>
			<div class="ss-stat-card danger">
				<div class="ss-stat-icon">⚠️</div>
				<div>
					<div class="ss-stat-number"><?php echo esc_html( SS_Helper::format_currency( $pending_fees ) ); ?></div>
					<div class="ss-stat-label"><?php esc_html_e( 'Pending Fees', 'school-softwere' ); ?></div>
					<div class="ss-stat-trend down">↓ Outstanding</div>
				</div>
			</div>
		</div>

		<!-- Quick Actions -->
		<div style="margin-bottom:28px;">
			<h3 style="font-family:'Nunito',sans-serif;font-size:18px;font-weight:700;color:var(--ss-dark);margin:0 0 16px;"><?php esc_html_e( 'Quick Actions', 'school-softwere' ); ?></h3>
			<div class="ss-quick-actions">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere-students&action=add' ) ); ?>" class="ss-quick-action-btn">
					<span class="ss-quick-action-icon">➕</span><span class="ss-quick-action-label"><?php esc_html_e( 'Add Student', 'school-softwere' ); ?></span>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere-fees&action=collect' ) ); ?>" class="ss-quick-action-btn">
					<span class="ss-quick-action-icon">💳</span><span class="ss-quick-action-label"><?php esc_html_e( 'Collect Fee', 'school-softwere' ); ?></span>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere-attendance' ) ); ?>" class="ss-quick-action-btn">
					<span class="ss-quick-action-icon">✅</span><span class="ss-quick-action-label"><?php esc_html_e( 'Mark Attendance', 'school-softwere' ); ?></span>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere-exams&action=add' ) ); ?>" class="ss-quick-action-btn">
					<span class="ss-quick-action-icon">📝</span><span class="ss-quick-action-label"><?php esc_html_e( 'Create Exam', 'school-softwere' ); ?></span>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere-staff&action=add' ) ); ?>" class="ss-quick-action-btn">
					<span class="ss-quick-action-icon">👤</span><span class="ss-quick-action-label"><?php esc_html_e( 'Add Staff', 'school-softwere' ); ?></span>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere-reports' ) ); ?>" class="ss-quick-action-btn">
					<span class="ss-quick-action-icon">📊</span><span class="ss-quick-action-label"><?php esc_html_e( 'View Reports', 'school-softwere' ); ?></span>
				</a>
			</div>
		</div>

		<!-- Charts -->
		<div class="ss-charts-grid">
			<div class="ss-chart-card">
				<h4 class="ss-chart-title">📈 <?php esc_html_e( 'Fee Collection (Last 6 Months)', 'school-softwere' ); ?></h4>
				<div style="height:240px;"><canvas id="ss-fee-chart" class="ss-chart-canvas"></canvas></div>
			</div>
			<div class="ss-chart-card">
				<h4 class="ss-chart-title">🎯 <?php esc_html_e( 'Today\'s Attendance', 'school-softwere' ); ?></h4>
				<div style="height:240px;"><canvas id="ss-att-chart" class="ss-chart-canvas"></canvas></div>
			</div>
		</div>

		<!-- Two-column bottom section -->
		<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));gap:20px;margin-bottom:28px;">

			<!-- Recent Activity -->
			<div class="ss-card">
				<div class="ss-card-header">
					<h4 class="ss-card-title">📋 <?php esc_html_e( 'Recent Activity', 'school-softwere' ); ?></h4>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere-logs' ) ); ?>" class="ss-btn ss-btn-secondary ss-btn-sm"><?php esc_html_e( 'View All', 'school-softwere' ); ?></a>
				</div>
				<div class="ss-activity-feed">
					<?php if ( $logs ) : foreach ( $logs as $log ) : ?>
					<div class="ss-activity-item">
						<div class="ss-activity-dot"></div>
						<div class="ss-activity-text"><?php echo esc_html( $log->action ); ?><?php if ( $log->details ) echo ' — ' . esc_html( wp_trim_words( $log->details, 8 ) ); ?></div>
						<div class="ss-activity-time"><?php echo esc_html( human_time_diff( strtotime( $log->created_at ) ) ) . ' ' . __( 'ago', 'school-softwere' ); ?></div>
					</div>
					<?php endforeach; else : ?>
					<p style="color:var(--ss-text-muted);font-size:13px;"><?php esc_html_e( 'No recent activity.', 'school-softwere' ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<!-- Upcoming Events -->
			<div class="ss-card">
				<div class="ss-card-header">
					<h4 class="ss-card-title">🗓️ <?php esc_html_e( 'Upcoming Events', 'school-softwere' ); ?></h4>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere-events' ) ); ?>" class="ss-btn ss-btn-secondary ss-btn-sm"><?php esc_html_e( 'View All', 'school-softwere' ); ?></a>
				</div>
				<?php if ( $events ) : ?>
				<div style="display:flex;flex-direction:column;gap:10px;">
					<?php foreach ( $events as $event ) : ?>
					<div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--ss-border);">
						<div style="background:#EEF2FF;border-radius:8px;padding:8px 12px;text-align:center;min-width:46px;">
							<div style="font-family:'Nunito',sans-serif;font-weight:800;font-size:18px;color:var(--ss-primary);"><?php echo esc_html( date( 'd', strtotime( $event->start_date ) ) ); ?></div>
							<div style="font-size:10px;font-weight:600;color:var(--ss-text-muted);text-transform:uppercase;"><?php echo esc_html( date( 'M', strtotime( $event->start_date ) ) ); ?></div>
						</div>
						<div>
							<div style="font-weight:600;font-size:13px;"><?php echo esc_html( $event->title ); ?></div>
							<?php if ( $event->venue ) : ?><div style="font-size:12px;color:var(--ss-text-muted);">📍 <?php echo esc_html( $event->venue ); ?></div><?php endif; ?>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				<?php else : ?>
				<p style="color:var(--ss-text-muted);font-size:13px;"><?php esc_html_e( 'No upcoming events.', 'school-softwere' ); ?></p>
				<?php endif; ?>
			</div>

			<!-- Birthdays -->
			<div class="ss-card">
				<div class="ss-card-header">
					<h4 class="ss-card-title">🎂 <?php esc_html_e( "Today's Birthdays", 'school-softwere' ); ?></h4>
				</div>
				<?php if ( $birthdays ) : ?>
				<div class="ss-birthday-list">
					<?php foreach ( $birthdays as $bday ) : ?>
					<div class="ss-birthday-item">
						<img src="<?php echo $bday->photo ? esc_url( $bday->photo ) : esc_url( SS_PLUGIN_URL . 'assets/images/default-avatar.png' ); ?>" class="ss-birthday-avatar" alt="">
						<div>
							<div class="ss-birthday-name"><?php echo esc_html( $bday->first_name . ' ' . $bday->last_name ); ?></div>
							<div class="ss-birthday-role"><?php echo esc_html( $bday->role ); ?></div>
						</div>
						<span style="margin-left:auto;">🎂</span>
					</div>
					<?php endforeach; ?>
				</div>
				<?php else : ?>
				<p style="color:var(--ss-text-muted);font-size:13px;"><?php esc_html_e( 'No birthdays today.', 'school-softwere' ); ?></p>
				<?php endif; ?>
			</div>

			<!-- Pending Fee Reminders -->
			<div class="ss-card">
				<div class="ss-card-header">
					<h4 class="ss-card-title">💸 <?php esc_html_e( 'Pending Fee Reminders', 'school-softwere' ); ?></h4>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere-fees&tab=pending' ) ); ?>" class="ss-btn ss-btn-secondary ss-btn-sm"><?php esc_html_e( 'View All', 'school-softwere' ); ?></a>
				</div>
				<?php if ( $pending_list ) : ?>
				<table class="ss-table" style="margin:0;">
					<thead><tr>
						<th><?php esc_html_e( 'Student', 'school-softwere' ); ?></th>
						<th><?php esc_html_e( 'Invoice', 'school-softwere' ); ?></th>
						<th><?php esc_html_e( 'Due', 'school-softwere' ); ?></th>
						<th><?php esc_html_e( 'Amount', 'school-softwere' ); ?></th>
					</tr></thead>
					<tbody>
					<?php foreach ( $pending_list as $p ) : ?>
					<tr>
						<td><?php echo esc_html( $p->first_name . ' ' . $p->last_name ); ?></td>
						<td><code><?php echo esc_html( $p->invoice_number ); ?></code></td>
						<td><?php echo esc_html( SS_Helper::format_date( $p->due_date ) ); ?></td>
						<td><?php echo esc_html( SS_Helper::format_currency( $p->due_amount ) ); ?></td>
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php else : ?>
				<p style="color:var(--ss-text-muted);font-size:13px;">🎉 <?php esc_html_e( 'No pending fees! All dues are cleared.', 'school-softwere' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div><!-- .ss-page-content -->
</div><!-- .ss-wrap -->

<script>
jQuery(document).on('ss:charts:init', function () {
	// Fee collection line chart
	SS_Charts.line('ss-fee-chart',
		<?php echo wp_json_encode( $fee_months ); ?>,
		[{
			label: '<?php esc_html_e( 'Fee Collected', 'school-softwere' ); ?>',
			data: <?php echo wp_json_encode( $fee_amounts ); ?>,
			borderColor: '#4F46E5',
			backgroundColor: 'rgba(79,70,229,0.08)',
			tension: 0.4,
			fill: true,
			pointBackgroundColor: '#4F46E5',
		}]
	);
	// Attendance doughnut chart
	SS_Charts.doughnut('ss-att-chart',
		['<?php esc_html_e( 'Present', 'school-softwere' ); ?>', '<?php esc_html_e( 'Absent', 'school-softwere' ); ?>', '<?php esc_html_e( 'Late', 'school-softwere' ); ?>'],
		[<?php echo (int) $att_present; ?>, <?php echo (int) $att_absent; ?>, <?php echo (int) $att_late; ?>],
		['#10B981', '#EF4444', '#F59E0B']
	);
});
</script>
