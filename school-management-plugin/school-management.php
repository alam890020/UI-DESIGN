<?php
/**
 * Plugin Name: School Management System
 * Plugin URI: https://example.com/school-management
 * Description: A comprehensive school management system for WordPress with student management, classes, attendance, fees, exams, staff, notices, and more.
 * Version: 1.0.0
 * Author: Developer
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: school-management
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('SM_VERSION', '1.0.0');
define('SM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SM_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Main plugin class
final class School_Management {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    private function includes() {
        // Core
        require_once SM_PLUGIN_DIR . 'includes/class-sm-database.php';
        require_once SM_PLUGIN_DIR . 'includes/class-sm-admin-menu.php';
        require_once SM_PLUGIN_DIR . 'includes/class-sm-dashboard.php';
        require_once SM_PLUGIN_DIR . 'includes/class-sm-students.php';
        require_once SM_PLUGIN_DIR . 'includes/class-sm-classes.php';
        require_once SM_PLUGIN_DIR . 'includes/class-sm-attendance.php';
        require_once SM_PLUGIN_DIR . 'includes/class-sm-fees.php';
        require_once SM_PLUGIN_DIR . 'includes/class-sm-exams.php';
        require_once SM_PLUGIN_DIR . 'includes/class-sm-staff.php';
        require_once SM_PLUGIN_DIR . 'includes/class-sm-notices.php';
        require_once SM_PLUGIN_DIR . 'includes/class-sm-settings.php';
    }

    private function init_hooks() {
        register_activation_hook(__FILE__, array('SM_Database', 'activate'));
        register_deactivation_hook(__FILE__, array('SM_Database', 'deactivate'));
        add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
        add_action('init', array($this, 'load_textdomain'));
    }

    public function admin_assets($hook) {
        if (strpos($hook, 'school-management') === false && strpos($hook, 'sm-') === false) {
            return;
        }
        wp_enqueue_style('sm-admin-style', SM_PLUGIN_URL . 'assets/css/admin.css', array(), SM_VERSION);
        wp_enqueue_script('sm-admin-script', SM_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), SM_VERSION, true);
        wp_localize_script('sm-admin-script', 'sm_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sm_nonce'),
        ));
    }

    public function load_textdomain() {
        load_plugin_textdomain('school-management', false, dirname(SM_PLUGIN_BASENAME) . '/languages/');
    }
}

// Initialize
function school_management_init() {
    return School_Management::get_instance();
}
add_action('plugins_loaded', 'school_management_init');
