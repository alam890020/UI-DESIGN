<?php
/**
 * Asset enqueue. No bundled vendor libs.
 *
 * @package SchoolManagementStudio
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_Assets {

    public function enqueue_admin( $hook ) {
        $page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
        if ( strpos( $page, 'sms-' ) !== 0 ) return;

        wp_enqueue_style( 'sms-studio', SMS_PLUGIN_URL . 'assets/css/studio.css', array(), SMS_VERSION );
        wp_enqueue_script( 'sms-studio', SMS_PLUGIN_URL . 'assets/js/studio.js', array( 'jquery' ), SMS_VERSION, true );

        wp_localize_script( 'sms-studio', 'SMS', array(
            'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'sms_nonce' ),
            'currency' => SMS_Helper::get_setting( 'currency_sign', '$' ),
        ) );

        // Optional: Chart.js from CDN, only on dashboard / chart pages.
        if ( (int) SMS_Helper::get_setting( 'load_cdn_libs', 1 ) ) {
            if ( in_array( $page, array( 'sms-dashboard', 'sms-finance-reports', 'sms-fee-generator', 'sms-invoice-print' ), true ) ) {
                wp_enqueue_script( 'sms-chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js', array(), SMS_VERSION, true );
            }
        }
    }
}
