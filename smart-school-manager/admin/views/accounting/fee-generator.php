<?php
/**
 * Monthly Fee Generator UI.
 *
 * @package SmartSchoolManager
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb; $p = $wpdb->prefix . 'ssm_';
$classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes ORDER BY name" );
$fees    = $wpdb->get_results( "SELECT id, name, amount, frequency FROM {$p}fee_structures ORDER BY id DESC" );
$months  = array( 1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June',
                  7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December' );
$year    = (int) date( 'Y' );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header(
        'Monthly Fee Generator',
        'Bulk-generate monthly invoices for all active students',
        'dashicons-update',
        array(
            array( 'href' => SSM_Helper::admin_url('ssm-invoices'), 'label' => 'View Invoices', 'icon' => 'dashicons-media-document', 'class' => 'ssm-btn-ghost' ),
            array( 'href' => SSM_Helper::admin_url('ssm-fees'),     'label' => 'Manage Fees',   'icon' => 'dashicons-money-alt',      'class' => 'ssm-btn-ghost' ),
        )
    ); ?>

    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-update"></span> Generation Parameters</div></div>

            <form id="ssm-fee-gen-form">
                <div class="ssm-form-grid-3">
                    <div class="ssm-field">
                        <label>Month</label>
                        <select class="ssm-select" name="month">
                            <?php foreach ( $months as $k => $m ) : ?>
                                <option value="<?php echo (int) $k; ?>" <?php selected( $k, (int) date( 'n' ) ); ?>><?php echo esc_html( $m ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="ssm-field">
                        <label>Year</label>
                        <select class="ssm-select" name="year">
                            <?php for ( $y = $year - 2; $y <= $year + 2; $y++ ) : ?>
                                <option value="<?php echo (int) $y; ?>" <?php selected( $y, $year ); ?>><?php echo (int) $y; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="ssm-field">
                        <label>Due Day</label>
                        <input class="ssm-input" type="number" name="due_day" min="1" max="28" value="5">
                    </div>
                </div>

                <div class="ssm-form-grid" style="margin-top:14px">
                    <div class="ssm-field">
                        <label>Class</label>
                        <select class="ssm-select" name="class_id">
                            <option value="0">All Classes</option>
                            <?php foreach ( $classes as $c ) : ?>
                                <option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="ssm-field">
                        <label>Fee Structures (leave blank for monthly fees)</label>
                        <select class="ssm-select" name="fee_structure_ids[]" multiple size="4">
                            <?php foreach ( $fees as $f ) : ?>
                                <option value="<?php echo (int) $f->id; ?>"><?php echo esc_html( $f->name . ' (' . SSM_Helper::money( $f->amount ) . ' / ' . $f->frequency . ')' ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="margin-top:16px;display:flex;gap:8px;flex-wrap:wrap">
                    <button type="button" id="ssm-fee-preview" class="ssm-btn ssm-btn-ghost"><span class="dashicons dashicons-visibility"></span> Preview (Dry Run)</button>
                    <button type="button" id="ssm-fee-generate" class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-saved"></span> Generate Invoices</button>
                </div>
            </form>
        </div>

        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-info"></span> How it works</div></div>
            <ol class="ssm-muted" style="line-height:1.8;font-size:13px;padding-left:18px;margin:0">
                <li>For each <strong>active student</strong>, sums the matching fee structures.</li>
                <li>Applies any active concession (percent or flat).</li>
                <li>Creates a unique invoice numbered <code>INV-YYYYMM-XXXXX</code>.</li>
                <li>Skips students who already have an invoice for the chosen month.</li>
                <li>Use <strong>Preview</strong> first to verify amounts before committing.</li>
            </ol>
        </div>
    </div>

    <div class="ssm-card" id="ssm-fee-result" style="display:none">
        <div class="ssm-card-header">
            <div class="ssm-card-title"><span class="dashicons dashicons-chart-bar"></span> Result</div>
            <div id="ssm-fee-summary" class="ssm-muted"></div>
        </div>
        <div class="ssm-table-wrap">
            <table class="ssm-table" id="ssm-fee-table">
                <thead><tr><th>Invoice #</th><th>Student ID</th><th>Amount</th><th>Due Date</th><th>Status</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
jQuery(function($){
    function payload(dry){
        var args = {
            month:    $('select[name=month]').val(),
            year:     $('select[name=year]').val(),
            due_day:  $('input[name=due_day]').val(),
            class_id: $('select[name=class_id]').val(),
            fee_structure_ids: $('select[name="fee_structure_ids[]"]').val() || []
        };
        return { action: dry ? 'ssm_preview_monthly_fees' : 'ssm_generate_monthly_fees', nonce: SSM.nonce, args: args };
    }
    function render(r){
        $('#ssm-fee-result').show();
        $('#ssm-fee-summary').html('Created: <strong>'+r.created+'</strong> &middot; Skipped: <strong>'+r.skipped+'</strong> &middot; Total: <strong>'+SSM.currency+(r.total_amount||0).toFixed(2)+'</strong>');
        var $tb = $('#ssm-fee-table tbody').empty();
        (r.invoices||[]).slice(0,500).forEach(function(i){
            $tb.append('<tr><td><strong>'+i.invoice_no+'</strong></td><td>'+i.student_id+'</td><td>'+SSM.currency+parseFloat(i.amount).toFixed(2)+'</td><td>'+i.due_date+'</td><td><span class="ssm-badge ssm-badge-warning">'+i.status+'</span></td></tr>');
        });
    }
    $('#ssm-fee-preview').on('click', function(){
        $.post(SSM.ajaxUrl, payload(true)).done(function(r){
            if(r && r.success){ render(r.data); } else { alert('Preview failed.'); }
        });
    });
    $('#ssm-fee-generate').on('click', function(){
        if(!confirm('Generate invoices for the selected month? This is permanent.')) return;
        $.post(SSM.ajaxUrl, payload(false)).done(function(r){
            if(r && r.success){ render(r.data); alert('Generated '+r.data.created+' invoice(s).'); } else { alert('Generation failed.'); }
        });
    });
});
</script>
