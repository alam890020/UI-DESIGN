<?php
/**
 * Main plugin class — wires up menu, assets, AJAX, router.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Plugin {

    /**
     * Run the plugin: register hooks.
     */
    public function run() {
        // Load text domain.
        add_action( 'init', array( $this, 'load_textdomain' ) );

        // Admin menu.
        $menu = new SSM_Menu();
        add_action( 'admin_menu', array( $menu, 'register_menus' ) );

        // Admin assets.
        $assets = new SSM_Assets();
        add_action( 'admin_enqueue_scripts', array( $assets, 'enqueue_admin' ) );

        // AJAX handlers (admin).
        $ajax = new SSM_Ajax();
        $ajax->register();

        // REST API.
        ( new SSM_REST() )->register();

        // Frontend (shortcodes + assets + public AJAX).
        ( new SSM_Shortcodes() )->register();
        ( new SSM_Public() )->register();

        // Cron jobs.
        ( new SSM_Cron() )->register();

        // Native WP dashboard widget.
        ( new SSM_Widgets() )->register();

        // Print / Export / Import endpoints.
        SSM_PDF::register();
        SSM_Export::register();
        SSM_Import::register();

        // Monthly fee generator (AJAX).
        ( new SSM_Fee_Generator() )->register();
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'smart-school-manager', false, dirname( SSM_PLUGIN_BASENAME ) . '/languages' );
    }
}
