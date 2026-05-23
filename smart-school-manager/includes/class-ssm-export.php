<?php
/**
 * CSV export utility.
 *
 *   /wp-admin/admin-post.php?action=ssm_export&t=students
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Export {

    /**
     * Map of safe export keys -> tables.
     */
    public static function tables() {
        return array(
            'students'   => 'students',
            'staff'      => 'staff',
            'classes'    => 'classes',
            'subjects'   => 'subjects',
            'invoices'   => 'invoices',
            'payments'   => 'payments',
            'income'     => 'income',
            'expenses'   => 'expenses',
            'books'      => 'books',
            'admissions' => 'admissions',
            'inquiries'  => 'inquiries',
            'attendance' => 'attendance',
            'lectures'   => 'lectures',
            'tickets'    => 'tickets',
            'logs'       => 'logs',
        );
    }

    public static function register() {
        add_action( 'admin_post_ssm_export', array( __CLASS__, 'handle_export' ) );
    }

    public static function handle_export() {
        if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'ssm_view_reports' ) ) {
            wp_die( 'Permission denied.' );
        }
        check_admin_referer( 'ssm_export' );

        $t = isset( $_GET['t'] ) ? sanitize_key( $_GET['t'] ) : '';
        $map = self::tables();
        if ( ! isset( $map[ $t ] ) ) wp_die( 'Bad table.' );

        global $wpdb;
        $tbl = $wpdb->prefix . 'ssm_' . $map[ $t ];
        $rows = $wpdb->get_results( "SELECT * FROM {$tbl} ORDER BY id DESC", ARRAY_A );

        nocache_headers();
        header( 'Content-Type: text/csv; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename="' . $t . '-' . date( 'Ymd-His' ) . '.csv"' );

        $out = fopen( 'php://output', 'w' );
        if ( $rows ) {
            fputcsv( $out, array_keys( $rows[0] ) );
            foreach ( $rows as $r ) {
                fputcsv( $out, $r );
            }
        } else {
            fputcsv( $out, array( 'No data' ) );
        }
        fclose( $out );
        exit;
    }
}
