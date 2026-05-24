<?php
if (!defined('ABSPATH')) {
    exit;
}

class SM_Dashboard {

    public static function render() {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $total_students = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}students WHERE status='active'");
        $total_staff = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}staff WHERE status='active'");
        $total_classes = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}classes WHERE status=1");
        $total_fees_pending = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}fees WHERE status IN ('pending','overdue')");
        $total_notices = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}notices WHERE status=1");

        $recent_students = $wpdb->get_results("SELECT * FROM {$prefix}students ORDER BY created_at DESC LIMIT 5");
        $recent_notices = $wpdb->get_results("SELECT * FROM {$prefix}notices WHERE status=1 ORDER BY created_at DESC LIMIT 5");
        ?>
        <div class="wrap sm-dashboard">
            <h1><?php esc_html_e('School Management Dashboard', 'school-management'); ?></h1>

            <div class="sm-stats-grid">
                <div class="sm-stat-card sm-stat-blue">
                    <div class="sm-stat-icon"><span class="dashicons dashicons-groups"></span></div>
                    <div class="sm-stat-info">
                        <h3><?php echo intval($total_students); ?></h3>
                        <p><?php esc_html_e('Total Students', 'school-management'); ?></p>
                    </div>
                </div>
                <div class="sm-stat-card sm-stat-green">
                    <div class="sm-stat-icon"><span class="dashicons dashicons-businessman"></span></div>
                    <div class="sm-stat-info">
                        <h3><?php echo intval($total_staff); ?></h3>
                        <p><?php esc_html_e('Total Staff', 'school-management'); ?></p>
                    </div>
                </div>
                <div class="sm-stat-card sm-stat-orange">
                    <div class="sm-stat-icon"><span class="dashicons dashicons-welcome-learn-more"></span></div>
                    <div class="sm-stat-info">
                        <h3><?php echo intval($total_classes); ?></h3>
                        <p><?php esc_html_e('Total Classes', 'school-management'); ?></p>
                    </div>
                </div>
                <div class="sm-stat-card sm-stat-red">
                    <div class="sm-stat-icon"><span class="dashicons dashicons-money-alt"></span></div>
                    <div class="sm-stat-info">
                        <h3><?php echo intval($total_fees_pending); ?></h3>
                        <p><?php esc_html_e('Pending Fees', 'school-management'); ?></p>
                    </div>
                </div>
                <div class="sm-stat-card sm-stat-purple">
                    <div class="sm-stat-icon"><span class="dashicons dashicons-megaphone"></span></div>
                    <div class="sm-stat-info">
                        <h3><?php echo intval($total_notices); ?></h3>
                        <p><?php esc_html_e('Active Notices', 'school-management'); ?></p>
                    </div>
                </div>
            </div>

            <div class="sm-dashboard-widgets">
                <div class="sm-widget">
                    <h2><?php esc_html_e('Recent Students', 'school-management'); ?></h2>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Admission No', 'school-management'); ?></th>
                                <th><?php esc_html_e('Name', 'school-management'); ?></th>
                                <th><?php esc_html_e('Status', 'school-management'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_students): ?>
                                <?php foreach ($recent_students as $student): ?>
                                    <tr>
                                        <td><?php echo esc_html($student->admission_no); ?></td>
                                        <td><?php echo esc_html($student->first_name . ' ' . $student->last_name); ?></td>
                                        <td><span class="sm-badge sm-badge-<?php echo esc_attr($student->status); ?>"><?php echo esc_html(ucfirst($student->status)); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3"><?php esc_html_e('No students found.', 'school-management'); ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="sm-widget">
                    <h2><?php esc_html_e('Recent Notices', 'school-management'); ?></h2>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Title', 'school-management'); ?></th>
                                <th><?php esc_html_e('Type', 'school-management'); ?></th>
                                <th><?php esc_html_e('Date', 'school-management'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_notices): ?>
                                <?php foreach ($recent_notices as $notice): ?>
                                    <tr>
                                        <td><?php echo esc_html($notice->title); ?></td>
                                        <td><span class="sm-badge"><?php echo esc_html(ucfirst($notice->type)); ?></span></td>
                                        <td><?php echo esc_html(date('M d, Y', strtotime($notice->created_at))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3"><?php esc_html_e('No notices found.', 'school-management'); ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }
}
