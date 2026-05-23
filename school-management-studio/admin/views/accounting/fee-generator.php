<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'sms_';
$classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes ORDER BY name" );
$fees    = $wpdb->get_results( "SELECT id, name, amount, frequency FROM {$p}fee_structures ORDER BY id DESC" );
$months  = array( 1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June', 7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December' );
$year    = (int) date( 'Y' );
?>
<div class="sms-page">
    <?php SMS_Helper::page_header( 'Monthly Fee Generator', 'Bulk-generate invoices for all active students', 'switch-h',
        array( array( 'href' => SMS_Helper::admin_url('sms-invoices'), 'label' => 'View Invoices', 'icon' => 'invoice', 'class' => 'sms-btn-ghost' ) ) ); ?>

    <div class="sms-grid-2-1">
        <div class="sms-card">
            <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('switch-h',18); ?> Generation Parameters</div></div>
            <form id="sms-fee-gen-form">
                <div class="sms-grid-3">
                    <div class="sms-field"><label>Month</label>
                        <select class="sms-select" name="month">
                            <?php foreach ( $months as $k => $m ) : ?><option value="<?php echo (int) $k; ?>" <?php selected( $k, (int) date( 'n' ) ); ?>><?php echo esc_html( $m ); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sms-field"><label>Year</label>
                        <select class="sms-select" name="year">
                            <?php for ( $y = $year - 2; $y <= $year + 2; $y++ ) : ?><option value="<?php echo (int) $y; ?>" <?php selected( $y, $year ); ?>><?php echo (int) $y; ?></option><?php endfor; ?>
                        </select>
                    </div>
                    <div class="sms-field"><label>Due Day</label><input class="sms-input" type="number" name="due_day" min="1" max="28" value="5"></div>
                </div>
                <div class="sms-grid-2" style="margin-top:14px">
                    <div class="sms-field"><label>Class</label>
                        <select class="sms-select" name="class_id">
                            <option value="0">All Classes</option>
                            <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sms-field"><label>Fee Structures</label>
                        <select class="sms-select" name="fee_structure_ids[]" multiple size="4">
                            <?php foreach ( $fees as $f ) : ?><option value="<?php echo (int) $f->id; ?>"><?php echo esc_html( $f->name . ' (' . SMS_Helper::money( $f->amount ) . '/' . $f->frequency . ')' ); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="sms-flex" style="margin-top:18px">
                    <button type="button" id="sms-fee-preview" class="sms-btn sms-btn-ghost"><?php echo SMS_Icons::svg('eye',14); ?><span>Preview (Dry Run)</span></button>
                    <button type="button" id="sms-fee-generate" class="sms-btn sms-btn-primary"><?php echo SMS_Icons::svg('check',14); ?><span>Generate Invoices</span></button>
                </div>
            </form>
        </div>

        <div class="sms-card">
            <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('book',18); ?> How it works</div></div>
            <ol class="sms-muted" style="line-height:1.8;font-size:13px;padding-left:18px;margin:0">
                <li>Sums matching fees for each <strong>active student</strong>.</li>
                <li>Applies any active concession (percent or flat).</li>
                <li>Creates a unique invoice <code>INV-YYYYMM-XXXXX</code>.</li>
                <li>Skips students who already have an invoice for that month.</li>
                <li>Use <strong>Preview</strong> first to verify before committing.</li>
            </ol>
        </div>
    </div>

    <div class="sms-card" id="sms-fee-result" style="display:none">
        <div class="sms-card-head">
            <div class="sms-card-title"><?php echo SMS_Icons::svg('chart-bar',18); ?> Result</div>
            <div id="sms-fee-summary" class="sms-muted"></div>
        </div>
        <div class="sms-table-wrap">
            <table class="sms-table" id="sms-fee-table">
                <thead><tr><th>Invoice #</th><th>Student ID</th><th>Amount</th><th>Due</th><th>Status</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
jQuery(function($){
    function payload(dry){
        return { action: dry ? 'sms_preview_monthly_fees' : 'sms_generate_monthly_fees', nonce: SMS.nonce, args: {
            month:    $('select[name=month]').val(),
            year:     $('select[name=year]').val(),
            due_day:  $('input[name=due_day]').val(),
            class_id: $('select[name=class_id]').val(),
            fee_structure_ids: $('select[name="fee_structure_ids[]"]').val() || []
        } };
    }
    function render(r){
        $('#sms-fee-result').show();
        $('#sms-fee-summary').html('Created: <strong>'+r.created+'</strong> · Skipped: <strong>'+r.skipped+'</strong> · Total: <strong>'+SMS.currency+(r.total_amount||0).toFixed(2)+'</strong>');
        var $tb = $('#sms-fee-table tbody').empty();
        (r.invoices||[]).slice(0,500).forEach(function(i){
            $tb.append('<tr><td><strong>'+i.invoice_no+'</strong></td><td>'+i.student_id+'</td><td>'+SMS.currency+parseFloat(i.amount).toFixed(2)+'</td><td>'+i.due_date+'</td><td><span class="sms-pill sms-pill-warn">'+i.status+'</span></td></tr>');
        });
    }
    $('#sms-fee-preview').on('click', function(){ $.post(SMS.ajaxUrl, payload(true)).done(function(r){ if(r&&r.success) render(r.data); }); });
    $('#sms-fee-generate').on('click', function(){
        if(!confirm('Generate invoices for the selected month? This is permanent.')) return;
        $.post(SMS.ajaxUrl, payload(false)).done(function(r){ if(r&&r.success){ render(r.data); alert('Generated '+r.data.created+' invoice(s).'); } });
    });
});
</script>
