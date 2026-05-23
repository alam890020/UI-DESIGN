<?php
/**
 * Print/PDF endpoint. Renders a template under /templates/ wrapped with
 * the studio print stylesheet — browsers can save as PDF via Ctrl+P.
 *
 * @package SchoolManagementStudio
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_PDF {

    public static function register() {
        add_action( 'admin_post_sms_print',        array( __CLASS__, 'handle' ) );
        add_action( 'admin_post_nopriv_sms_print', array( __CLASS__, 'noaccess' ) );
    }
    public static function noaccess() { wp_die( 'Please sign in to print.' ); }

    public static function render( $template, $data = array() ) {
        $tpl = SMS_PLUGIN_DIR . 'templates/' . sanitize_file_name( $template ) . '.php';
        if ( ! file_exists( $tpl ) ) wp_die( 'Template not found.' );

        nocache_headers();
        header( 'Content-Type: text/html; charset=UTF-8' );

        $school = SMS_Helper::get_setting( 'school_name', get_bloginfo( 'name' ) );
        $color  = SMS_Helper::get_setting( 'theme_color', '#7c3aed' );

        echo '<!doctype html><html><head><meta charset="utf-8"><title>' . esc_html( $template ) . '</title>';
        echo '<link rel="stylesheet" href="' . esc_url( SMS_PLUGIN_URL . 'assets/css/print.css?v=' . SMS_VERSION ) . '">';
        echo '<style>:root{--sms-print:' . esc_attr( $color ) . '}</style>';
        echo '</head><body class="sms-print">';

        if ( is_array( $data ) ) extract( $data, EXTR_SKIP );
        $school_name = $school;
        $theme_color = $color;

        include $tpl;

        if ( ! empty( $_GET['autoprint'] ) ) {
            echo '<script>window.addEventListener("load",function(){setTimeout(function(){window.print();},300);});</script>';
        }
        echo '</body></html>';
        exit;
    }

    public static function handle() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Permission denied.' );
        $t  = isset( $_GET['t'] )  ? sanitize_key( $_GET['t'] )  : '';
        $id = isset( $_GET['id'] ) ? absint( $_GET['id'] )       : 0;
        if ( ! $t ) wp_die( 'Missing template.' );

        global $wpdb; $p = $wpdb->prefix . 'sms_';
        $data = array();
        switch ( $t ) {
            case 'invoice':
                $data['invoice'] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$p}invoices WHERE id=%d", $id ) );
                if ( $data['invoice'] ) {
                    $data['student']  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$p}students WHERE id=%d", $data['invoice']->student_id ) );
                    $data['payments'] = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$p}payments WHERE invoice_id=%d", $id ) );
                }
                break;
            case 'student-id-card':
                $data['student'] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$p}students WHERE id=%d", $id ) );
                break;
            case 'staff-id-card':
                $data['staff'] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$p}staff WHERE id=%d", $id ) );
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
