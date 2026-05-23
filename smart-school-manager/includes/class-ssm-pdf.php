<?php
/**
 * Print-friendly HTML output (PDF-ready).
 *
 * The plugin does not bundle a PDF library to keep the zip small.
 * Instead we generate a clean, print-optimised HTML page that browsers
 * can save as PDF (Ctrl+P -> Save as PDF) or that can be piped into
 * dompdf / mPDF if available in the host environment.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_PDF {

    /**
     * Render any template under /templates/ as a print page.
     *
     * @param string $template e.g. 'invoice'
     * @param array  $data     variables to extract into the template
     */
    public static function render( $template, $data = array() ) {
        $tpl = SSM_PLUGIN_DIR . 'templates/' . sanitize_file_name( $template ) . '.php';
        if ( ! file_exists( $tpl ) ) {
            wp_die( 'Template not found.' );
        }

        // Stream a minimal HTML shell with print stylesheet.
        nocache_headers();
        header( 'Content-Type: text/html; charset=UTF-8' );

        $school = SSM_Helper::get_setting( 'school_name', get_bloginfo( 'name' ) );
        $color  = SSM_Helper::get_setting( 'theme_color', '#6366f1' );

        echo '<!doctype html><html><head><meta charset="utf-8"><title>' . esc_html( $template ) . '</title>';
        echo '<link rel="stylesheet" href="' . esc_url( SSM_PLUGIN_URL . 'assets/css/print.css?v=' . SSM_VERSION ) . '">';
        echo '<style>:root{--ssm-print-color:' . esc_attr( $color ) . '}</style>';
        echo '</head><body class="ssm-print">';

        // Make data available as variables in the template.
        if ( is_array( $data ) ) {
            extract( $data, EXTR_SKIP );
        }
        $school_name = $school;
        $theme_color = $color;

        include $tpl;

        // Auto-print prompt when ?autoprint=1.
        if ( ! empty( $_GET['autoprint'] ) ) {
            echo '<script>window.addEventListener("load",function(){setTimeout(function(){window.print();},300);});</script>';
        }

        echo '</body></html>';
        exit;
    }

    /**
     * Register print endpoints.
     *
     *   /wp-admin/admin-post.php?action=ssm_print&t=invoice&id=123
     */
    public static function register() {
        add_action( 'admin_post_ssm_print',        array( __CLASS__, 'handle_print' ) );
        add_action( 'admin_post_nopriv_ssm_print', array( __CLASS__, 'handle_print_logged_out' ) );
    }

    public static function handle_print_logged_out() {
        wp_die( 'Please sign in to print.' );
    }

    public static function handle_print() {
        if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'ssm_view_dashboard' ) ) {
            wp_die( 'Permission denied.' );
        }
        $t  = isset( $_GET['t'] )  ? sanitize_key( $_GET['t'] )  : '';
        $id = isset( $_GET['id'] ) ? absint( $_GET['id'] )       : 0;
        if ( ! $t ) wp_die( 'Missing template.' );

        global $wpdb; $p = $wpdb->prefix . 'ssm_';
        $data = array();

        switch ( $t ) {
            case 'invoice':
                $data['invoice'] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$p}invoices WHERE id=%d", $id ) );
                if ( $data['invoice'] ) {
                    $data['student'] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$p}students WHERE id=%d", $data['invoice']->student_id ) );
                    $data['payments']= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$p}payments WHERE invoice_id=%d", $id ) );
                }
                break;
            case 'student-id-card':
                $data['student'] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$p}students WHERE id=%d", $id ) );
                break;
            case 'staff-id-card':
                $data['staff']   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$p}staff WHERE id=%d", $id ) );
                break;
            case 'admit-card':
                $data['student'] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$p}students WHERE id=%d", $id ) );
                $data['exam']    = $wpdb->get_row( "SELECT * FROM {$p}exams ORDER BY id DESC LIMIT 1" );
                break;
            case 'certificate':
            case 'transfer-certificate':
                $data['student'] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$p}students WHERE id=%d", $id ) );
                break;
        }

        self::render( $t, $data );
    }
}
