<?php
if (!defined('ABSPATH')) {
    exit;
}

class SM_Classes {

    public function __construct() {
        add_action('wp_ajax_sm_save_class', array($this, 'save_class'));
        add_action('wp_ajax_sm_delete_class', array($this, 'delete_class'));
        add_action('wp_ajax_sm_save_section', array($this, 'save_section'));
        add_action('wp_ajax_sm_delete_section', array($this, 'delete_section'));
        add_action('wp_ajax_sm_get_sections', array($this, 'get_sections'));
    }

    public static function render() {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $classes = $wpdb->get_results("SELECT * FROM {$prefix}classes ORDER BY name");
        $sections = $wpdb->get_results("SELECT s.*, c.name as class_name FROM {$prefix}sections s LEFT JOIN {$prefix}classes c ON s.class_id=c.id ORDER BY c.name, s.name");
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Classes & Sections', 'school-management'); ?></h1>

            <div class="sm-two-col">
                <div class="sm-col">
                    <h2><?php esc_html_e('Add Class', 'school-management'); ?></h2>
                    <form id="sm-class-form" class="sm-form">
                        <?php wp_nonce_field('sm_nonce', 'sm_class_nonce'); ?>
                        <input type="hidden" name="class_id" value="">
                        <div class="sm-form-group">
                            <label><?php esc_html_e('Class Name *', 'school-management'); ?></label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="sm-form-group">
                            <label><?php esc_html_e('Description', 'school-management'); ?></label>
                            <textarea name="description" rows="2"></textarea>
                        </div>
                        <button type="submit" class="button button-primary"><?php esc_html_e('Save Class', 'school-management'); ?></button>
                    </form>

                    <h3><?php esc_html_e('All Classes', 'school-management'); ?></h3>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php esc_html_e('Name', 'school-management'); ?></th>
                                <th><?php esc_html_e('Status', 'school-management'); ?></th>
                                <th><?php esc_html_e('Actions', 'school-management'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($classes): $i=1; foreach ($classes as $class): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo esc_html($class->name); ?></td>
                                <td><?php echo $class->status ? __('Active','school-management') : __('Inactive','school-management'); ?></td>
                                <td>
                                    <button class="button button-small sm-delete-btn" data-id="<?php echo $class->id; ?>" data-type="class"><?php esc_html_e('Delete', 'school-management'); ?></button>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="4"><?php esc_html_e('No classes found.', 'school-management'); ?></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="sm-col">
                    <h2><?php esc_html_e('Add Section', 'school-management'); ?></h2>
                    <form id="sm-section-form" class="sm-form">
                        <?php wp_nonce_field('sm_nonce', 'sm_section_nonce'); ?>
                        <input type="hidden" name="section_id" value="">
                        <div class="sm-form-group">
                            <label><?php esc_html_e('Class *', 'school-management'); ?></label>
                            <select name="class_id" required>
                                <option value=""><?php esc_html_e('Select Class', 'school-management'); ?></option>
                                <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class->id; ?>"><?php echo esc_html($class->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="sm-form-group">
                            <label><?php esc_html_e('Section Name *', 'school-management'); ?></label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="sm-form-group">
                            <label><?php esc_html_e('Capacity', 'school-management'); ?></label>
                            <input type="number" name="capacity" value="30">
                        </div>
                        <button type="submit" class="button button-primary"><?php esc_html_e('Save Section', 'school-management'); ?></button>
                    </form>

                    <h3><?php esc_html_e('All Sections', 'school-management'); ?></h3>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php esc_html_e('Class', 'school-management'); ?></th>
                                <th><?php esc_html_e('Section', 'school-management'); ?></th>
                                <th><?php esc_html_e('Capacity', 'school-management'); ?></th>
                                <th><?php esc_html_e('Actions', 'school-management'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($sections): $i=1; foreach ($sections as $sec): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo esc_html($sec->class_name); ?></td>
                                <td><?php echo esc_html($sec->name); ?></td>
                                <td><?php echo intval($sec->capacity); ?></td>
                                <td>
                                    <button class="button button-small sm-delete-btn" data-id="<?php echo $sec->id; ?>" data-type="section"><?php esc_html_e('Delete', 'school-management'); ?></button>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="5"><?php esc_html_e('No sections found.', 'school-management'); ?></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }


    public function save_class() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $id = intval($_POST['class_id']);
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'description' => sanitize_textarea_field($_POST['description']),
        );
        if ($id) {
            $wpdb->update("{$prefix}classes", $data, array('id' => $id));
        } else {
            $wpdb->insert("{$prefix}classes", $data);
        }
        wp_send_json_success(array('message' => __('Class saved successfully.', 'school-management')));
    }

    public function delete_class() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $id = intval($_POST['id']);
        $wpdb->delete("{$prefix}classes", array('id' => $id));
        $wpdb->delete("{$prefix}sections", array('class_id' => $id));
        wp_send_json_success(array('message' => __('Class deleted successfully.', 'school-management')));
    }

    public function save_section() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $id = intval($_POST['section_id']);
        $data = array(
            'class_id' => intval($_POST['class_id']),
            'name' => sanitize_text_field($_POST['name']),
            'capacity' => intval($_POST['capacity']),
        );
        if ($id) {
            $wpdb->update("{$prefix}sections", $data, array('id' => $id));
        } else {
            $wpdb->insert("{$prefix}sections", $data);
        }
        wp_send_json_success(array('message' => __('Section saved successfully.', 'school-management')));
    }

    public function delete_section() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $id = intval($_POST['id']);
        $wpdb->delete("{$prefix}sections", array('id' => $id));
        wp_send_json_success(array('message' => __('Section deleted successfully.', 'school-management')));
    }

    public function get_sections() {
        check_ajax_referer('sm_nonce', 'nonce');
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $class_id = intval($_POST['class_id']);
        $sections = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$prefix}sections WHERE class_id=%d AND status=1", $class_id
        ));
        wp_send_json_success($sections);
    }
}

new SM_Classes();
