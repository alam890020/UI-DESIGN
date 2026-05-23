<?php
/**
 * Enqueue admin CSS / JS only on Smart School Manager pages.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Assets {

    /**
     * Map of optional bundled vendor libraries.
     */
    public static function vendor_map() {
        return array(
            'chartjs'     => array( 'js' => 'vendor/chartjs/chart.umd.js' ),
            'apexcharts'  => array( 'js' => 'vendor/apexcharts/apexcharts.min.js', 'css' => 'vendor/apexcharts/apexcharts.css' ),
            'datatables'  => array( 'js' => 'vendor/datatables/jquery.dataTables.min.js', 'css' => 'vendor/datatables/jquery.dataTables.min.css', 'deps' => array( 'jquery' ) ),
            'select2'     => array( 'js' => 'vendor/select2/select2.full.min.js', 'css' => 'vendor/select2/select2.min.css', 'deps' => array( 'jquery' ) ),
            'flatpickr'   => array( 'js' => 'vendor/flatpickr/flatpickr.min.js', 'css' => 'vendor/flatpickr/flatpickr.min.css' ),
            'sweetalert2' => array( 'js' => 'vendor/sweetalert2/sweetalert2.all.min.js', 'css' => 'vendor/sweetalert2/sweetalert2.min.css' ),
            'quill'       => array( 'js' => 'vendor/quill/quill.min.js', 'css' => 'vendor/quill/quill.snow.css' ),
            'moment'      => array( 'js' => 'vendor/moment/moment.min.js' ),
            'fullcalendar'=> array( 'js' => 'vendor/fullcalendar/index.global.min.js' ),
            'jspdf'       => array( 'js' => 'vendor/tcpdf/jspdf.umd.min.js' ),
            'jspdf-autotable' => array( 'js' => 'vendor/tcpdf/jspdf.autotable.min.js', 'deps' => array( 'ssm-jspdf' ) ),
            'html2canvas' => array( 'js' => 'vendor/tcpdf/html2canvas.min.js' ),
            'fontawesome' => array( 'css' => 'vendor/fontawesome/css/all.min.css' ),
            'bootstrap'   => array( 'js' => 'vendor/bootstrap/bootstrap.bundle.min.js', 'css' => 'vendor/bootstrap/bootstrap.min.css' ),
            'tabler-icons'=> array( 'css' => 'vendor/tabler-icons/tabler-icons.min.css' ),
            'leaflet'     => array( 'js' => 'vendor/leaflet/leaflet.js', 'css' => 'vendor/leaflet/leaflet.css' ),
        );
    }

    /**
     * Enqueue a bundled vendor lib by handle.
     */
    public static function enqueue_vendor( $handle ) {
        $map = self::vendor_map();
        if ( ! isset( $map[ $handle ] ) ) return;
        $entry = $map[ $handle ];
        $deps  = isset( $entry['deps'] ) ? $entry['deps'] : array();
        if ( ! empty( $entry['css'] ) ) {
            wp_enqueue_style( 'ssm-' . $handle, SSM_PLUGIN_URL . $entry['css'], array(), SSM_VERSION );
        }
        if ( ! empty( $entry['js'] ) ) {
            wp_enqueue_script( 'ssm-' . $handle, SSM_PLUGIN_URL . $entry['js'], $deps, SSM_VERSION, true );
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

        // Auto-load Chart.js + DataTables on dashboard, fee generator, invoice print.
        if ( in_array( $page, array( 'ssm-dashboard', 'ssm-school-dashboard', 'ssm-fee-generator',
            'ssm-invoice-print', 'ssm-finance-reports', 'ssm-academic-report' ), true ) ) {
            self::enqueue_vendor( 'chartjs' );
            self::enqueue_vendor( 'apexcharts' );
        }
        if ( in_array( $page, array( 'ssm-students', 'ssm-staff', 'ssm-invoices', 'ssm-invoice-print' ), true ) ) {
            self::enqueue_vendor( 'datatables' );
            self::enqueue_vendor( 'select2' );
        }
        if ( in_array( $page, array( 'ssm-events', 'ssm-routines', 'ssm-staff-timetable' ), true ) ) {
            self::enqueue_vendor( 'fullcalendar' );
        }
    }
}
