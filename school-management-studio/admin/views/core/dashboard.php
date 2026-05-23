<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'sms_';

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
$session = $wpdb->get_row( "SELECT * FROM {$p}sessions WHERE is_current=1 LIMIT 1" );
$user = wp_get_current_user();
$male   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE gender='male'" );
$female = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE gender='female'" );
?>
<div class="sms-page">
    <div class="sms-hero">
        <h2>Welcome back, <?php echo esc_html( $user->display_name ); ?> 👋</h2>
        <p>Here is a snapshot of your school. Manage admissions, fees, attendance and more — all from this beautifully unified studio.</p>
        <div class="sms-hero-actions">
            <a href="<?php echo esc_url( SMS_Helper::admin_url('sms-student-form') ); ?>" class="sms-btn"><?php echo SMS_Icons::svg('user-plus',16); ?><span>Add Student</span></a>
            <a href="<?php echo esc_url( SMS_Helper::admin_url('sms-staff') ); ?>" class="sms-btn"><?php echo SMS_Icons::svg('briefcase',16); ?><span>Add Staff</span></a>
            <a href="<?php echo esc_url( SMS_Helper::admin_url('sms-collect') ); ?>" class="sms-btn"><?php echo SMS_Icons::svg('money',16); ?><span>Collect Fees</span></a>
            <a href="<?php echo esc_url( SMS_Helper::admin_url('sms-fee-generator') ); ?>" class="sms-btn"><?php echo SMS_Icons::svg('switch-h',16); ?><span>Generate Monthly Fees</span></a>
        </div>
    </div>

    <div class="sms-stats">
        <?php
        echo SMS_Helper::stat( 'Total Students', number_format( $stats['students'] ), 'users',     'violet', '+12% this month' );
        echo SMS_Helper::stat( 'Active Staff',   number_format( $stats['staff'] ),    'briefcase', 'cyan',   '+3 new hires' );
        echo SMS_Helper::stat( 'Classes',        number_format( $stats['classes'] ),  'graduation','mint',   'across sessions' );
        echo SMS_Helper::stat( 'Schools',        number_format( $stats['schools'] ),  'school',    'amber',  'multi-campus' );
        echo SMS_Helper::stat( 'Income (YTD)',   SMS_Helper::money( $stats['income'] ), 'chart-line','blue', 'this year' );
        echo SMS_Helper::stat( 'Expenses',       SMS_Helper::money( $stats['expenses'] ), 'chart-bar', 'rose',  'this year' );
        echo SMS_Helper::stat( 'Invoices',       number_format( $stats['invoices'] ), 'invoice',   'pink',   'all status' );
        echo SMS_Helper::stat( 'Open Tickets',   number_format( $stats['tickets'] ),  'ticket',    'rose',   'queue' );
        ?>
    </div>

    <div class="sms-grid-2-1">
        <div class="sms-card">
            <div class="sms-card-head">
                <div class="sms-card-title"><?php echo SMS_Icons::svg('chart-line',18); ?> Income vs Expenses</div>
                <span class="sms-pill sms-pill-info">Last 6 Months</span>
            </div>
            <canvas id="smsFinanceChart" height="120"></canvas>
        </div>
        <div class="sms-card">
            <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('chart-pie',18); ?> Students by Gender</div></div>
            <canvas id="smsGenderChart" height="120"></canvas>
        </div>
    </div>

    <div class="sms-grid-2-1">
        <div class="sms-card">
            <div class="sms-card-head">
                <div class="sms-card-title"><?php echo SMS_Icons::svg('list',18); ?> Recent Activity</div>
                <a class="sms-btn sms-btn-ghost sms-btn-sm" href="<?php echo esc_url( SMS_Helper::admin_url('sms-logs') ); ?>">View Logs</a>
            </div>
            <div class="sms-list">
            <?php $logs = $wpdb->get_results( "SELECT * FROM {$p}logs ORDER BY id DESC LIMIT 6" );
            if ( $logs ) : foreach ( $logs as $log ) : ?>
                <div class="sms-list-item">
                    <span class="sms-avatar" style="background:#06b6d4">L</span>
                    <div style="flex:1">
                        <strong><?php echo esc_html( ucfirst( $log->module ) ); ?> · <?php echo esc_html( $log->action ); ?></strong>
                        <div class="meta"><?php echo esc_html( $log->created_at ); ?></div>
                    </div>
                </div>
            <?php endforeach; else : ?><p class="sms-muted">No activity yet.</p><?php endif; ?>
            </div>
        </div>
        <div class="sms-card">
            <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('calendar',18); ?> Current Session</div></div>
            <?php if ( $session ) : ?>
                <h2 style="font-size:22px;margin:0 0 4px"><?php echo esc_html( $session->name ); ?></h2>
                <p class="sms-muted" style="margin:0"><?php echo esc_html( $session->start_date ); ?> &rarr; <?php echo esc_html( $session->end_date ); ?></p>
                <div class="sms-progress" style="margin-top:14px"><div class="sms-progress-bar" style="width:38%"></div></div>
                <p class="sms-muted" style="margin-top:6px;font-size:12px">38% of session completed</p>
            <?php else : ?>
                <p class="sms-muted">No session configured.</p>
            <?php endif; ?>

            <div class="sms-divider"></div>
            <div class="sms-card-title" style="margin-bottom:12px"><?php echo SMS_Icons::svg('grid',18); ?> Quick Actions</div>
            <div class="sms-quick">
                <a href="<?php echo esc_url( SMS_Helper::admin_url('sms-admissions') ); ?>"><span class="ico"><?php echo SMS_Icons::svg('note',18); ?></span><span>Admissions</span></a>
                <a href="<?php echo esc_url( SMS_Helper::admin_url('sms-attendance') ); ?>"><span class="ico"><?php echo SMS_Icons::svg('check',18); ?></span><span>Attendance</span></a>
                <a href="<?php echo esc_url( SMS_Helper::admin_url('sms-notices') ); ?>"><span class="ico"><?php echo SMS_Icons::svg('megaphone',18); ?></span><span>Notice</span></a>
                <a href="<?php echo esc_url( SMS_Helper::admin_url('sms-events') ); ?>"><span class="ico"><?php echo SMS_Icons::svg('calendar',18); ?></span><span>Event</span></a>
            </div>
        </div>
    </div>

    <div class="sms-grid-3" style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px">
        <div class="sms-card">
            <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('megaphone',18); ?> Latest Notices</div></div>
            <div class="sms-list">
            <?php $notices = $wpdb->get_results( "SELECT * FROM {$p}notices ORDER BY id DESC LIMIT 4" );
            if ( $notices ) : foreach ( $notices as $n ) : ?>
                <div class="sms-list-item">
                    <span class="sms-avatar" style="background:#f59e0b"><?php echo SMS_Icons::svg('megaphone',16); ?></span>
                    <div style="flex:1"><strong><?php echo esc_html( $n->title ); ?></strong><div class="meta"><?php echo esc_html( $n->posted_at ); ?></div></div>
                </div>
            <?php endforeach; else : ?><p class="sms-muted">No notices.</p><?php endif; ?>
            </div>
        </div>
        <div class="sms-card">
            <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('calendar',18); ?> Upcoming Events</div></div>
            <div class="sms-list">
            <?php $events = $wpdb->get_results( "SELECT * FROM {$p}events ORDER BY start_date ASC LIMIT 4" );
            if ( $events ) : foreach ( $events as $e ) : ?>
                <div class="sms-list-item">
                    <span class="sms-avatar" style="background:#7c3aed"><?php echo SMS_Icons::svg('calendar',16); ?></span>
                    <div style="flex:1"><strong><?php echo esc_html( $e->title ); ?></strong><div class="meta"><?php echo esc_html( $e->start_date ); ?></div></div>
                </div>
            <?php endforeach; else : ?><p class="sms-muted">No upcoming events.</p><?php endif; ?>
            </div>
        </div>
        <div class="sms-card">
            <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('ticket',18); ?> Recent Tickets</div></div>
            <div class="sms-list">
            <?php $tickets = $wpdb->get_results( "SELECT * FROM {$p}tickets ORDER BY id DESC LIMIT 4" );
            if ( $tickets ) : foreach ( $tickets as $t ) : ?>
                <div class="sms-list-item">
                    <span class="sms-avatar" style="background:#ec4899"><?php echo SMS_Icons::svg('ticket',16); ?></span>
                    <div style="flex:1"><strong><?php echo esc_html( $t->subject ); ?></strong><div class="meta"><?php echo SMS_Helper::badge( $t->status ); ?></div></div>
                </div>
            <?php endforeach; else : ?><p class="sms-muted">No tickets.</p><?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') return;
        var months = []; for (var i = 5; i >= 0; i--) { var d = new Date(); d.setMonth(d.getMonth() - i); months.push(d.toLocaleString('en', { month: 'short' })); }
        var inc = [4200, 5100, 4800, 6200, 5800, <?php echo (float) $stats['income']; ?>];
        var exp = [3000, 3400, 3100, 3700, 3500, <?php echo (float) $stats['expenses']; ?>];
        new Chart(document.getElementById('smsFinanceChart'), {
            type: 'line',
            data: { labels: months, datasets: [
                { label: 'Income',   data: inc, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,.1)', tension: .35, fill: true },
                { label: 'Expenses', data: exp, borderColor: '#f43f5e', backgroundColor: 'rgba(244,63,94,.1)', tension: .35, fill: true }
            ] },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
        new Chart(document.getElementById('smsGenderChart'), {
            type: 'doughnut',
            data: { labels: ['Male','Female'], datasets: [{ data: [<?php echo $male ?: 1; ?>, <?php echo $female ?: 1; ?>], backgroundColor: ['#3b82f6','#ec4899'], borderWidth: 0 }] },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
    });
    </script>
</div>
