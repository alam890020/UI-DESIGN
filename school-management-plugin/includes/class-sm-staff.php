<?php
if (!defined('ABSPATH')) {
    exit;
}

class SM_Staff {

    public function __construct() {
        add_action('wp_ajax_sm_save_staff', array($this, 'save_staff'));
        add_action('wp_ajax_sm_delete_staff', array($this, 'delete_staff'));
    }

    public static function render() {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $id = isset($_GET['staff_id']) ? intval($_GET['staff_id']) : 0;

        if ($action === 'add' || $action === 'edit') {
            $staff = null;
            if ($action === 'edit' && $id) {
                $staff = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}staff WHERE id=%d", $id));
            }
            self::render_form($staff);
        } else {
            self::render_list();
        }
    }

    private static function render_list() {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $where = "WHERE 1=1";
        if ($search) {
            $where .= $wpdb->prepare(" AND (first_name LIKE %s OR last_name LIKE %s OR staff_id LIKE %s)", '%'.$search.'%', '%'.$search.'%', '%'.$search.'%');
        }
        $staff_list = $wpdb->get_results("SELECT * FROM {$prefix}staff {$where} ORDER BY created_at DESC");
        ?>
        <div class="wrap">
            <h1>
                <?php esc_html_e('Staff', 'school-management'); ?>
                <a href="<?php echo admin_url('admin.php?page=sm-staff&action=add'); ?>" class="page-title-action"><?php esc_html_e('Add New', 'school-management'); ?></a>
            </h1>
            <div class="sm-filters">
                <form method="get">
                    <input type="hidden" name="page" value="sm-staff">
                    <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Search...', 'school-management'); ?>">
                    <button type="submit" class="button"><?php esc_html_e('Search', 'school-management'); ?></button>
                </form>
            </div>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php esc_html_e('Staff ID', 'school-management'); ?></th>
                        <th><?php esc_html_e('Name', 'school-management'); ?></th>
                        <th><?php esc_html_e('Designation', 'school-management'); ?></th>
                        <th><?php esc_html_e('Department', 'school-management'); ?></th>
                        <th><?php esc_html_e('Phone', 'school-management'); ?></th>
                        <th><?php esc_html_e('Status', 'school-management'); ?></th>
                        <th><?php esc_html_e('Actions', 'school-management'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($staff_list): $i=1; foreach ($staff_list as $s): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo esc_html($s->staff_id); ?></td>
                        <td><?php echo esc_html($s->first_name . ' ' . $s->last_name); ?></td>
                        <td><?php echo esc_html($s->designation ?: '-'); ?></td>
                        <td><?php echo esc_html($s->department ?: '-'); ?></td>
                        <td><?php echo esc_html($s->phone ?: '-'); ?></td>
                        <td><span class="sm-badge sm-badge-<?php echo esc_attr($s->status); ?>"><?php echo esc_html(ucfirst($s->status)); ?></span></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=sm-staff&action=edit&staff_id=' . $s->id); ?>" class="button button-small"><?php esc_html_e('Edit', 'school-management'); ?></a>
                            <button class="button button-small sm-delete-btn" data-id="<?php echo $s->id; ?>" data-type="staff"><?php esc_html_e('Delete', 'school-management'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="8"><?php esc_html_e('No staff found.', 'school-management'); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }


    private static function render_form($staff = null) {
        $is_edit = !empty($staff);
        ?>
        <div class="wrap">
            <h1><?php echo $is_edit ? esc_html__('Edit Staff', 'school-management') : esc_html__('Add New Staff', 'school-management'); ?></h1>
            <form method="post" id="sm-staff-form" class="sm-form">
                <?php wp_nonce_field('sm_staff_nonce', 'sm_nonce'); ?>
                <input type="hidden" name="id" value="<?php echo $is_edit ? $staff->id : ''; ?>">
                <div class="sm-form-grid">
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Staff ID *', 'school-management'); ?></label>
                        <input type="text" name="staff_id" value="<?php echo $is_edit ? esc_attr($staff->staff_id) : ''; ?>" required>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('First Name *', 'school-management'); ?></label>
                        <input type="text" name="first_name" value="<?php echo $is_edit ? esc_attr($staff->first_name) : ''; ?>" required>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Last Name *', 'school-management'); ?></label>
                        <input type="text" name="last_name" value="<?php echo $is_edit ? esc_attr($staff->last_name) : ''; ?>" required>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Email', 'school-management'); ?></label>
                        <input type="email" name="email" value="<?php echo $is_edit ? esc_attr($staff->email) : ''; ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Phone', 'school-management'); ?></label>
                        <input type="text" name="phone" value="<?php echo $is_edit ? esc_attr($staff->phone) : ''; ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Designation', 'school-management'); ?></label>
                        <input type="text" name="designation" value="<?php echo $is_edit ? esc_attr($staff->designation) : ''; ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Department', 'school-management'); ?></label>
                        <input type="text" name="department" value="<?php echo $is_edit ? esc_attr($staff->department) : ''; ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Gender', 'school-management'); ?></label>
                        <select name="gender">
                            <option value="male" <?php echo $is_edit ? selected($staff->gender, 'male', false) : ''; ?>><?php esc_html_e('Male', 'school-management'); ?></option>
                            <option value="female" <?php echo $is_edit ? selected($staff->gender, 'female', false) : ''; ?>><?php esc_html_e('Female', 'school-management'); ?></option>
                            <option value="other" <?php echo $is_edit ? selected($staff->gender, 'other', false) : ''; ?>><?php esc_html_e('Other', 'school-management'); ?></option>
                        </select>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Date of Birth', 'school-management'); ?></label>
                        <input type="date" name="date_of_birth" value="<?php echo $is_edit ? esc_attr($staff->date_of_birth) : ''; ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Joining Date', 'school-management'); ?></label>
                        <input type="date" name="joining_date" value="<?php echo $is_edit ? esc_attr($staff->joining_date) : date('Y-m-d'); ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Salary', 'school-management'); ?></label>
                        <input type="number" step="0.01" name="salary" value="<?php echo $is_edit ? esc_attr($staff->salary) : '0'; ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Status', 'school-management'); ?></label>
                        <select name="status">
                            <option value="active" <?php echo $is_edit ? selected($staff->status, 'active', false) : ''; ?>><?php esc_html_e('Active', 'school-management'); ?></option>
                            <option value="inactive" <?php echo $is_edit ? selected($staff->status, 'inactive', false) : ''; ?>><?php esc_html_e('Inactive', 'school-management'); ?></option>
                            <option value="resigned" <?php echo $is_edit ? selected($staff->status, 'resigned', false) : ''; ?>><?php esc_html_e('Resigned', 'school-management'); ?></option>
                        </select>
                    </div>
                    <div class="sm-form-group sm-form-full">
                        <label><?php esc_html_e('Address', 'school-management'); ?></label>
                        <textarea name="address" rows="3"><?php echo $is_edit ? esc_textarea($staff->address) : ''; ?></textarea>
                    </div>
                </div>
                <p class="submit">
                    <button type="submit" class="button button-primary" id="sm-save-staff"><?php echo $is_edit ? esc_html__('Update Staff', 'school-management') : esc_html__('Add Staff', 'school-management'); ?></button>
                    <a href="<?php echo admin_url('admin.php?page=sm-staff'); ?>" class="button"><?php esc_html_e('Cancel', 'school-management'); ?></a>
                </p>
            </form>
        </div>
        <?php
    }

    public function save_staff() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $id = intval($_POST['id']);
        $data = array(
            'staff_id' => sanitize_text_field($_POST['staff_id']),
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name' => sanitize_text_field($_POST['last_name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'designation' => sanitize_text_field($_POST['designation']),
            'department' => sanitize_text_field($_POST['department']),
            'gender' => sanitize_text_field($_POST['gender']),
            'date_of_birth' => sanitize_text_field($_POST['date_of_birth']),
            'joining_date' => sanitize_text_field($_POST['joining_date']),
            'salary' => floatval($_POST['salary']),
            'address' => sanitize_textarea_field($_POST['address']),
            'status' => sanitize_text_field($_POST['status']),
        );
        if ($id) {
            $wpdb->update("{$prefix}staff", $data, array('id' => $id));
            wp_send_json_success(array('message' => __('Staff updated successfully.', 'school-management')));
        } else {
            $wpdb->insert("{$prefix}staff", $data);
            wp_send_json_success(array('message' => __('Staff added successfully.', 'school-management')));
        }
    }

    public function delete_staff() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $wpdb->delete("{$prefix}staff", array('id' => intval($_POST['id'])));
        wp_send_json_success(array('message' => __('Staff deleted.', 'school-management')));
    }
}

new SM_Staff();
