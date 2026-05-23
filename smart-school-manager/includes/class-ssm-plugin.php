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

        // AJAX handlers.
        $ajax = new SSM_Ajax();
        $ajax->register();
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'smart-school-manager', false, dirname( SSM_PLUGIN_BASENAME ) . '/languages' );
    }
}
