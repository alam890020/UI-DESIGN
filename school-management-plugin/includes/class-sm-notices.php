<?php
if (!defined('ABSPATH')) {
    exit;
}

class SM_Notices {

    public function __construct() {
        add_action('wp_ajax_sm_save_notice', array($this, 'save_notice'));
        add_action('wp_ajax_sm_delete_notice', array($this, 'delete_notice'));
    }

    public static function render() {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $id = isset($_GET['notice_id']) ? intval($_GET['notice_id']) : 0;

        if ($action === 'add' || $action === 'edit') {
            $notice = null;
            if ($action === 'edit' && $id) {
                $notice = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}notices WHERE id=%d", $id));
            }
            self::render_form($notice);
        } else {
            self::render_list();
        }
    }

    private static function render_list() {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $notices = $wpdb->get_results("SELECT * FROM {$prefix}notices ORDER BY created_at DESC");
        ?>
        <div class="wrap">
            <h1>
                <?php esc_html_e('Notices & Events', 'school-management'); ?>
                <a href="<?php echo admin_url('admin.php?page=sm-notices&action=add'); ?>" class="page-title-action"><?php esc_html_e('Add New', 'school-management'); ?></a>
            </h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php esc_html_e('Title', 'school-management'); ?></th>
                        <th><?php esc_html_e('Type', 'school-management'); ?></th>
                        <th><?php esc_html_e('Target', 'school-management'); ?></th>
                        <th><?php esc_html_e('Start Date', 'school-management'); ?></th>
                        <th><?php esc_html_e('End Date', 'school-management'); ?></th>
                        <th><?php esc_html_e('Status', 'school-management'); ?></th>
                        <th><?php esc_html_e('Actions', 'school-management'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($notices): $i=1; foreach ($notices as $n): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo esc_html($n->title); ?></td>
                        <td><span class="sm-badge"><?php echo esc_html(ucfirst($n->type)); ?></span></td>
                        <td><?php echo esc_html(ucfirst($n->target)); ?></td>
                        <td><?php echo $n->start_date ? date('M d, Y', strtotime($n->start_date)) : '-'; ?></td>
                        <td><?php echo $n->end_date ? date('M d, Y', strtotime($n->end_date)) : '-'; ?></td>
                        <td><?php echo $n->status ? __('Active','school-management') : __('Inactive','school-management'); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=sm-notices&action=edit&notice_id=' . $n->id); ?>" class="button button-small"><?php esc_html_e('Edit', 'school-management'); ?></a>
                            <button class="button button-small sm-delete-btn" data-id="<?php echo $n->id; ?>" data-type="notice"><?php esc_html_e('Delete', 'school-management'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="8"><?php esc_html_e('No notices found.', 'school-management'); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }


    private static function render_form($notice = null) {
        $is_edit = !empty($notice);
        ?>
        <div class="wrap">
            <h1><?php echo $is_edit ? esc_html__('Edit Notice', 'school-management') : esc_html__('Add New Notice', 'school-management'); ?></h1>
            <form method="post" id="sm-notice-form" class="sm-form">
                <?php wp_nonce_field('sm_notice_nonce', 'sm_nonce'); ?>
                <input type="hidden" name="notice_id" value="<?php echo $is_edit ? $notice->id : ''; ?>">
                <div class="sm-form-grid">
                    <div class="sm-form-group sm-form-full">
                        <label><?php esc_html_e('Title *', 'school-management'); ?></label>
                        <input type="text" name="title" value="<?php echo $is_edit ? esc_attr($notice->title) : ''; ?>" required>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Type', 'school-management'); ?></label>
                        <select name="type">
                            <option value="general" <?php echo $is_edit ? selected($notice->type, 'general', false) : ''; ?>><?php esc_html_e('General', 'school-management'); ?></option>
                            <option value="event" <?php echo $is_edit ? selected($notice->type, 'event', false) : ''; ?>><?php esc_html_e('Event', 'school-management'); ?></option>
                            <option value="holiday" <?php echo $is_edit ? selected($notice->type, 'holiday', false) : ''; ?>><?php esc_html_e('Holiday', 'school-management'); ?></option>
                            <option value="exam" <?php echo $is_edit ? selected($notice->type, 'exam', false) : ''; ?>><?php esc_html_e('Exam', 'school-management'); ?></option>
                        </select>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Target Audience', 'school-management'); ?></label>
                        <select name="target">
                            <option value="all" <?php echo $is_edit ? selected($notice->target, 'all', false) : ''; ?>><?php esc_html_e('All', 'school-management'); ?></option>
                            <option value="students" <?php echo $is_edit ? selected($notice->target, 'students', false) : ''; ?>><?php esc_html_e('Students', 'school-management'); ?></option>
                            <option value="staff" <?php echo $is_edit ? selected($notice->target, 'staff', false) : ''; ?>><?php esc_html_e('Staff', 'school-management'); ?></option>
                            <option value="parents" <?php echo $is_edit ? selected($notice->target, 'parents', false) : ''; ?>><?php esc_html_e('Parents', 'school-management'); ?></option>
                        </select>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Start Date', 'school-management'); ?></label>
                        <input type="date" name="start_date" value="<?php echo $is_edit ? esc_attr($notice->start_date) : date('Y-m-d'); ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('End Date', 'school-management'); ?></label>
                        <input type="date" name="end_date" value="<?php echo $is_edit ? esc_attr($notice->end_date) : ''; ?>">
                    </div>
                    <div class="sm-form-group sm-form-full">
                        <label><?php esc_html_e('Content', 'school-management'); ?></label>
                        <textarea name="content" rows="5"><?php echo $is_edit ? esc_textarea($notice->content) : ''; ?></textarea>
                    </div>
                </div>
                <p class="submit">
                    <button type="submit" class="button button-primary" id="sm-save-notice"><?php echo $is_edit ? esc_html__('Update Notice', 'school-management') : esc_html__('Add Notice', 'school-management'); ?></button>
                    <a href="<?php echo admin_url('admin.php?page=sm-notices'); ?>" class="button"><?php esc_html_e('Cancel', 'school-management'); ?></a>
                </p>
            </form>
        </div>
        <?php
    }

    public function save_notice() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $id = intval($_POST['notice_id']);
        $data = array(
            'title' => sanitize_text_field($_POST['title']),
            'content' => sanitize_textarea_field($_POST['content']),
            'type' => sanitize_text_field($_POST['type']),
            'target' => sanitize_text_field($_POST['target']),
            'start_date' => sanitize_text_field($_POST['start_date']),
            'end_date' => sanitize_text_field($_POST['end_date']),
            'created_by' => get_current_user_id(),
        );
        if ($id) {
            $wpdb->update("{$prefix}notices", $data, array('id' => $id));
            wp_send_json_success(array('message' => __('Notice updated.', 'school-management')));
        } else {
            $wpdb->insert("{$prefix}notices", $data);
            wp_send_json_success(array('message' => __('Notice added.', 'school-management')));
        }
    }

    public function delete_notice() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $wpdb->delete("{$prefix}notices", array('id' => intval($_POST['id'])));
        wp_send_json_success(array('message' => __('Notice deleted.', 'school-management')));
    }
}

new SM_Notices();
