<?php
if (!defined('ABSPATH')) {
    exit;
}

class SM_Students {

    public function __construct() {
        add_action('wp_ajax_sm_save_student', array($this, 'save_student'));
        add_action('wp_ajax_sm_delete_student', array($this, 'delete_student'));
    }

    public static function render() {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;

        $classes = $wpdb->get_results("SELECT * FROM {$prefix}classes WHERE status=1 ORDER BY name");
        $sections = $wpdb->get_results("SELECT * FROM {$prefix}sections WHERE status=1 ORDER BY name");

        if ($action === 'add' || $action === 'edit') {
            $student = null;
            if ($action === 'edit' && $id) {
                $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}students WHERE id=%d", $id));
            }
            self::render_form($student, $classes, $sections);
        } else {
            self::render_list($classes);
        }
    }

    private static function render_list($classes) {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $filter_class = isset($_GET['filter_class']) ? intval($_GET['filter_class']) : 0;
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

        $where = "WHERE 1=1";
        if ($filter_class) {
            $where .= $wpdb->prepare(" AND s.class_id=%d", $filter_class);
        }
        if ($search) {
            $where .= $wpdb->prepare(" AND (s.first_name LIKE %s OR s.last_name LIKE %s OR s.admission_no LIKE %s)", '%' . $search . '%', '%' . $search . '%', '%' . $search . '%');
        }

        $students = $wpdb->get_results("SELECT s.*, c.name as class_name FROM {$prefix}students s LEFT JOIN {$prefix}classes c ON s.class_id = c.id $where ORDER BY s.created_at DESC");
        ?>
        <div class="wrap">
            <h1>
                <?php esc_html_e('Students', 'school-management'); ?>
                <a href="<?php echo admin_url('admin.php?page=sm-students&action=add'); ?>" class="page-title-action"><?php esc_html_e('Add New', 'school-management'); ?></a>
            </h1>

            <div class="sm-filters">
                <form method="get">
                    <input type="hidden" name="page" value="sm-students">
                    <select name="filter_class">
                        <option value=""><?php esc_html_e('All Classes', 'school-management'); ?></option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class->id; ?>" <?php selected($filter_class, $class->id); ?>><?php echo esc_html($class->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Search...', 'school-management'); ?>">
                    <button type="submit" class="button"><?php esc_html_e('Filter', 'school-management'); ?></button>
                </form>
            </div>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th><?php esc_html_e('Admission No', 'school-management'); ?></th>
                        <th><?php esc_html_e('Name', 'school-management'); ?></th>
                        <th><?php esc_html_e('Class', 'school-management'); ?></th>
                        <th><?php esc_html_e('Phone', 'school-management'); ?></th>
                        <th><?php esc_html_e('Gender', 'school-management'); ?></th>
                        <th><?php esc_html_e('Status', 'school-management'); ?></th>
                        <th><?php esc_html_e('Actions', 'school-management'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students): $i = 1; ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo esc_html($student->admission_no); ?></td>
                                <td><?php echo esc_html($student->first_name . ' ' . $student->last_name); ?></td>
                                <td><?php echo esc_html($student->class_name ?: '-'); ?></td>
                                <td><?php echo esc_html($student->phone ?: '-'); ?></td>
                                <td><?php echo esc_html(ucfirst($student->gender)); ?></td>
                                <td><span class="sm-badge sm-badge-<?php echo esc_attr($student->status); ?>"><?php echo esc_html(ucfirst($student->status)); ?></span></td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=sm-students&action=edit&student_id=' . $student->id); ?>" class="button button-small"><?php esc_html_e('Edit', 'school-management'); ?></a>
                                    <button class="button button-small sm-delete-btn" data-id="<?php echo $student->id; ?>" data-type="student"><?php esc_html_e('Delete', 'school-management'); ?></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8"><?php esc_html_e('No students found.', 'school-management'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    private static function render_form($student = null, $classes = array(), $sections = array()) {
        $is_edit = !empty($student);
        ?>
        <div class="wrap">
            <h1><?php echo $is_edit ? esc_html__('Edit Student', 'school-management') : esc_html__('Add New Student', 'school-management'); ?></h1>
            <form method="post" id="sm-student-form" class="sm-form" enctype="multipart/form-data">
                <?php wp_nonce_field('sm_student_nonce', 'sm_nonce'); ?>
                <input type="hidden" name="student_id" value="<?php echo $is_edit ? $student->id : ''; ?>">

                <div class="sm-form-grid">
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Admission No *', 'school-management'); ?></label>
                        <input type="text" name="admission_no" value="<?php echo $is_edit ? esc_attr($student->admission_no) : ''; ?>" required>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('First Name *', 'school-management'); ?></label>
                        <input type="text" name="first_name" value="<?php echo $is_edit ? esc_attr($student->first_name) : ''; ?>" required>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Last Name *', 'school-management'); ?></label>
                        <input type="text" name="last_name" value="<?php echo $is_edit ? esc_attr($student->last_name) : ''; ?>" required>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Email', 'school-management'); ?></label>
                        <input type="email" name="email" value="<?php echo $is_edit ? esc_attr($student->email) : ''; ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Phone', 'school-management'); ?></label>
                        <input type="text" name="phone" value="<?php echo $is_edit ? esc_attr($student->phone) : ''; ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Date of Birth', 'school-management'); ?></label>
                        <input type="date" name="date_of_birth" value="<?php echo $is_edit ? esc_attr($student->date_of_birth) : ''; ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Gender', 'school-management'); ?></label>
                        <select name="gender">
                            <option value="male" <?php echo $is_edit ? selected($student->gender, 'male', false) : ''; ?>><?php esc_html_e('Male', 'school-management'); ?></option>
                            <option value="female" <?php echo $is_edit ? selected($student->gender, 'female', false) : ''; ?>><?php esc_html_e('Female', 'school-management'); ?></option>
                            <option value="other" <?php echo $is_edit ? selected($student->gender, 'other', false) : ''; ?>><?php esc_html_e('Other', 'school-management'); ?></option>
                        </select>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Class', 'school-management'); ?></label>
                        <select name="class_id">
                            <option value=""><?php esc_html_e('Select Class', 'school-management'); ?></option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class->id; ?>" <?php echo $is_edit ? selected($student->class_id, $class->id, false) : ''; ?>><?php echo esc_html($class->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Section', 'school-management'); ?></label>
                        <select name="section_id">
                            <option value=""><?php esc_html_e('Select Section', 'school-management'); ?></option>
                            <?php foreach ($sections as $section): ?>
                                <option value="<?php echo $section->id; ?>" <?php echo $is_edit ? selected($student->section_id, $section->id, false) : ''; ?>><?php echo esc_html($section->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Admission Date', 'school-management'); ?></label>
                        <input type="date" name="admission_date" value="<?php echo $is_edit ? esc_attr($student->admission_date) : date('Y-m-d'); ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Parent/Guardian Name', 'school-management'); ?></label>
                        <input type="text" name="parent_name" value="<?php echo $is_edit ? esc_attr($student->parent_name) : ''; ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Parent Phone', 'school-management'); ?></label>
                        <input type="text" name="parent_phone" value="<?php echo $is_edit ? esc_attr($student->parent_phone) : ''; ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Parent Email', 'school-management'); ?></label>
                        <input type="email" name="parent_email" value="<?php echo $is_edit ? esc_attr($student->parent_email) : ''; ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Status', 'school-management'); ?></label>
                        <select name="status">
                            <option value="active" <?php echo $is_edit ? selected($student->status, 'active', false) : ''; ?>><?php esc_html_e('Active', 'school-management'); ?></option>
                            <option value="inactive" <?php echo $is_edit ? selected($student->status, 'inactive', false) : ''; ?>><?php esc_html_e('Inactive', 'school-management'); ?></option>
                            <option value="graduated" <?php echo $is_edit ? selected($student->status, 'graduated', false) : ''; ?>><?php esc_html_e('Graduated', 'school-management'); ?></option>
                            <option value="transferred" <?php echo $is_edit ? selected($student->status, 'transferred', false) : ''; ?>><?php esc_html_e('Transferred', 'school-management'); ?></option>
                        </select>
                    </div>
                    <div class="sm-form-group sm-form-full">
                        <label><?php esc_html_e('Address', 'school-management'); ?></label>
                        <textarea name="address" rows="3"><?php echo $is_edit ? esc_textarea($student->address) : ''; ?></textarea>
                    </div>
                </div>

                <p class="submit">
                    <button type="submit" class="button button-primary" id="sm-save-student"><?php echo $is_edit ? esc_html__('Update Student', 'school-management') : esc_html__('Add Student', 'school-management'); ?></button>
                    <a href="<?php echo admin_url('admin.php?page=sm-students'); ?>" class="button"><?php esc_html_e('Cancel', 'school-management'); ?></a>
                </p>
            </form>
        </div>
        <?php
    }

    public function save_student() {
        check_ajax_referer('sm_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $id = intval($_POST['student_id']);
        $data = array(
            'admission_no' => sanitize_text_field($_POST['admission_no']),
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name' => sanitize_text_field($_POST['last_name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'date_of_birth' => sanitize_text_field($_POST['date_of_birth']),
            'gender' => sanitize_text_field($_POST['gender']),
            'class_id' => intval($_POST['class_id']),
            'section_id' => intval($_POST['section_id']),
            'address' => sanitize_textarea_field($_POST['address']),
            'parent_name' => sanitize_text_field($_POST['parent_name']),
            'parent_phone' => sanitize_text_field($_POST['parent_phone']),
            'parent_email' => sanitize_email($_POST['parent_email']),
            'admission_date' => sanitize_text_field($_POST['admission_date']),
            'status' => sanitize_text_field($_POST['status']),
        );

        if ($id) {
            $wpdb->update("{$prefix}students", $data, array('id' => $id));
            wp_send_json_success(array('message' => __('Student updated successfully.', 'school-management')));
        } else {
            $wpdb->insert("{$prefix}students", $data);
            wp_send_json_success(array('message' => __('Student added successfully.', 'school-management')));
        }
    }

    public function delete_student() {
        check_ajax_referer('sm_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $id = intval($_POST['id']);

        $wpdb->delete("{$prefix}students", array('id' => $id));
        wp_send_json_success(array('message' => __('Student deleted successfully.', 'school-management')));
    }
}

new SM_Students();
