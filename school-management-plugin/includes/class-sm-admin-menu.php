<?php
if (!defined('ABSPATH')) {
    exit;
}

class SM_Admin_Menu {

    public function __construct() {
        add_action('admin_menu', array($this, 'register_menus'));
    }

    public function register_menus() {
        // Main menu
        add_menu_page(
            __('School Management', 'school-management'),
            __('School Mgmt', 'school-management'),
            'manage_options',
            'school-management',
            array('SM_Dashboard', 'render'),
            'dashicons-welcome-learn-more',
            25
        );

        // Dashboard
        add_submenu_page(
            'school-management',
            __('Dashboard', 'school-management'),
            __('Dashboard', 'school-management'),
            'manage_options',
            'school-management',
            array('SM_Dashboard', 'render')
        );

        // Students
        add_submenu_page(
            'school-management',
            __('Students', 'school-management'),
            __('Students', 'school-management'),
            'manage_options',
            'sm-students',
            array('SM_Students', 'render')
        );

        // Classes
        add_submenu_page(
            'school-management',
            __('Classes', 'school-management'),
            __('Classes', 'school-management'),
            'manage_options',
            'sm-classes',
            array('SM_Classes', 'render')
        );

        // Attendance
        add_submenu_page(
            'school-management',
            __('Attendance', 'school-management'),
            __('Attendance', 'school-management'),
            'manage_options',
            'sm-attendance',
            array('SM_Attendance', 'render')
        );

        // Fees
        add_submenu_page(
            'school-management',
            __('Fees', 'school-management'),
            __('Fees', 'school-management'),
            'manage_options',
            'sm-fees',
            array('SM_Fees', 'render')
        );

        // Exams
        add_submenu_page(
            'school-management',
            __('Exams', 'school-management'),
            __('Exams & Results', 'school-management'),
            'manage_options',
            'sm-exams',
            array('SM_Exams', 'render')
        );

        // Staff
        add_submenu_page(
            'school-management',
            __('Staff', 'school-management'),
            __('Staff', 'school-management'),
            'manage_options',
            'sm-staff',
            array('SM_Staff', 'render')
        );

        // Notices
        add_submenu_page(
            'school-management',
            __('Notices', 'school-management'),
            __('Notices', 'school-management'),
            'manage_options',
            'sm-notices',
            array('SM_Notices', 'render')
        );

        // Settings
        add_submenu_page(
            'school-management',
            __('Settings', 'school-management'),
            __('Settings', 'school-management'),
            'manage_options',
            'sm-settings',
            array('SM_Settings', 'render')
        );
    }
}

new SM_Admin_Menu();
