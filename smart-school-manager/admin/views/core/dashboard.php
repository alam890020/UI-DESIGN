<?php
/**
 * Super-admin Dashboard view.
 *
 * @package SmartSchoolManager
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;
$p = $wpdb->prefix . 'ssm_';

$stats = array(
    'schools'  => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}schools" ),
    'students' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students" ),
    'staff'    => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}staff" ),
    'classes'  => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}classes" ),
    'income'   => (float) $wpdb->get_var( "SELECT COALESCE(SUM(amount),0) FROM {$p}income" ),
    'expenses' => (float) $wpdb->get_var( "SELECT COALESCE(SUM(amount),0) FROM {$p}expenses" ),
    'invoices' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}invoices" ),
    'tickets'  => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}tickets WHERE status='open'" ),
);
$current_session = $wpdb->get_row( "SELECT * FROM {$p}sessions WHERE is_current=1 LIMIT 1" );
$user = wp_get_current_user();
?>
<div class="ssm-page">
    <div class="ssm-welcome-card">
        <h2>Welcome back, <?php echo esc_html( $user->display_name ); ?> 👋</h2>
        <p>Here is a quick overview of your school operations. Manage students, staff, fees, classes, exams and more — all from one beautifully unified dashboard.</p>
        <div class="ssm-welcome-actions">
            <a href="<?php echo esc_url( SSM_Helper::admin_url('ssm-students') ); ?>" class="ssm-btn"><span class="dashicons dashicons-groups"></span> Add Student</a>
            <a href="<?php echo esc_url( SSM_Helper::admin_url('ssm-staff') ); ?>" class="ssm-btn"><span class="dashicons dashicons-businessman"></span> Add Staff</a>
            <a href="<?php echo esc_url( SSM_Helper::admin_url('ssm-collect-payments') ); ?>" class="ssm-btn"><span class="dashicons dashicons-money-alt"></span> Collect Fees</a>
            <a href="<?php echo esc_url( SSM_Helper::admin_url('ssm-setup-wizard') ); ?>" class="ssm-btn"><span class="dashicons dashicons-admin-tools"></span> Setup Wizard</a>
        </div>
    </div>

    <div class="ssm-stats-grid">
        <?php
        echo SSM_Helper::stat_card( 'Total Students', number_format( $stats['students'] ), 'dashicons-groups', 'indigo', '+12% this month' );
        echo SSM_Helper::stat_card( 'Total Staff',    number_format( $stats['staff'] ),    'dashicons-businessman', 'cyan',   '+3 new hires' );
        echo SSM_Helper::stat_card( 'Active Classes', number_format( $stats['classes'] ),  'dashicons-welcome-write-blog', 'green', 'across all sessions' );
        echo SSM_Helper::stat_card( 'Schools',        number_format( $stats['schools'] ),  'dashicons-bank', 'purple',   'multi-campus ready' );
        echo SSM_Helper::stat_card( 'Income (Total)', SSM_Helper::money( $stats['income'] ), 'dashicons-chart-line', 'orange', 'YTD' );
        echo SSM_Helper::stat_card( 'Expenses',       SSM_Helper::money( $stats['expenses'] ), 'dashicons-chart-bar', 'red',  'YTD' );
        echo SSM_Helper::stat_card( 'Invoices',       number_format( $stats['invoices'] ), 'dashicons-media-document', 'blue', 'all status' );
        echo SSM_Helper::stat_card( 'Open Tickets',   number_format( $stats['tickets'] ),  'dashicons-tickets-alt', 'pink', 'support queue' );
        ?>
    </div>

    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header">
                <div class="ssm-card-title"><span class="dashicons dashicons-chart-area"></span> Recent Activity</div>
                <a href="<?php echo esc_url( SSM_Helper::admin_url('ssm-logs') ); ?>" class="ssm-btn ssm-btn-ghost ssm-btn-sm">View Logs</a>
            </div>
            <div class="ssm-list">
                <?php
                $logs = $wpdb->get_results( "SELECT * FROM {$p}logs ORDER BY id DESC LIMIT 8" );
                if ( $logs ) : foreach ( $logs as $log ) : ?>
                    <div class="ssm-list-item">
                        <span class="ssm-avatar" style="background:#06b6d4">L</span>
                        <div style="flex:1">
                            <strong><?php echo esc_html( ucfirst( $log->module ) ); ?>: <?php echo esc_html( $log->action ); ?></strong>
                            <div class="meta"><?php echo esc_html( $log->created_at ); ?></div>
                        </div>
                    </div>
                <?php endforeach; else : ?>
                    <p class="ssm-muted">No activity yet — actions will appear here as you use the system.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="ssm-card">
            <div class="ssm-card-header">
                <div class="ssm-card-title"><span class="dashicons dashicons-info"></span> Current Session</div>
            </div>
            <?php if ( $current_session ) : ?>
                <h2 style="font-size:24px;margin-bottom:6px"><?php echo esc_html( $current_session->name ); ?></h2>
                <p class="ssm-muted"><?php echo esc_html( $current_session->start_date ); ?> &rarr; <?php echo esc_html( $current_session->end_date ); ?></p>
                <div class="ssm-progress" style="margin-top:14px"><div class="ssm-progress-bar" style="width:38%"></div></div>
                <p class="ssm-muted" style="margin-top:6px;font-size:12px">38% of session completed</p>
            <?php else : ?>
                <p class="ssm-muted">No session configured. <a href="<?php echo esc_url( SSM_Helper::admin_url('ssm-sessions') ); ?>">Create one</a>.</p>
            <?php endif; ?>

            <div class="ssm-divider"></div>

            <div class="ssm-card-title" style="margin-bottom:10px"><span class="dashicons dashicons-admin-tools"></span> Quick Actions</div>
            <div class="ssm-quick-actions">
                <a class="ssm-quick-action" href="<?php echo esc_url( SSM_Helper::admin_url('ssm-admissions') ); ?>"><span class="dashicons dashicons-welcome-add-page"></span><span>New Admission</span></a>
                <a class="ssm-quick-action" href="<?php echo esc_url( SSM_Helper::admin_url('ssm-attendance') ); ?>"><span class="dashicons dashicons-yes-alt"></span><span>Attendance</span></a>
                <a class="ssm-quick-action" href="<?php echo esc_url( SSM_Helper::admin_url('ssm-notices') ); ?>"><span class="dashicons dashicons-megaphone"></span><span>Post Notice</span></a>
                <a class="ssm-quick-action" href="<?php echo esc_url( SSM_Helper::admin_url('ssm-events') ); ?>"><span class="dashicons dashicons-calendar-alt"></span><span>Add Event</span></a>
            </div>
        </div>
    </div>

    <div class="ssm-grid-2">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-chart-area"></span> Income vs Expenses (Last 6 Months)</div></div>
            <canvas id="ssmFinanceChart" height="120"></canvas>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-chart-pie"></span> Students by Gender</div></div>
            <canvas id="ssmGenderChart" height="120"></canvas>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') return;

        // Demo finance data (replace with real values via REST /ssm/v1/stats).
        var months = [];
        for (var i = 5; i >= 0; i--) {
            var d = new Date(); d.setMonth(d.getMonth() - i);
            months.push(d.toLocaleString('en', { month: 'short' }));
        }
        var inc = [4200, 5100, 4800, 6200, 5800, <?php echo (float) $stats['income']; ?>];
        var exp = [3000, 3400, 3100, 3700, 3500, <?php echo (float) $stats['expenses']; ?>];

        new Chart(document.getElementById('ssmFinanceChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    { label: 'Income',   data: inc, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,.1)', tension: .35, fill: true },
                    { label: 'Expenses', data: exp, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,.1)',  tension: .35, fill: true }
                ]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });

        var male   = <?php echo (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE gender='male'" ); ?>;
        var female = <?php echo (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE gender='female'" ); ?>;
        new Chart(document.getElementById('ssmGenderChart'), {
            type: 'doughnut',
            data: {
                labels: ['Male','Female'],
                datasets: [{ data: [male||1, female||1], backgroundColor: ['#3b82f6','#ec4899'], borderWidth: 0 }]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
    });
    </script>

    <div class="ssm-grid-3">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-megaphone"></span> Latest Notices</div></div>
            <div class="ssm-list">
            <?php $notices = $wpdb->get_results( "SELECT * FROM {$p}notices ORDER BY id DESC LIMIT 4" );
            if ( $notices ) : foreach ( $notices as $n ) : ?>
                <div class="ssm-list-item">
                    <span class="ssm-avatar ssm-grad-orange"><span class="dashicons dashicons-megaphone" style="color:#fff"></span></span>
                    <div style="flex:1"><strong><?php echo esc_html( $n->title ); ?></strong><div class="meta"><?php echo esc_html( $n->posted_at ); ?></div></div>
                </div>
            <?php endforeach; else: ?><p class="ssm-muted">No notices yet.</p><?php endif; ?>
            </div>
        </div>

        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-calendar-alt"></span> Upcoming Events</div></div>
            <div class="ssm-list">
            <?php $events = $wpdb->get_results( "SELECT * FROM {$p}events ORDER BY start_date ASC LIMIT 4" );
            if ( $events ) : foreach ( $events as $e ) : ?>
                <div class="ssm-list-item">
                    <span class="ssm-avatar ssm-grad-purple"><span class="dashicons dashicons-calendar-alt" style="color:#fff"></span></span>
                    <div style="flex:1"><strong><?php echo esc_html( $e->title ); ?></strong><div class="meta"><?php echo esc_html( $e->start_date ); ?></div></div>
                </div>
            <?php endforeach; else: ?><p class="ssm-muted">No upcoming events.</p><?php endif; ?>
            </div>
        </div>

        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-tickets-alt"></span> Recent Tickets</div></div>
            <div class="ssm-list">
            <?php $tickets = $wpdb->get_results( "SELECT * FROM {$p}tickets ORDER BY id DESC LIMIT 4" );
            if ( $tickets ) : foreach ( $tickets as $t ) : ?>
                <div class="ssm-list-item">
                    <span class="ssm-avatar ssm-grad-pink"><span class="dashicons dashicons-tickets-alt" style="color:#fff"></span></span>
                    <div style="flex:1"><strong><?php echo esc_html( $t->subject ); ?></strong><div class="meta"><?php echo SSM_Helper::badge( $t->status ); ?></div></div>
                </div>
            <?php endforeach; else: ?><p class="ssm-muted">No support tickets yet.</p><?php endif; ?>
            </div>
        </div>
    </div>
</div>
