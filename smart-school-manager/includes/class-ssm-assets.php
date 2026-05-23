<?php
/**
 * Enqueue admin CSS / JS only on Smart School Manager pages.
 *
 * No third-party JS libraries are bundled with this plugin. Pages that
 * benefit from charts/datatables load these helpers from official CDNs
 * at runtime. Site owners can disable CDN loads via the
 * 'load_cdn_libs' setting.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Assets {

    /**
     * Map of optional CDN-hosted helper libraries.
     */
    public static function vendor_map() {
        return array(
            'chartjs'    => array(
                'js' => 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js',
            ),
            'datatables' => array(
                'js'   => 'https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js',
                'css'  => 'https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css',
                'deps' => array( 'jquery' ),
            ),
            'select2'    => array(
                'js'   => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'css'  => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'deps' => array( 'jquery' ),
            ),
            'flatpickr'  => array(
                'js'  => 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js',
                'css' => 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css',
            ),
        );
    }

    /**
     * Enqueue a CDN library by handle.
     */
    public static function enqueue_vendor( $handle ) {
        if ( ! (int) SSM_Helper::get_setting( 'load_cdn_libs', 1 ) ) {
            return;
        }
        $map = self::vendor_map();
        if ( ! isset( $map[ $handle ] ) ) return;
        $entry = $map[ $handle ];
        $deps  = isset( $entry['deps'] ) ? $entry['deps'] : array();
        if ( ! empty( $entry['css'] ) ) {
            wp_enqueue_style( 'ssm-' . $handle, $entry['css'], array(), SSM_VERSION );
        }
        if ( ! empty( $entry['js'] ) ) {
            wp_enqueue_script( 'ssm-' . $handle, $entry['js'], $deps, SSM_VERSION, true );
        }
    }

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

        // Pull Chart.js from CDN on chart-heavy pages (graceful fallback if blocked).
        if ( in_array( $page, array( 'ssm-dashboard', 'ssm-school-dashboard',
            'ssm-fee-generator', 'ssm-invoice-print', 'ssm-finance-reports',
            'ssm-academic-report' ), true ) ) {
            self::enqueue_vendor( 'chartjs' );
        }
    }
}
