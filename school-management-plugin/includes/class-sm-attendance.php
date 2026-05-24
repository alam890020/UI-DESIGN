<?php
if (!defined('ABSPATH')) {
    exit;
}

class SM_Attendance {

    public function __construct() {
        add_action('wp_ajax_sm_save_attendance', array($this, 'save_attendance'));
        add_action('wp_ajax_sm_get_attendance', array($this, 'get_attendance'));
    }

    public static function render() {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $classes = $wpdb->get_results("SELECT * FROM {$prefix}classes WHERE status=1 ORDER BY name");
        $sections = $wpdb->get_results("SELECT * FROM {$prefix}sections WHERE status=1 ORDER BY name");

        $sel_class = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
        $sel_section = isset($_GET['section_id']) ? intval($_GET['section_id']) : 0;
        $sel_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : date('Y-m-d');

        $students = array();
        $attendance_data = array();
        if ($sel_class) {
            $where = $wpdb->prepare("WHERE class_id=%d AND status='active'", $sel_class);
            if ($sel_section) {
                $where .= $wpdb->prepare(" AND section_id=%d", $sel_section);
            }
            $students = $wpdb->get_results("SELECT * FROM {$prefix}students {$where} ORDER BY first_name");

            if ($students) {
                $att_records = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM {$prefix}attendance WHERE class_id=%d AND date=%s",
                    $sel_class, $sel_date
                ));
                foreach ($att_records as $rec) {
                    $attendance_data[$rec->student_id] = $rec->status;
                }
            }
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Attendance', 'school-management'); ?></h1>

            <form method="get" class="sm-filters">
                <input type="hidden" name="page" value="sm-attendance">
                <select name="class_id" required>
                    <option value=""><?php esc_html_e('Select Class', 'school-management'); ?></option>
                    <?php foreach ($classes as $class): ?>
                    <option value="<?php echo $class->id; ?>" <?php selected($sel_class, $class->id); ?>><?php echo esc_html($class->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="section_id">
                    <option value=""><?php esc_html_e('All Sections', 'school-management'); ?></option>
                    <?php foreach ($sections as $sec): ?>
                    <option value="<?php echo $sec->id; ?>" <?php selected($sel_section, $sec->id); ?>><?php echo esc_html($sec->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="date" name="date" value="<?php echo esc_attr($sel_date); ?>" required>
                <button type="submit" class="button button-primary"><?php esc_html_e('Load Students', 'school-management'); ?></button>
            </form>


            <?php if ($sel_class && $students): ?>
            <form id="sm-attendance-form">
                <?php wp_nonce_field('sm_nonce', 'sm_att_nonce'); ?>
                <input type="hidden" name="class_id" value="<?php echo $sel_class; ?>">
                <input type="hidden" name="section_id" value="<?php echo $sel_section; ?>">
                <input type="hidden" name="date" value="<?php echo esc_attr($sel_date); ?>">

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th><?php esc_html_e('Student', 'school-management'); ?></th>
                            <th><?php esc_html_e('Admission No', 'school-management'); ?></th>
                            <th><?php esc_html_e('Present', 'school-management'); ?></th>
                            <th><?php esc_html_e('Absent', 'school-management'); ?></th>
                            <th><?php esc_html_e('Late', 'school-management'); ?></th>
                            <th><?php esc_html_e('Half Day', 'school-management'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; foreach ($students as $student):
                        $status = isset($attendance_data[$student->id]) ? $attendance_data[$student->id] : 'present';
                    ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo esc_html($student->first_name . ' ' . $student->last_name); ?></td>
                            <td><?php echo esc_html($student->admission_no); ?></td>
                            <td><input type="radio" name="attendance[<?php echo $student->id; ?>]" value="present" <?php checked($status, 'present'); ?>></td>
                            <td><input type="radio" name="attendance[<?php echo $student->id; ?>]" value="absent" <?php checked($status, 'absent'); ?>></td>
                            <td><input type="radio" name="attendance[<?php echo $student->id; ?>]" value="late" <?php checked($status, 'late'); ?>></td>
                            <td><input type="radio" name="attendance[<?php echo $student->id; ?>]" value="half_day" <?php checked($status, 'half_day'); ?>></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary" id="sm-save-attendance"><?php esc_html_e('Save Attendance', 'school-management'); ?></button>
                </p>
            </form>
            <?php elseif ($sel_class && empty($students)): ?>
                <p><?php esc_html_e('No students found in this class.', 'school-management'); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    public function save_attendance() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $class_id = intval($_POST['class_id']);
        $section_id = intval($_POST['section_id']);
        $date = sanitize_text_field($_POST['date']);
        $attendance = isset($_POST['attendance']) ? $_POST['attendance'] : array();

        // Delete existing attendance for this class/date
        $wpdb->delete("{$prefix}attendance", array('class_id' => $class_id, 'date' => $date));

        foreach ($attendance as $student_id => $status) {
            $wpdb->insert("{$prefix}attendance", array(
                'student_id' => intval($student_id),
                'class_id' => $class_id,
                'section_id' => $section_id,
                'date' => $date,
                'status' => sanitize_text_field($status),
            ));
        }
        wp_send_json_success(array('message' => __('Attendance saved successfully.', 'school-management')));
    }

    public function get_attendance() {
        check_ajax_referer('sm_nonce', 'nonce');
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $class_id = intval($_POST['class_id']);
        $date = sanitize_text_field($_POST['date']);
        $records = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$prefix}attendance WHERE class_id=%d AND date=%s", $class_id, $date
        ));
        wp_send_json_success($records);
    }
}

new SM_Attendance();
