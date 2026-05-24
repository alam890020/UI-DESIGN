<?php
if (!defined('ABSPATH')) {
    exit;
}

class SM_Settings {

    public function __construct() {
        add_action('wp_ajax_sm_save_settings', array($this, 'save_settings'));
    }

    public static function render() {
        $settings = get_option('sm_settings', array());
        $school_name = isset($settings['school_name']) ? $settings['school_name'] : '';
        $school_email = isset($settings['school_email']) ? $settings['school_email'] : '';
        $school_phone = isset($settings['school_phone']) ? $settings['school_phone'] : '';
        $school_address = isset($settings['school_address']) ? $settings['school_address'] : '';
        $school_website = isset($settings['school_website']) ? $settings['school_website'] : '';
        $currency = isset($settings['currency']) ? $settings['currency'] : 'USD';
        $date_format = isset($settings['date_format']) ? $settings['date_format'] : 'Y-m-d';
        $session_start = isset($settings['session_start']) ? $settings['session_start'] : '';
        $session_end = isset($settings['session_end']) ? $settings['session_end'] : '';
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Settings', 'school-management'); ?></h1>
            <form id="sm-settings-form" class="sm-form">
                <?php wp_nonce_field('sm_nonce', 'sm_settings_nonce'); ?>
                <h2><?php esc_html_e('School Information', 'school-management'); ?></h2>
                <div class="sm-form-grid">
                    <div class="sm-form-group">
                        <label><?php esc_html_e('School Name', 'school-management'); ?></label>
                        <input type="text" name="school_name" value="<?php echo esc_attr($school_name); ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Email', 'school-management'); ?></label>
                        <input type="email" name="school_email" value="<?php echo esc_attr($school_email); ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Phone', 'school-management'); ?></label>
                        <input type="text" name="school_phone" value="<?php echo esc_attr($school_phone); ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Website', 'school-management'); ?></label>
                        <input type="url" name="school_website" value="<?php echo esc_attr($school_website); ?>">
                    </div>
                    <div class="sm-form-group sm-form-full">
                        <label><?php esc_html_e('Address', 'school-management'); ?></label>
                        <textarea name="school_address" rows="3"><?php echo esc_textarea($school_address); ?></textarea>
                    </div>
                </div>

                <h2><?php esc_html_e('General Settings', 'school-management'); ?></h2>
                <div class="sm-form-grid">
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Currency', 'school-management'); ?></label>
                        <select name="currency">
                            <option value="USD" <?php selected($currency, 'USD'); ?>>USD ($)</option>
                            <option value="EUR" <?php selected($currency, 'EUR'); ?>>EUR</option>
                            <option value="GBP" <?php selected($currency, 'GBP'); ?>>GBP</option>
                            <option value="INR" <?php selected($currency, 'INR'); ?>>INR</option>
                            <option value="PKR" <?php selected($currency, 'PKR'); ?>>PKR</option>
                            <option value="BDT" <?php selected($currency, 'BDT'); ?>>BDT</option>
                        </select>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Date Format', 'school-management'); ?></label>
                        <select name="date_format">
                            <option value="Y-m-d" <?php selected($date_format, 'Y-m-d'); ?>>YYYY-MM-DD</option>
                            <option value="d/m/Y" <?php selected($date_format, 'd/m/Y'); ?>>DD/MM/YYYY</option>
                            <option value="m/d/Y" <?php selected($date_format, 'm/d/Y'); ?>>MM/DD/YYYY</option>
                        </select>
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Session Start', 'school-management'); ?></label>
                        <input type="date" name="session_start" value="<?php echo esc_attr($session_start); ?>">
                    </div>
                    <div class="sm-form-group">
                        <label><?php esc_html_e('Session End', 'school-management'); ?></label>
                        <input type="date" name="session_end" value="<?php echo esc_attr($session_end); ?>">
                    </div>
                </div>

                <p class="submit">
                    <button type="submit" class="button button-primary"><?php esc_html_e('Save Settings', 'school-management'); ?></button>
                </p>
            </form>
        </div>
        <?php
    }

    public function save_settings() {
        check_ajax_referer('sm_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'school-management')));
        }
        $settings = array(
            'school_name' => sanitize_text_field($_POST['school_name']),
            'school_email' => sanitize_email($_POST['school_email']),
            'school_phone' => sanitize_text_field($_POST['school_phone']),
            'school_address' => sanitize_textarea_field($_POST['school_address']),
            'school_website' => esc_url_raw($_POST['school_website']),
            'currency' => sanitize_text_field($_POST['currency']),
            'date_format' => sanitize_text_field($_POST['date_format']),
            'session_start' => sanitize_text_field($_POST['session_start']),
            'session_end' => sanitize_text_field($_POST['session_end']),
        );
        update_option('sm_settings', $settings);
        wp_send_json_success(array('message' => __('Settings saved successfully.', 'school-management')));
    }
}

new SM_Settings();
