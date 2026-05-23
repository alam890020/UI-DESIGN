<?php
/**
 * AJAX endpoints for CRUD on key SSM entities.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Ajax {

    public function register() {
        $entities = array( 'student', 'staff', 'class_item', 'subject', 'fee_structure',
            'invoice', 'book', 'route', 'vehicle', 'hostel', 'room', 'session_item',
            'category', 'medium', 'student_type', 'role', 'admin', 'admission',
            'inquiry', 'notice', 'event', 'activity', 'meeting', 'homework_item',
            'study_material', 'lecture', 'chapter', 'ticket', 'exam_item', 'exam_group',
            'income_item', 'expense_item', 'concession_type', 'school',
        );

        foreach ( array( 'save', 'delete' ) as $op ) {
            foreach ( $entities as $e ) {
                add_action( "wp_ajax_ssm_{$op}_{$e}", array( $this, "{$op}_entity" ) );
            }
        }

        add_action( 'wp_ajax_ssm_save_settings', array( $this, 'save_settings' ) );
        add_action( 'wp_ajax_ssm_quick_search',  array( $this, 'quick_search' ) );
    }

    /**
     * Map entity slugs to table names.
     */
    private function table_for( $entity ) {
        $map = array(
            'student'         => 'students',
            'staff'           => 'staff',
            'class_item'      => 'classes',
            'subject'         => 'subjects',
            'fee_structure'   => 'fee_structures',
            'invoice'         => 'invoices',
            'book'            => 'books',
            'route'           => 'routes',
            'vehicle'         => 'vehicles',
            'hostel'          => 'hostels',
            'room'            => 'hostel_rooms',
            'session_item'    => 'sessions',
            'category'        => 'categories',
            'medium'          => 'mediums',
            'student_type'    => 'student_types',
            'role'            => 'roles',
            'admin'           => 'admins',
            'admission'       => 'admissions',
            'inquiry'         => 'inquiries',
            'notice'          => 'notices',
            'event'           => 'events',
            'activity'        => 'activities',
            'meeting'         => 'meetings',
            'homework_item'   => 'homework',
            'study_material'  => 'study_materials',
            'lecture'         => 'lectures',
            'chapter'         => 'chapters',
            'ticket'          => 'tickets',
            'exam_item'       => 'exams',
            'exam_group'      => 'exam_groups',
            'income_item'     => 'income',
            'expense_item'    => 'expenses',
            'concession_type' => 'concession_types',
            'school'          => 'schools',
        );
        return isset( $map[ $entity ] ) ? $map[ $entity ] : null;
    }

    public function save_entity() {
        check_ajax_referer( 'ssm_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'unauthorized' );

        $action = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
        $entity = preg_replace( '/^ssm_save_/', '', $action );
        $table  = $this->table_for( $entity );
        if ( ! $table ) wp_send_json_error( 'bad entity' );

        global $wpdb;
        $tbl = $wpdb->prefix . 'ssm_' . $table;
        $data = isset( $_POST['data'] ) ? (array) $_POST['data'] : array();
        $clean = array();
        foreach ( $data as $k => $v ) {
            $k = sanitize_key( $k );
            $clean[ $k ] = is_array( $v ) ? wp_json_encode( $v ) : sanitize_text_field( wp_unslash( $v ) );
        }

        $id = isset( $clean['id'] ) ? (int) $clean['id'] : 0;
        unset( $clean['id'] );

        if ( $id > 0 ) {
            $wpdb->update( $tbl, $clean, array( 'id' => $id ) );
        } else {
            $wpdb->insert( $tbl, $clean );
            $id = $wpdb->insert_id;
        }
        $this->log( $entity, $id ? 'saved' : 'save_failed' );
        wp_send_json_success( array( 'id' => $id ) );
    }

    public function delete_entity() {
        check_ajax_referer( 'ssm_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'unauthorized' );

        $action = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
        $entity = preg_replace( '/^ssm_delete_/', '', $action );
        $table  = $this->table_for( $entity );
        if ( ! $table ) wp_send_json_error( 'bad entity' );

        $id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
        if ( $id <= 0 ) wp_send_json_error( 'bad id' );

        global $wpdb;
        $tbl = $wpdb->prefix . 'ssm_' . $table;
        $wpdb->delete( $tbl, array( 'id' => $id ) );
        $this->log( $entity, 'deleted', $id );
        wp_send_json_success();
    }

    public function save_settings() {
        check_ajax_referer( 'ssm_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'unauthorized' );
        $opts = isset( $_POST['data'] ) ? (array) $_POST['data'] : array();
        $clean = array();
        foreach ( $opts as $k => $v ) {
            $clean[ sanitize_key( $k ) ] = sanitize_text_field( wp_unslash( $v ) );
        }
        update_option( 'ssm_settings', array_merge( get_option( 'ssm_settings', array() ), $clean ) );
        wp_send_json_success();
    }

    public function quick_search() {
        check_ajax_referer( 'ssm_nonce', 'nonce' );
        global $wpdb;
        $q = isset( $_POST['q'] ) ? '%' . $wpdb->esc_like( sanitize_text_field( wp_unslash( $_POST['q'] ) ) ) . '%' : '%';
        $p = $wpdb->prefix . 'ssm_';
        $students = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, first_name, last_name, admission_no FROM {$p}students WHERE first_name LIKE %s OR last_name LIKE %s OR admission_no LIKE %s LIMIT 10",
            $q, $q, $q
        ) );
        $staff = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, first_name, last_name, employee_no FROM {$p}staff WHERE first_name LIKE %s OR last_name LIKE %s OR employee_no LIKE %s LIMIT 10",
            $q, $q, $q
        ) );
        wp_send_json_success( array( 'students' => $students, 'staff' => $staff ) );
    }

    private function log( $module, $action, $id = 0 ) {
        global $wpdb;
        $wpdb->insert( $wpdb->prefix . 'ssm_logs', array(
            'user_id' => get_current_user_id(),
            'action'  => $action . ( $id ? ' #' . $id : '' ),
            'module'  => $module,
            'ip'      => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '',
        ) );
    }
}
