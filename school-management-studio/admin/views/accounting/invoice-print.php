<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'sms_';

$status   = isset( $_GET['sms_status'] )   ? sanitize_key( $_GET['sms_status'] )   : '';
$month    = isset( $_GET['sms_month'] )    ? (int) $_GET['sms_month']              : 0;
$year     = isset( $_GET['sms_year'] )     ? (int) $_GET['sms_year']               : 0;
$class_id = isset( $_GET['sms_class_id'] ) ? (int) $_GET['sms_class_id']           : 0;

$where = ' WHERE 1=1 '; $params = array();
if ( $status )   { $where .= " AND i.status=%s "; $params[] = $status; }
if ( $month && $year ) { $where .= " AND DATE_FORMAT(i.due_date,'%%Y-%%m')=%s "; $params[] = sprintf( '%04d-%02d', $year, $month ); }
if ( $class_id ) { $where .= " AND s.class_id=%d "; $params[] = $class_id; }

$sql = "SELECT i.*, CONCAT(s.first_name,' ',s.last_name) AS student, s.admission_no
        FROM {$p}invoices i LEFT JOIN {$p}students s ON s.id=i.student_id
        $where ORDER BY i.id DESC LIMIT 500";
$rows = $params ? $wpdb->get_results( $wpdb->prepare( $sql, $params ) ) : $wpdb->get_results( $sql );

$classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes" );
$months  = array( 1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec' );
?>
<div class="sms-page">
    <?php SMS_Helper::page_header( 'Invoice Print Center', 'Filter, preview and bulk-print invoices', 'printer' ); ?>

    <form class="sms-card" method="get" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
        <input type="hidden" name="page" value="sms-invoice-print">
        <div class="sms-field" style="min-width:140px"><label>Status</label>
            <select class="sms-select" name="sms_status">
                <option value="">All</option>
                <option value="unpaid" <?php selected( $status, 'unpaid' ); ?>>Unpaid</option>
                <option value="paid"   <?php selected( $status, 'paid' ); ?>>Paid</option>
                <option value="partial"<?php selected( $status, 'partial' ); ?>>Partial</option>
            </select>
        </div>
        <div class="sms-field" style="min-width:140px"><label>Class</label>
            <select class="sms-select" name="sms_class_id">
                <option value="0">All</option>
                <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>" <?php selected( $class_id, $c->id ); ?>><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="sms-field" style="min-width:120px"><label>Month</label>
            <select class="sms-select" name="sms_month">
                <option value="0">Any</option>
                <?php foreach ( $months as $k => $m ) : ?><option value="<?php echo (int) $k; ?>" <?php selected( $month, $k ); ?>><?php echo esc_html( $m ); ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="sms-field" style="min-width:120px"><label>Year</label>
            <input class="sms-input" type="number" name="sms_year" value="<?php echo $year ?: ''; ?>">
        </div>
        <button class="sms-btn sms-btn-primary"><?php echo SMS_Icons::svg('search',14); ?><span>Filter</span></button>
    </form>

    <div class="sms-card">
        <div class="sms-card-head">
            <div class="sms-card-title"><?php echo SMS_Icons::svg('list',18); ?> <?php echo count( $rows ); ?> matching invoice(s)</div>
            <div class="sms-flex">
                <button id="sms-bulk-print" class="sms-btn sms-btn-primary"><?php echo SMS_Icons::svg('printer',14); ?><span>Bulk Print</span></button>
                <a class="sms-btn sms-btn-ghost" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=sms_export&t=invoices' ), 'sms_export' ) ); ?>"><?php echo SMS_Icons::svg('download',14); ?><span>Export CSV</span></a>
            </div>
        </div>
        <div class="sms-table-wrap">
            <table class="sms-table">
                <thead><tr><th><input type="checkbox" id="sms-check-all"></th><th>Invoice #</th><th>Student</th><th>Amount</th><th>Paid</th><th>Due</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                    <tr>
                        <td><input type="checkbox" class="sms-row-check" value="<?php echo (int) $r->id; ?>"></td>
                        <td><strong><?php echo esc_html( $r->invoice_no ); ?></strong></td>
                        <td><?php echo esc_html( $r->student ); ?> <small class="sms-muted">(<?php echo esc_html( $r->admission_no ); ?>)</small></td>
                        <td><?php echo SMS_Helper::money( $r->amount ); ?></td>
                        <td><?php echo SMS_Helper::money( $r->paid ); ?></td>
                        <td><?php echo esc_html( $r->due_date ); ?></td>
                        <td><?php echo SMS_Helper::badge( $r->status ); ?></td>
                        <td><a class="sms-btn sms-btn-ghost sms-btn-sm" target="_blank" href="<?php echo esc_url( SMS_Helper::print_url('invoice', $r->id, true) ); ?>"><?php echo SMS_Icons::svg('printer',14); ?><span>Print</span></a></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="8" class="sms-muted" style="text-align:center;padding:30px">No invoices match the filter.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
jQuery(function($){
    $('#sms-check-all').on('change', function(){ $('.sms-row-check').prop('checked', this.checked); });
    $('#sms-bulk-print').on('click', function(){
        var ids = $('.sms-row-check:checked').map(function(){ return this.value; }).get();
        if(!ids.length){ alert('Select at least one invoice.'); return; }
        ids.forEach(function(id){
            window.open('<?php echo esc_url( admin_url('admin-post.php') ); ?>?action=sms_print&t=invoice&id=' + id + '&autoprint=1', '_blank');
        });
    });
});
</script>
