<?php
/**
 * Plugin Name: School Softwere
 * Plugin URI: https://yoursite.com/school-softwere/
 * Description: School Softwere is a powerful WordPress plugin to manage multiple schools with students, staff, exams, fees, attendance, library, transport, hostel, and much more — all in one beautiful dashboard.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yoursite.com
 * Text Domain: school-softwere
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || die();

// Define core constants
define( 'SS_VERSION',     '1.0.0' );
define( 'SS_PLUGIN_FILE', __FILE__ );
define( 'SS_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'SS_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );
define( 'SS_PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'SS_TEXT_DOMAIN', 'school-softwere' );

// Load constants & autoloader
require_once SS_PLUGIN_DIR . 'includes/constants.php';
require_once SS_PLUGIN_DIR . 'includes/helpers/SS_Helper.php';
require_once SS_PLUGIN_DIR . 'includes/helpers/SS_Config.php';
require_once SS_PLUGIN_DIR . 'includes/helpers/SS_M_Role.php';
require_once SS_PLUGIN_DIR . 'admin/inc/SS_Database.php';
require_once SS_PLUGIN_DIR . 'admin/inc/SS_Menu.php';
require_once SS_PLUGIN_DIR . 'admin/admin.php';
require_once SS_PLUGIN_DIR . 'public/public.php';

/**
 * Main plugin class — Singleton
 */
final class School_Softwere {

	/** @var School_Softwere */
	private static $instance = null;

	/**
	 * Get the single instance.
	 *
	 * @return School_Softwere
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** Constructor — wire up hooks */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init',           array( $this, 'init' ) );
	}

	/** Load plugin text domain */
	public function load_textdomain() {
		load_plugin_textdomain(
			SS_TEXT_DOMAIN,
			false,
			dirname( SS_PLUGIN_BASE ) . '/languages'
		);
	}

	/** Run init tasks */
	public function init() {
		// Register REST API routes
		if ( class_exists( 'SS_REST_API' ) ) {
			( new SS_REST_API() )->register_routes();
		}
	}
}

/**
 * Activation hook
 */
function ss_activate() {
	require_once SS_PLUGIN_DIR . 'admin/inc/SS_Database.php';
	SS_Database::create_tables();
	SS_Database::insert_sample_data();
	// Trigger setup wizard on first activation
	if ( ! get_option( 'ss_setup_complete' ) ) {
		update_option( 'ss_activation_redirect', true );
	}
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ss_activate' );

/**
 * Deactivation hook
 */
function ss_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ss_deactivate' );

/**
 * Bootstrap
 */
School_Softwere::instance();
