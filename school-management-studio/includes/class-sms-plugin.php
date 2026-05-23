<?php
/**
 * Main plugin runner.
 *
 * @package SchoolManagementStudio
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_Plugin {
    public function run() {
        add_action( 'init', array( $this, 'load_textdomain' ) );

        $menu = new SMS_Menu();
        add_action( 'admin_menu', array( $menu, 'register_menus' ) );

        $assets = new SMS_Assets();
        add_action( 'admin_enqueue_scripts', array( $assets, 'enqueue_admin' ) );

        ( new SMS_Ajax() )->register();
        ( new SMS_REST() )->register();
        ( new SMS_Shortcodes() )->register();
        ( new SMS_Public() )->register();
        ( new SMS_Cron() )->register();
        ( new SMS_Fee_Generator() )->register();

        SMS_PDF::register();
        SMS_Export::register();
        SMS_Import::register();
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'school-management-studio', false, dirname( SMS_PLUGIN_BASENAME ) . '/languages' );
    }
}
