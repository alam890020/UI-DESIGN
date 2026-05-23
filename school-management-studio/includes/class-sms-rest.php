<?php
/**
 * REST API endpoints under /sms/v1/.
 *
 * @package SchoolManagementStudio
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_REST {

    public function register() {
        add_action( 'rest_api_init', array( $this, 'routes' ) );
    }

    public function routes() {
        $entities = array(
            'students'  => 'students',
            'staff'     => 'staff',
            'classes'   => 'classes',
            'subjects'  => 'subjects',
            'sessions'  => 'sessions',
            'fees'      => 'fee_structures',
            'invoices'  => 'invoices',
            'books'     => 'books',
            'routes'    => 'routes',
            'vehicles'  => 'vehicles',
            'lectures'  => 'lectures',
            'tickets'   => 'tickets',
            'notices'   => 'notices',
            'events'    => 'events',
        );
        foreach ( $entities as $route => $table ) {
            register_rest_route( 'sms/v1', '/' . $route, array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => function ( WP_REST_Request $r ) use ( $table ) { return $this->collection( $table, $r ); },
                'permission_callback' => array( $this, 'permission_read' ),
            ) );
            register_rest_route( 'sms/v1', '/' . $route . '/(?P<id>\d+)', array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => function ( WP_REST_Request $r ) use ( $table ) {
                    global $wpdb;
                    $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sms_{$table} WHERE id=%d", (int) $r['id'] ) );
                    if ( ! $row ) return new WP_Error( 'not_found', 'Not found', array( 'status' => 404 ) );
                    return rest_ensure_response( $row );
                },
                'permission_callback' => array( $this, 'permission_read' ),
            ) );
        }
        register_rest_route( 'sms/v1', '/stats', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'stats' ),
            'permission_callback' => array( $this, 'permission_read' ),
        ) );
    }

    public function permission_read() { return is_user_logged_in(); }

    private function collection( $table, WP_REST_Request $r ) {
        global $wpdb;
        $tbl = $wpdb->prefix . 'sms_' . $table;
        $per = max( 1, min( 100, (int) ( $r->get_param( 'per_page' ) ?: 25 ) ) );
        $page = max( 1, (int) ( $r->get_param( 'page' ) ?: 1 ) );
        $offset = ( $page - 1 ) * $per;
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$tbl} ORDER BY id DESC LIMIT %d OFFSET %d", $per, $offset ) );
        $total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tbl}" );
        $resp = new WP_REST_Response( $rows );
        $resp->header( 'X-Total', (string) $total );
        return $resp;
    }

    public function stats() {
        global $wpdb;
        $p = $wpdb->prefix . 'sms_';
        return rest_ensure_response( array(
            'students'        => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students" ),
            'students_active' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE status='active'" ),
            'staff'           => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}staff" ),
            'classes'         => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}classes" ),
            'invoices'        => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}invoices" ),
            'invoices_unpaid' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}invoices WHERE status='unpaid'" ),
            'income'          => (float) $wpdb->get_var( "SELECT COALESCE(SUM(amount),0) FROM {$p}income" ),
            'expenses'        => (float) $wpdb->get_var( "SELECT COALESCE(SUM(amount),0) FROM {$p}expenses" ),
            'tickets_open'    => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}tickets WHERE status='open'" ),
        ) );
    }
}
