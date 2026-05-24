<?php
if (!defined('ABSPATH')) {
    exit;
}

class SM_Fees {

    public function __construct() {
        add_action('wp_ajax_sm_save_fee_type', array($this, 'save_fee_type'));
        add_action('wp_ajax_sm_delete_fee_type', array($this, 'delete_fee_type'));
        add_action('wp_ajax_sm_assign_fee', array($this, 'assign_fee'));
        add_action('wp_ajax_sm_pay_fee', array($this, 'pay_fee'));
        add_action('wp_ajax_sm_delete_fee', array($this, 'delete_fee'));
    }

    public static function render() {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'fees';

        echo '<div class="wrap"><h1>' . esc_html__('Fee Management', 'school-management') . '</h1>';
        echo '<nav class="nav-tab-wrapper">';
        echo '<a href="' . admin_url('admin.php?page=sm-fees&tab=fees') . '" class="nav-tab ' . ($tab === 'fees' ? 'nav-tab-active' : '') . '">' . esc_html__('Student Fees', 'school-management') . '</a>';
        echo '<a href="' . admin_url('admin.php?page=sm-fees&tab=types') . '" class="nav-tab ' . ($tab === 'types' ? 'nav-tab-active' : '') . '">' . esc_html__('Fee Types', 'school-management') . '</a>';
        echo '</nav>';

        if ($tab === 'types') {
            self::render_fee_types();
        } else {
            self::render_fees();
        }
        echo '</div>';
    }

    private static function render_fee_types() {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $fee_types = $wpdb->get_results("SELECT * FROM {$prefix}fee_types ORDER BY name");
        ?>
        <div class="sm-section">
            <h2><?php esc_html_e('Add Fee Type', 'school-management'); ?></h2>
            <form id="sm-fee-type-form" class="sm-form">
                <?php wp_nonce_field('sm_nonce', 'sm_fee_nonce'); ?>
                <input type="hidden" name="fee_type_id" value="">
                <div class="sm-form-grid">
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Name *', 'school-management'); ?></label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Amount', 'school-management'); ?></label>
                        <input type="number" step="0.01" name="amount" value="0">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Description', 'school-management'); ?></label>
                        <input type="text" name="description">
                    </div>
                </div>
                <button type="submit" class="button button-primary"><?php esc_html_e('Save Fee Type', 'school-management'); ?></button>
            </form>


            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php esc_html_e('Name', 'school-management'); ?></th>
                        <th><?php esc_html_e('Amount', 'school-management'); ?></th>
                        <th><?php esc_html_e('Description', 'school-management'); ?></th>
                        <th><?php esc_html_e('Actions', 'school-management'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($fee_types): $i=1; foreach ($fee_types as $ft): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo esc_html($ft->name); ?></td>
                        <td><?php echo number_format($ft->amount, 2); ?></td>
                        <td><?php echo esc_html($ft->description); ?></td>
                        <td><button class="button button-small sm-delete-btn" data-id="<?php echo $ft->id; ?>" data-type="fee_type"><?php esc_html_e('Delete', 'school-management'); ?></button></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="5"><?php esc_html_e('No fee types found.', 'school-management'); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    private static function render_fees() {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $fee_types = $wpdb->get_results("SELECT * FROM {$prefix}fee_types WHERE status=1 ORDER BY name");
        $students = $wpdb->get_results("SELECT * FROM {$prefix}students WHERE status='active' ORDER BY first_name");
        $fees = $wpdb->get_results("SELECT f.*, s.first_name, s.last_name, s.admission_no, ft.name as fee_type_name FROM {$prefix}fees f LEFT JOIN {$prefix}students s ON f.student_id=s.id LEFT JOIN {$prefix}fee_types ft ON f.fee_type_id=ft.id ORDER BY f.created_at DESC LIMIT 100");
        ?>
        <div class="sm-section">
            <h2><?php esc_html_e('Assign Fee to Student', 'school-management'); ?></h2>
            <form id="sm-assign-fee-form" class="sm-form">
                <?php wp_nonce_field('sm_nonce', 'sm_assign_fee_nonce'); ?>
                <div class="sm-form-grid">
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Student *', 'school-management'); ?></label>
                        <select name="student_id" required>
                            <option value=""><?php esc_html_e('Select Student', 'school-management'); ?></option>
                            <?php foreach ($students as $st): ?>
                            <option value="<?php echo $st->id; ?>"><?php echo esc_html($st->first_name . ' ' . $st->last_name . ' (' . $st->admission_no . ')'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Fee Type *', 'school-management'); ?></label>
                        <select name="fee_type_id" required>
                            <option value=""><?php esc_html_e('Select Fee Type', 'school-management'); ?></option>
                            <?php foreach ($fee_types as $ft): ?>
                            <option value="<?php echo $ft->id; ?>" data-amount="<?php echo $ft->amount; ?>"><?php echo esc_html($ft->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Amount *', 'school-management'); ?></label>
                        <input type="number" step="0.01" name="amount" required>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Due Date', 'school-management'); ?></label>
                        <input type="date" name="due_date">
                    </div>
                </div>
                <button type="submit" class="button button-primary"><?php esc_html_e('Assign Fee', 'school-management'); ?></button>
            </form>

            <h3><?php esc_html_e('Fee Records', 'school-management'); ?></h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php esc_html_e('Student', 'school-management'); ?></th>
                        <th><?php esc_html_e('Fee Type', 'school-management'); ?></th>
                        <th><?php esc_html_e('Amount', 'school-management'); ?></th>
                        <th><?php esc_html_e('Paid', 'school-management'); ?></th>
                        <th><?php esc_html_e('Status', 'school-management'); ?></th>
                        <th><?php esc_html_e('Due Date', 'school-management'); ?></th>
                        <th><?php esc_html_e('Actions', 'school-management'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($fees): $i=1; foreach ($fees as $fee): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo esc_html($fee->first_name . ' ' . $fee->last_name); ?></td>
                        <td><?php echo esc_html($fee->fee_type_name); ?></td>
                        <td><?php echo number_format($fee->amount, 2); ?></td>
                        <td><?php echo number_format($fee->paid_amount, 2); ?></td>
                        <td><span class="sm-badge sm-badge-<?php echo esc_attr($fee->status); ?>"><?php echo esc_html(ucfirst($fee->status)); ?></span></td>
                        <td><?php echo $fee->due_date ? date('M d, Y', strtotime($fee->due_date)) : '-'; ?></td>
                        <td>
                            <?php if ($fee->status !== 'paid'): ?>
                            <button class="button button-small sm-pay-fee-btn" data-id="<?php echo $fee->id; ?>" data-amount="<?php echo $fee->amount - $fee->paid_amount; ?>"><?php esc_html_e('Pay', 'school-management'); ?></button>
                            <?php endif; ?>
                            <button class="button button-small sm-delete-btn" data-id="<?php echo $fee->id; ?>" data-type="fee"><?php esc_html_e('Delete', 'school-management'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="8"><?php esc_html_e('No fee records found.', 'school-management'); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }


    public function save_fee_type() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $id = intval($_POST['fee_type_id']);
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'amount' => floatval($_POST['amount']),
            'description' => sanitize_text_field($_POST['description']),
        );
        if ($id) {
            $wpdb->update("{$prefix}fee_types", $data, array('id' => $id));
        } else {
            $wpdb->insert("{$prefix}fee_types", $data);
        }
        wp_send_json_success(array('message' => __('Fee type saved successfully.', 'school-management')));
    }

    public function delete_fee_type() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $wpdb->delete("{$prefix}fee_types", array('id' => intval($_POST['id'])));
        wp_send_json_success(array('message' => __('Fee type deleted.', 'school-management')));
    }

    public function assign_fee() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $data = array(
            'student_id' => intval($_POST['student_id']),
            'fee_type_id' => intval($_POST['fee_type_id']),
            'amount' => floatval($_POST['amount']),
            'due_date' => sanitize_text_field($_POST['due_date']),
            'status' => 'pending',
        );
        $wpdb->insert("{$prefix}fees", $data);
        wp_send_json_success(array('message' => __('Fee assigned successfully.', 'school-management')));
    }

    public function pay_fee() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $id = intval($_POST['id']);
        $amount = floatval($_POST['pay_amount']);
        $fee = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}fees WHERE id=%d", $id));
        if (!$fee) {
            wp_send_json_error(array('message' => __('Fee not found.', 'school-management')));
        }
        $new_paid = $fee->paid_amount + $amount;
        $status = $new_paid >= $fee->amount ? 'paid' : 'partial';
        $wpdb->update("{$prefix}fees", array(
            'paid_amount' => $new_paid,
            'status' => $status,
            'paid_date' => current_time('Y-m-d'),
            'payment_method' => sanitize_text_field($_POST['payment_method']),
        ), array('id' => $id));
        wp_send_json_success(array('message' => __('Payment recorded successfully.', 'school-management')));
    }

    public function delete_fee() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $wpdb->delete("{$prefix}fees", array('id' => intval($_POST['id'])));
        wp_send_json_success(array('message' => __('Fee deleted.', 'school-management')));
    }
}

new SM_Fees();
