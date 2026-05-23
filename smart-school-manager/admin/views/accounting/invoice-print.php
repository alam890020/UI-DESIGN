<?php
/**
 * Print all invoices: filterable list with one-click print buttons + bulk print.
 *
 * @package SmartSchoolManager
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb; $p = $wpdb->prefix . 'ssm_';

$status   = isset( $_GET['ssm_status'] )   ? sanitize_key( $_GET['ssm_status'] )   : '';
$month    = isset( $_GET['ssm_month'] )    ? (int) $_GET['ssm_month']              : 0;
$year     = isset( $_GET['ssm_year'] )     ? (int) $_GET['ssm_year']               : 0;
$class_id = isset( $_GET['ssm_class_id'] ) ? (int) $_GET['ssm_class_id']           : 0;

$where = ' WHERE 1=1 ';
$params = array();
if ( $status ) { $where .= " AND i.status=%s "; $params[] = $status; }
if ( $month && $year ) { $where .= " AND DATE_FORMAT(i.due_date,'%%Y-%%m')=%s "; $params[] = sprintf( '%04d-%02d', $year, $month ); }
if ( $class_id ) { $where .= " AND s.class_id=%d "; $params[] = $class_id; }

$sql = "SELECT i.*, CONCAT(s.first_name,' ',s.last_name) AS student, s.admission_no, s.class_id
        FROM {$p}invoices i
        LEFT JOIN {$p}students s ON s.id=i.student_id
        $where ORDER BY i.id DESC LIMIT 500";

$rows = $params ? $wpdb->get_results( $wpdb->prepare( $sql, $params ) ) : $wpdb->get_results( $sql );

$classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes" );
$months  = array( 1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec' );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Invoice Print Center', 'Filter, preview and bulk-print invoices', 'dashicons-printer' ); ?>

    <form class="ssm-card" method="get" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
        <input type="hidden" name="page" value="ssm-invoice-print">
        <div class="ssm-field" style="min-width:140px"><label>Status</label>
            <select class="ssm-select" name="ssm_status">
                <option value="">All</option>
                <option value="unpaid" <?php selected( $status, 'unpaid' ); ?>>Unpaid</option>
                <option value="paid"   <?php selected( $status, 'paid' ); ?>>Paid</option>
                <option value="partial"<?php selected( $status, 'partial' ); ?>>Partial</option>
            </select>
        </div>
        <div class="ssm-field" style="min-width:140px"><label>Class</label>
            <select class="ssm-select" name="ssm_class_id">
                <option value="0">All</option>
                <?php foreach ( $classes as $c ) : ?>
                    <option value="<?php echo (int) $c->id; ?>" <?php selected( $class_id, $c->id ); ?>><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="ssm-field" style="min-width:140px"><label>Month</label>
            <select class="ssm-select" name="ssm_month">
                <option value="0">Any</option>
                <?php foreach ( $months as $k => $m ) : ?><option value="<?php echo (int) $k; ?>" <?php selected( $month, $k ); ?>><?php echo esc_html( $m ); ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="ssm-field" style="min-width:120px"><label>Year</label>
            <input class="ssm-input" type="number" name="ssm_year" value="<?php echo $year ?: ''; ?>">
        </div>
        <button class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-search"></span> Filter</button>
    </form>

    <div class="ssm-card">
        <div class="ssm-card-header">
            <div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> <?php echo count( $rows ); ?> matching invoice(s)</div>
            <div style="display:flex;gap:8px">
                <button id="ssm-bulk-print" class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-printer"></span> Bulk Print</button>
                <a class="ssm-btn ssm-btn-ghost" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=ssm_export&t=invoices' ), 'ssm_export' ) ); ?>"><span class="dashicons dashicons-download"></span> Export CSV</a>
            </div>
        </div>
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th><input type="checkbox" id="ssm-check-all"></th><th>Invoice #</th><th>Student</th><th>Amount</th><th>Paid</th><th>Due</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                    <tr>
                        <td><input type="checkbox" class="ssm-row-check" value="<?php echo (int) $r->id; ?>"></td>
                        <td><strong><?php echo esc_html( $r->invoice_no ); ?></strong></td>
                        <td><?php echo esc_html( $r->student ); ?> <small class="ssm-muted">(<?php echo esc_html( $r->admission_no ); ?>)</small></td>
                        <td><?php echo SSM_Helper::money( $r->amount ); ?></td>
                        <td><?php echo SSM_Helper::money( $r->paid ); ?></td>
                        <td><?php echo esc_html( $r->due_date ); ?></td>
                        <td><?php echo SSM_Helper::badge( $r->status ); ?></td>
                        <td>
                            <a class="ssm-btn ssm-btn-ghost ssm-btn-sm" target="_blank" href="<?php echo esc_url( SSM_Helper::print_url( 'invoice', $r->id, true ) ); ?>"><span class="dashicons dashicons-printer"></span> Print</a>
                        </td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="8" class="ssm-muted" style="text-align:center;padding:30px">No invoices match the filter.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
jQuery(function($){
    $('#ssm-check-all').on('change', function(){ $('.ssm-row-check').prop('checked', this.checked); });
    $('#ssm-bulk-print').on('click', function(){
        var ids = $('.ssm-row-check:checked').map(function(){ return this.value; }).get();
        if(!ids.length){ alert('Select at least one invoice.'); return; }
        ids.forEach(function(id){
            window.open('<?php echo esc_url( admin_url('admin-post.php') ); ?>?action=ssm_print&t=invoice&id=' + id + '&autoprint=1', '_blank');
        });
    });
});
</script>
