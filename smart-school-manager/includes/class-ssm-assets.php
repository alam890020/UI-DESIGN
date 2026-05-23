<?php
/**
 * Enqueue admin CSS / JS only on Smart School Manager pages.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Assets {
    public function enqueue_admin( $hook ) {
        $page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
        if ( strpos( $page, 'ssm-' ) !== 0 ) {
            return;
        }

        wp_enqueue_style(
            'ssm-admin',
            SSM_PLUGIN_URL . 'assets/css/admin.css',
            array( 'dashicons' ),
            SSM_VERSION
        );

        wp_enqueue_script(
            'ssm-admin',
            SSM_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            SSM_VERSION,
            true
        );

        wp_localize_script( 'ssm-admin', 'SSM', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'ssm_nonce' ),
            'currency'=> SSM_Helper::get_setting( 'currency_sign', '$' ),
        ) );
    }
}
