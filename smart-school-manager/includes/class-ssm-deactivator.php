<?php
/**
 * Plugin deactivation.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Deactivator {
    public static function deactivate() {
        flush_rewrite_rules();
    }
}
