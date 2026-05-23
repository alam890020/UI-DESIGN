<?php
/**
 * Monthly fee generator.
 *
 * @package SchoolManagementStudio
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_Fee_Generator {

    public function register() {
        add_action( 'wp_ajax_sms_generate_monthly_fees', array( $this, 'ajax_generate' ) );
        add_action( 'wp_ajax_sms_preview_monthly_fees',  array( $this, 'ajax_preview' ) );
    }

    public static function generate( $args ) {
        $args = wp_parse_args( $args, array(
            'month'             => (int) date( 'n' ),
            'year'              => (int) date( 'Y' ),
            'class_id'          => 0,
            'fee_structure_ids' => array(),
            'dry_run'           => false,
            'due_day'           => 5,
        ) );

        global $wpdb;
        $p = $wpdb->prefix . 'sms_';

        $where = " WHERE status='active' "; $params = array();
        if ( ! empty( $args['class_id'] ) ) { $where .= " AND class_id=%d "; $params[] = (int) $args['class_id']; }
        $sql = "SELECT * FROM {$p}students" . $where;
        $students = $params ? $wpdb->get_results( $wpdb->prepare( $sql, $params ) ) : $wpdb->get_results( $sql );

        if ( ! empty( $args['fee_structure_ids'] ) ) {
            $ids   = array_map( 'intval', (array) $args['fee_structure_ids'] );
            $place = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
            $fees  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$p}fee_structures WHERE id IN ($place)", $ids ) );
        } else {
            $fees = $wpdb->get_results( "SELECT * FROM {$p}fee_structures WHERE frequency='monthly'" );
        }

        $month = max( 1, min( 12, (int) $args['month'] ) );
        $year  = (int) $args['year'];
        $due   = max( 1, min( 28, (int) $args['due_day'] ) );
        $due_date = sprintf( '%04d-%02d-%02d', $year, $month, $due );

        $created = 0; $skipped = 0; $total = 0; $invoices = array();

        foreach ( $students as $stu ) {
            $sum = 0.0; $applicable = array();
            foreach ( $fees as $f ) {
                if ( (int) $f->class_id === 0 || (int) $f->class_id === (int) $stu->class_id ) {
                    $applicable[] = $f; $sum += (float) $f->amount;
                }
            }
            if ( ! $applicable ) { $skipped++; continue; }

            // Apply concessions.
            $rows = $wpdb->get_results( $wpdb->prepare(
                "SELECT c.* FROM {$p}student_concessions sc INNER JOIN {$p}concession_types c ON c.id=sc.concession_type_id WHERE sc.student_id=%d",
                $stu->id
            ) );
            foreach ( $rows as $c ) {
                if ( 'percent' === $c->type ) $sum -= $sum * ( (float) $c->value / 100 );
                else $sum -= (float) $c->value;
            }
            $sum = max( 0, $sum );

            $exists = (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$p}invoices WHERE student_id=%d AND DATE_FORMAT(due_date,'%%Y-%%m')=%s",
                $stu->id, sprintf( '%04d-%02d', $year, $month )
            ) );
            if ( $exists ) { $skipped++; continue; }

            $invoice_no = sprintf( 'INV-%04d%02d-%05d', $year, $month, $stu->id );
            $row = array(
                'invoice_no' => $invoice_no,
                'student_id' => (int) $stu->id,
                'amount'     => $sum,
                'paid'       => 0,
                'due_date'   => $due_date,
                'status'     => 'unpaid',
                'created_at' => current_time( 'mysql' ),
            );
            $invoices[] = $row; $total += $sum;
            if ( ! $args['dry_run'] ) $wpdb->insert( $p . 'invoices', $row );
            $created++;
        }

        return array(
            'created'      => $created,
            'skipped'      => $skipped,
            'total_amount' => $total,
            'invoices'     => $invoices,
            'month'        => $month,
            'year'         => $year,
        );
    }

    public function ajax_preview() {
        check_ajax_referer( 'sms_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'unauthorized' );
        $args = isset( $_POST['args'] ) ? (array) $_POST['args'] : array();
        $args['dry_run'] = true;
        wp_send_json_success( self::generate( $args ) );
    }

    public function ajax_generate() {
        check_ajax_referer( 'sms_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'unauthorized' );
        $args = isset( $_POST['args'] ) ? (array) $_POST['args'] : array();
        wp_send_json_success( self::generate( $args ) );
    }
}
