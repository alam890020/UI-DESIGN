<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_Import {
    public static function register() {
        add_action( 'admin_post_sms_import', array( __CLASS__, 'handle' ) );
    }
    public static function handle() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Permission denied.' );
        check_admin_referer( 'sms_import' );

        $t = isset( $_POST['t'] ) ? sanitize_key( $_POST['t'] ) : '';
        $map = SMS_Export::tables();
        if ( ! isset( $map[ $t ] ) ) wp_die( 'Bad table.' );
        if ( empty( $_FILES['csv']['tmp_name'] ) ) wp_die( 'No file uploaded.' );

        global $wpdb;
        $tbl  = $wpdb->prefix . 'sms_' . $map[ $t ];
        $cols = $wpdb->get_col( "DESCRIBE {$tbl}", 0 );

        $fh = fopen( $_FILES['csv']['tmp_name'], 'r' );
        if ( ! $fh ) wp_die( 'Could not read CSV.' );
        $header = fgetcsv( $fh );
        if ( ! $header ) { fclose( $fh ); wp_die( 'Empty CSV.' ); }

        $inserted = 0; $skipped = 0;
        while ( ( $row = fgetcsv( $fh ) ) !== false ) {
            $assoc = array();
            foreach ( $header as $i => $h ) {
                $key = sanitize_key( $h );
                if ( in_array( $key, $cols, true ) && $key !== 'id' ) {
                    $assoc[ $key ] = isset( $row[ $i ] ) ? sanitize_text_field( $row[ $i ] ) : '';
                }
            }
            if ( $assoc ) { $wpdb->insert( $tbl, $assoc ) ? $inserted++ : $skipped++; } else $skipped++;
        }
        fclose( $fh );

        wp_safe_redirect( add_query_arg( array( 'page' => 'sms-dashboard', 'sms_imported' => $inserted, 'sms_skipped' => $skipped ), admin_url( 'admin.php' ) ) );
        exit;
    }
}
