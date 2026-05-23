<?php
/**
 * Plugin Name:       School Management Studio
 * Plugin URI:        https://github.com/alam890020/UI-DESIGN
 * Description:       A modern, glassmorphic School Management System for WordPress. Multi-school setup, students, staff, classes, exams, fees, library, hostel, transport, live lectures, support tickets and a full Print Center — all wrapped in a brand-new sidebar admin UI with dark mode.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            UI-DESIGN
 * Author URI:        https://github.com/alam890020
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       school-management-studio
 * Domain Path:       /languages
 *
 * @package SchoolManagementStudio
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'SMS_VERSION', '1.0.0' );
define( 'SMS_PLUGIN_FILE', __FILE__ );
define( 'SMS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SMS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SMS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once SMS_PLUGIN_DIR . 'includes/class-sms-activator.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-deactivator.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-database.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-helper.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-icons.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-menu.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-assets.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-ajax.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-rest.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-shortcodes.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-public.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-email.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-cron.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-pdf.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-export.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-import.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-fee-generator.php';
require_once SMS_PLUGIN_DIR . 'includes/class-sms-plugin.php';

register_activation_hook( __FILE__, array( 'SMS_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'SMS_Deactivator', 'deactivate' ) );

function sms_run_plugin() {
    ( new SMS_Plugin() )->run();
}
add_action( 'plugins_loaded', 'sms_run_plugin' );
