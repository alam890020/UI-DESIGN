<?php
/**
 * REST API endpoints under the /ssm/v1 namespace.
 *
 * Provides read-only collection endpoints suitable for headless / mobile apps.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_REST {

    const NS = 'ssm/v1';

    public function register() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
        $entities = array(
            'students'        => 'students',
            'staff'           => 'staff',
            'classes'         => 'classes',
            'subjects'        => 'subjects',
            'sessions'        => 'sessions',
            'fees'            => 'fee_structures',
            'invoices'        => 'invoices',
            'books'           => 'books',
            'routes'          => 'routes',
            'vehicles'        => 'vehicles',
            'hostels'         => 'hostels',
            'rooms'           => 'hostel_rooms',
            'lectures'        => 'lectures',
            'chapters'        => 'chapters',
            'tickets'         => 'tickets',
            'notices'         => 'notices',
            'events'          => 'events',
            'admissions'      => 'admissions',
            'inquiries'       => 'inquiries',
            'income'          => 'income',
            'expenses'        => 'expenses',
        );

        foreach ( $entities as $route => $table ) {
            register_rest_route( self::NS, '/' . $route, array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => function ( WP_REST_Request $r ) use ( $table ) {
                    return $this->collection( $table, $r );
                },
                'permission_callback' => array( $this, 'permission_read' ),
                'args'                => $this->collection_args(),
            ) );

            register_rest_route( self::NS, '/' . $route . '/(?P<id>\d+)', array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => function ( WP_REST_Request $r ) use ( $table ) {
                    return $this->single( $table, (int) $r['id'] );
                },
                'permission_callback' => array( $this, 'permission_read' ),
            ) );
        }

        // Stats summary.
        register_rest_route( self::NS, '/stats', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'stats' ),
            'permission_callback' => array( $this, 'permission_read' ),
        ) );

        // Search across students/staff.
        register_rest_route( self::NS, '/search', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'search' ),
            'permission_callback' => array( $this, 'permission_read' ),
            'args'                => array(
                'q' => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
            ),
        ) );
    }

    public function permission_read() {
        return is_user_logged_in();
    }

    private function collection_args() {
        return array(
            'per_page' => array( 'default' => 25, 'sanitize_callback' => 'absint' ),
            'page'     => array( 'default' => 1,  'sanitize_callback' => 'absint' ),
            'search'   => array( 'sanitize_callback' => 'sanitize_text_field' ),
        );
    }

    private function collection( $table, WP_REST_Request $r ) {
        global $wpdb;
        $tbl     = $wpdb->prefix . 'ssm_' . $table;
        $per     = max( 1, min( 100, (int) $r->get_param( 'per_page' ) ) );
        $page    = max( 1, (int) $r->get_param( 'page' ) );
        $offset  = ( $page - 1 ) * $per;
        $search  = (string) $r->get_param( 'search' );

        $where  = '';
        $params = array();
        if ( $search ) {
            // Use a generic LIKE on a few common cols if they exist.
            $cols     = $wpdb->get_col( "DESCRIBE {$tbl}", 0 );
            $textCols = array_intersect( array( 'name', 'title', 'first_name', 'last_name', 'subject', 'invoice_no' ), $cols );
            if ( $textCols ) {
                $like     = '%' . $wpdb->esc_like( $search ) . '%';
                $clauses  = array();
                foreach ( $textCols as $c ) { $clauses[] = "{$c} LIKE %s"; $params[] = $like; }
                $where    = ' WHERE ' . implode( ' OR ', $clauses );
            }
        }

        $total_sql = "SELECT COUNT(*) FROM {$tbl}{$where}";
        $total     = $params ? (int) $wpdb->get_var( $wpdb->prepare( $total_sql, $params ) ) : (int) $wpdb->get_var( $total_sql );

        $sql_params = array_merge( $params, array( $per, $offset ) );
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$tbl}{$where} ORDER BY id DESC LIMIT %d OFFSET %d",
            $sql_params
        ) );

        $resp = new WP_REST_Response( $rows );
        $resp->header( 'X-Total', (string) $total );
        $resp->header( 'X-Pages', (string) (int) ceil( $total / $per ) );
        return $resp;
    }

    private function single( $table, $id ) {
        global $wpdb;
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ssm_{$table} WHERE id=%d",
            $id
        ) );
        if ( ! $row ) {
            return new WP_Error( 'ssm_not_found', 'Not found', array( 'status' => 404 ) );
        }
        return rest_ensure_response( $row );
    }

    public function stats() {
        global $wpdb;
        $p = $wpdb->prefix . 'ssm_';
        return rest_ensure_response( array(
            'students'         => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students" ),
            'students_active'  => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE status='active'" ),
            'staff'            => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}staff" ),
            'classes'          => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}classes" ),
            'invoices'         => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}invoices" ),
            'invoices_unpaid'  => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}invoices WHERE status='unpaid'" ),
            'income'           => (float) $wpdb->get_var( "SELECT COALESCE(SUM(amount),0) FROM {$p}income" ),
            'expenses'         => (float) $wpdb->get_var( "SELECT COALESCE(SUM(amount),0) FROM {$p}expenses" ),
            'tickets_open'     => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}tickets WHERE status='open'" ),
        ) );
    }

    public function search( WP_REST_Request $r ) {
        global $wpdb;
        $p    = $wpdb->prefix . 'ssm_';
        $q    = '%' . $wpdb->esc_like( (string) $r->get_param( 'q' ) ) . '%';
        $stu  = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, first_name, last_name, admission_no, class_id FROM {$p}students WHERE first_name LIKE %s OR last_name LIKE %s OR admission_no LIKE %s LIMIT 20",
            $q, $q, $q
        ) );
        $stf  = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, first_name, last_name, employee_no, designation FROM {$p}staff WHERE first_name LIKE %s OR last_name LIKE %s OR employee_no LIKE %s LIMIT 20",
            $q, $q, $q
        ) );
        return rest_ensure_response( array( 'students' => $stu, 'staff' => $stf ) );
    }
}
