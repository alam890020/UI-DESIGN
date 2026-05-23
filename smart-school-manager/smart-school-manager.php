<?php
/**
 * Plugin Name:       Smart School Manager
 * Plugin URI:        https://github.com/alam890020/UI-DESIGN
 * Description:       A complete School Management System for WordPress with an attractive, modern admin UI. Manage students, staff, classes, exams, fees, library, hostel, transport, live lectures, support tickets and more — all from one beautiful dashboard.
 * Version:           1.0.1
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            UI-DESIGN
 * Author URI:        https://github.com/alam890020
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       smart-school-manager
 * Domain Path:       /languages
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin constants.
define( 'SSM_VERSION', '1.0.1' );
define( 'SSM_PLUGIN_FILE', __FILE__ );
define( 'SSM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SSM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SSM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Includes.
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-activator.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-deactivator.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-database.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-helper.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-capabilities.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-menu.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-assets.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-ajax.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-router.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-rest.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-shortcodes.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-public.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-email.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-notifications.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-cron.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-pdf.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-export.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-import.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-widgets.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-fee-generator.php';
require_once SSM_PLUGIN_DIR . 'includes/class-ssm-plugin.php';

// Activation / Deactivation.
register_activation_hook( __FILE__, array( 'SSM_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'SSM_Deactivator', 'deactivate' ) );

/**
 * Bootstrap.
 */
function ssm_run_plugin() {
    $plugin = new SSM_Plugin();
    $plugin->run();
}
add_action( 'plugins_loaded', 'ssm_run_plugin' );
