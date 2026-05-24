<?php
if (!defined('ABSPATH')) {
    exit;
}

class SM_Exams {

    public function __construct() {
        add_action('wp_ajax_sm_save_exam', array($this, 'save_exam'));
        add_action('wp_ajax_sm_delete_exam', array($this, 'delete_exam'));
        add_action('wp_ajax_sm_save_exam_subject', array($this, 'save_exam_subject'));
        add_action('wp_ajax_sm_save_marks', array($this, 'save_marks'));
    }

    public static function render() {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'exams';
        $classes = $wpdb->get_results("SELECT * FROM {$prefix}classes WHERE status=1 ORDER BY name");

        echo '<div class="wrap"><h1>' . esc_html__('Exams & Results', 'school-management') . '</h1>';
        echo '<nav class="nav-tab-wrapper">';
        echo '<a href="' . admin_url('admin.php?page=sm-exams&tab=exams') . '" class="nav-tab ' . ($tab === 'exams' ? 'nav-tab-active' : '') . '">' . esc_html__('Exams', 'school-management') . '</a>';
        echo '<a href="' . admin_url('admin.php?page=sm-exams&tab=marks') . '" class="nav-tab ' . ($tab === 'marks' ? 'nav-tab-active' : '') . '">' . esc_html__('Enter Marks', 'school-management') . '</a>';
        echo '<a href="' . admin_url('admin.php?page=sm-exams&tab=results') . '" class="nav-tab ' . ($tab === 'results' ? 'nav-tab-active' : '') . '">' . esc_html__('Results', 'school-management') . '</a>';
        echo '</nav>';

        if ($tab === 'marks') {
            self::render_marks($classes);
        } elseif ($tab === 'results') {
            self::render_results($classes);
        } else {
            self::render_exams($classes);
        }
        echo '</div>';
    }

    private static function render_exams($classes) {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $exams = $wpdb->get_results("SELECT e.*, c.name as class_name FROM {$prefix}exams e LEFT JOIN {$prefix}classes c ON e.class_id=c.id ORDER BY e.created_at DESC");
        ?>
        <div class="sm-section">
            <h2><?php esc_html_e('Create Exam', 'school-management'); ?></h2>
            <form id="sm-exam-form" class="sm-form">
                <?php wp_nonce_field('sm_nonce', 'sm_exam_nonce'); ?>
                <input type="hidden" name="exam_id" value="">
                <div class="sm-form-grid">
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Exam Name *', 'school-management'); ?></label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Class *', 'school-management'); ?></label>
                        <select name="class_id" required>
                            <option value=""><?php esc_html_e('Select Class', 'school-management'); ?></option>
                            <?php foreach ($classes as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo esc_html($c->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Start Date', 'school-management'); ?></label>
                        <input type="date" name="start_date">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('End Date', 'school-management'); ?></label>
                        <input type="date" name="end_date">
                    </div>
                    <div class="sm-form-group sm-form-full">
                        <label><?php esc_html_e('Description', 'school-management'); ?></label>
                        <textarea name="description" rows="2"></textarea>
                    </div>
                </div>
                <button type="submit" class="button button-primary"><?php esc_html_e('Save Exam', 'school-management'); ?></button>
            </form>


            <h3><?php esc_html_e('All Exams', 'school-management'); ?></h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php esc_html_e('Exam Name', 'school-management'); ?></th>
                        <th><?php esc_html_e('Class', 'school-management'); ?></th>
                        <th><?php esc_html_e('Start', 'school-management'); ?></th>
                        <th><?php esc_html_e('End', 'school-management'); ?></th>
                        <th><?php esc_html_e('Status', 'school-management'); ?></th>
                        <th><?php esc_html_e('Actions', 'school-management'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($exams): $i=1; foreach ($exams as $exam): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo esc_html($exam->name); ?></td>
                        <td><?php echo esc_html($exam->class_name); ?></td>
                        <td><?php echo $exam->start_date ? date('M d, Y', strtotime($exam->start_date)) : '-'; ?></td>
                        <td><?php echo $exam->end_date ? date('M d, Y', strtotime($exam->end_date)) : '-'; ?></td>
                        <td><span class="sm-badge"><?php echo esc_html(ucfirst($exam->status)); ?></span></td>
                        <td><button class="button button-small sm-delete-btn" data-id="<?php echo $exam->id; ?>" data-type="exam"><?php esc_html_e('Delete', 'school-management'); ?></button></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="7"><?php esc_html_e('No exams found.', 'school-management'); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    private static function render_marks($classes) {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $sel_exam = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
        $exams = $wpdb->get_results("SELECT * FROM {$prefix}exams ORDER BY created_at DESC");
        $students = array();
        $subjects = array();
        $marks_data = array();

        if ($sel_exam) {
            $exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}exams WHERE id=%d", $sel_exam));
            if ($exam) {
                $students = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}students WHERE class_id=%d AND status='active' ORDER BY first_name", $exam->class_id));
                $subjects = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}exam_subjects WHERE exam_id=%d", $sel_exam));
                $marks = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}marks WHERE exam_id=%d", $sel_exam));
                foreach ($marks as $m) {
                    $marks_data[$m->student_id][$m->exam_subject_id] = $m->marks_obtained;
                }
            }
        }
        ?>
        <div class="sm-section">
            <h2><?php esc_html_e('Enter Marks', 'school-management'); ?></h2>
            <form method="get" class="sm-filters">
                <input type="hidden" name="page" value="sm-exams">
                <input type="hidden" name="tab" value="marks">
                <select name="exam_id" required>
                    <option value=""><?php esc_html_e('Select Exam', 'school-management'); ?></option>
                    <?php foreach ($exams as $e): ?>
                    <option value="<?php echo $e->id; ?>" <?php selected($sel_exam, $e->id); ?>><?php echo esc_html($e->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="button"><?php esc_html_e('Load', 'school-management'); ?></button>
            </form>

            <?php if ($sel_exam && $students && $subjects): ?>
            <form id="sm-marks-form">
                <?php wp_nonce_field('sm_nonce', 'sm_marks_nonce'); ?>
                <input type="hidden" name="exam_id" value="<?php echo $sel_exam; ?>">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Student', 'school-management'); ?></th>
                            <?php foreach ($subjects as $sub): ?>
                            <th><?php echo esc_html($sub->subject_name); ?> (<?php echo $sub->full_marks; ?>)</th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($students as $st): ?>
                        <tr>
                            <td><?php echo esc_html($st->first_name . ' ' . $st->last_name); ?></td>
                            <?php foreach ($subjects as $sub): ?>
                            <td><input type="number" step="0.01" name="marks[<?php echo $st->id; ?>][<?php echo $sub->id; ?>]" value="<?php echo isset($marks_data[$st->id][$sub->id]) ? $marks_data[$st->id][$sub->id] : ''; ?>" max="<?php echo $sub->full_marks; ?>" style="width:80px;"></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e('Save Marks', 'school-management'); ?></button></p>
            </form>
            <?php elseif ($sel_exam && empty($subjects)): ?>
                <p><?php esc_html_e('No subjects defined for this exam. Add subjects in exam settings first.', 'school-management'); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }


    private static function render_results($classes) {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $sel_exam = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
        $exams = $wpdb->get_results("SELECT * FROM {$prefix}exams ORDER BY created_at DESC");
        ?>
        <div class="sm-section">
            <h2><?php esc_html_e('View Results', 'school-management'); ?></h2>
            <form method="get" class="sm-filters">
                <input type="hidden" name="page" value="sm-exams">
                <input type="hidden" name="tab" value="results">
                <select name="exam_id" required>
                    <option value=""><?php esc_html_e('Select Exam', 'school-management'); ?></option>
                    <?php foreach ($exams as $e): ?>
                    <option value="<?php echo $e->id; ?>" <?php selected($sel_exam, $e->id); ?>><?php echo esc_html($e->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="button"><?php esc_html_e('View', 'school-management'); ?></button>
            </form>
            <?php
            if ($sel_exam) {
                $exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}exams WHERE id=%d", $sel_exam));
                if ($exam) {
                    $subjects = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}exam_subjects WHERE exam_id=%d", $sel_exam));
                    $students = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}students WHERE class_id=%d AND status='active' ORDER BY first_name", $exam->class_id));
                    $marks = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}marks WHERE exam_id=%d", $sel_exam));
                    $marks_data = array();
                    foreach ($marks as $m) {
                        $marks_data[$m->student_id][$m->exam_subject_id] = $m->marks_obtained;
                    }
                    if ($students && $subjects):
                    ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Student', 'school-management'); ?></th>
                                <?php foreach ($subjects as $sub): ?>
                                <th><?php echo esc_html($sub->subject_name); ?></th>
                                <?php endforeach; ?>
                                <th><?php esc_html_e('Total', 'school-management'); ?></th>
                                <th><?php esc_html_e('Percentage', 'school-management'); ?></th>
                                <th><?php esc_html_e('Result', 'school-management'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $total_full = 0;
                        foreach ($subjects as $sub) { $total_full += $sub->full_marks; }
                        foreach ($students as $st):
                            $total_marks = 0;
                            $passed = true;
                            foreach ($subjects as $sub) {
                                $obtained = isset($marks_data[$st->id][$sub->id]) ? $marks_data[$st->id][$sub->id] : 0;
                                $total_marks += $obtained;
                                if ($obtained < $sub->pass_marks) $passed = false;
                            }
                            $percentage = $total_full > 0 ? round(($total_marks / $total_full) * 100, 2) : 0;
                        ?>
                            <tr>
                                <td><?php echo esc_html($st->first_name . ' ' . $st->last_name); ?></td>
                                <?php foreach ($subjects as $sub): ?>
                                <td><?php echo isset($marks_data[$st->id][$sub->id]) ? $marks_data[$st->id][$sub->id] : '-'; ?></td>
                                <?php endforeach; ?>
                                <td><strong><?php echo $total_marks; ?>/<?php echo $total_full; ?></strong></td>
                                <td><?php echo $percentage; ?>%</td>
                                <td><span class="sm-badge sm-badge-<?php echo $passed ? 'active' : 'inactive'; ?>"><?php echo $passed ? __('Pass','school-management') : __('Fail','school-management'); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p><?php esc_html_e('No data available for this exam.', 'school-management'); ?></p>
                    <?php endif;
                }
            }
            ?>
        </div>
        <?php
    }

    public function save_exam() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $id = intval($_POST['exam_id']);
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'class_id' => intval($_POST['class_id']),
            'start_date' => sanitize_text_field($_POST['start_date']),
            'end_date' => sanitize_text_field($_POST['end_date']),
            'description' => sanitize_textarea_field($_POST['description']),
        );
        if ($id) {
            $wpdb->update("{$prefix}exams", $data, array('id' => $id));
        } else {
            $wpdb->insert("{$prefix}exams", $data);
        }
        wp_send_json_success(array('message' => __('Exam saved successfully.', 'school-management')));
    }

    public function delete_exam() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $id = intval($_POST['id']);
        $wpdb->delete("{$prefix}exams", array('id' => $id));
        $wpdb->delete("{$prefix}exam_subjects", array('exam_id' => $id));
        $wpdb->delete("{$prefix}marks", array('exam_id' => $id));
        wp_send_json_success(array('message' => __('Exam deleted.', 'school-management')));
    }

    public function save_exam_subject() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $data = array(
            'exam_id' => intval($_POST['exam_id']),
            'subject_name' => sanitize_text_field($_POST['subject_name']),
            'full_marks' => floatval($_POST['full_marks']),
            'pass_marks' => floatval($_POST['pass_marks']),
            'exam_date' => sanitize_text_field($_POST['exam_date']),
        );
        $wpdb->insert("{$prefix}exam_subjects", $data);
        wp_send_json_success(array('message' => __('Subject added.', 'school-management')));
    }

    public function save_marks() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $exam_id = intval($_POST['exam_id']);
        $marks = isset($_POST['marks']) ? $_POST['marks'] : array();

        // Delete existing marks for this exam
        $wpdb->delete("{$prefix}marks", array('exam_id' => $exam_id));

        foreach ($marks as $student_id => $subjects) {
            foreach ($subjects as $subject_id => $obtained) {
                if ($obtained !== '') {
                    $wpdb->insert("{$prefix}marks", array(
                        'exam_id' => $exam_id,
                        'exam_subject_id' => intval($subject_id),
                        'student_id' => intval($student_id),
                        'marks_obtained' => floatval($obtained),
                    ));
                }
            }
        }
        wp_send_json_success(array('message' => __('Marks saved successfully.', 'school-management')));
    }
}

new SM_Exams();
