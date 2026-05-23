<?php
/**
 * Monthly fee generator.
 *
 * Bulk-creates invoices for every active student in a class (or all classes)
 * based on the configured fee structures, for a chosen month/year.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Fee_Generator {

    public function register() {
        add_action( 'wp_ajax_ssm_generate_monthly_fees', array( $this, 'ajax_generate' ) );
        add_action( 'wp_ajax_ssm_preview_monthly_fees',  array( $this, 'ajax_preview' ) );
    }

    /**
     * Run the generator.
     *
     * @param array $args month, year, class_id (0 = all), fee_structure_ids[], dry_run, due_day
     * @return array { created, skipped, total_amount, invoices }
     */
    public static function generate( $args ) {
        $args = wp_parse_args( $args, array(
            'month'              => (int) date( 'n' ),
            'year'               => (int) date( 'Y' ),
            'class_id'           => 0,
            'fee_structure_ids'  => array(),
            'dry_run'            => false,
            'due_day'            => 5,
        ) );

        global $wpdb;
        $p = $wpdb->prefix . 'ssm_';

        // Resolve students.
        $where  = " WHERE status='active' ";
        $params = array();
        if ( ! empty( $args['class_id'] ) ) {
            $where   .= " AND class_id = %d ";
            $params[] = (int) $args['class_id'];
        }
        $sql      = "SELECT * FROM {$p}students" . $where;
        $students = $params ? $wpdb->get_results( $wpdb->prepare( $sql, $params ) ) : $wpdb->get_results( $sql );

        // Resolve fee structures.
        if ( ! empty( $args['fee_structure_ids'] ) ) {
            $ids   = array_map( 'intval', (array) $args['fee_structure_ids'] );
            $place = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
            $fees  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$p}fee_structures WHERE id IN ($place)", $ids ) );
        } else {
            // Pick monthly fees applicable to each student's class (or 0 = any).
            $fees = $wpdb->get_results( "SELECT * FROM {$p}fee_structures WHERE frequency='monthly'" );
        }

        $month = max( 1, min( 12, (int) $args['month'] ) );
        $year  = (int) $args['year'];
        $due   = max( 1, min( 28, (int) $args['due_day'] ) );
        $due_date = sprintf( '%04d-%02d-%02d', $year, $month, $due );
        $created  = 0; $skipped = 0; $total = 0;
        $invoices = array();

        foreach ( $students as $stu ) {
            // Pick fees for this student's class (or generic fees with class_id=0).
            $applicable = array();
            $sum = 0.0;
            foreach ( $fees as $f ) {
                if ( (int) $f->class_id === 0 || (int) $f->class_id === (int) $stu->class_id ) {
                    $applicable[] = $f;
                    $sum         += (float) $f->amount;
                }
            }
            if ( ! $applicable ) { $skipped++; continue; }

            // Apply concession(s).
            $sum = self::apply_concessions( $stu->id, $sum );

            // Skip if invoice for this month/student already exists.
            $exists = (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$p}invoices WHERE student_id=%d AND DATE_FORMAT(due_date,'%%Y-%%m')=%s",
                $stu->id,
                sprintf( '%04d-%02d', $year, $month )
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
            $invoices[] = $row;
            $total     += $sum;

            if ( ! $args['dry_run'] ) {
                $wpdb->insert( $p . 'invoices', $row );
            }
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

    /**
     * Reduce an amount by any active concession.
     */
    public static function apply_concessions( $student_id, $amount ) {
        global $wpdb; $p = $wpdb->prefix . 'ssm_';
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT c.* FROM {$p}student_concessions sc INNER JOIN {$p}concession_types c ON c.id=sc.concession_type_id WHERE sc.student_id=%d",
            $student_id
        ) );
        foreach ( $rows as $c ) {
            if ( 'percent' === $c->type ) {
                $amount -= $amount * ( (float) $c->value / 100 );
            } else {
                $amount -= (float) $c->value;
            }
        }
        return max( 0, $amount );
    }

    public function ajax_preview() {
        check_ajax_referer( 'ssm_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'unauthorized' );
        $args = isset( $_POST['args'] ) ? (array) $_POST['args'] : array();
        $args['dry_run'] = true;
        $r = self::generate( $args );
        wp_send_json_success( $r );
    }

    public function ajax_generate() {
        check_ajax_referer( 'ssm_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'unauthorized' );
        $args = isset( $_POST['args'] ) ? (array) $_POST['args'] : array();
        $r = self::generate( $args );
        wp_send_json_success( $r );
    }
}
