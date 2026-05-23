<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$students = $wpdb->get_results( "SELECT * FROM {$p}students LIMIT 200" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Collect Payments', 'Record fee payments and issue receipts', 'dashicons-money-alt' ); ?>
    <div class="ssm-grid-2">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-search"></span> Find Student</div></div>
            <input class="ssm-input" placeholder="Type name, admission no…">
            <div class="ssm-list" style="margin-top:12px">
                <?php foreach ( $students as $s ) : $name = trim( $s->first_name . ' ' . $s->last_name ); ?>
                    <div class="ssm-list-item" style="border:1px solid var(--ssm-border)">
                        <?php echo SSM_Helper::avatar( $name ); ?>
                        <div style="flex:1">
                            <strong><?php echo esc_html( $name ); ?></strong>
                            <div class="meta"><?php echo esc_html( $s->admission_no ); ?> &middot; Class <?php echo (int) $s->class_id; ?></div>
                        </div>
                        <button class="ssm-btn ssm-btn-primary ssm-btn-sm">Collect</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-money"></span> Receipt</div></div>
            <form>
                <div class="ssm-form-grid">
                    <div class="ssm-field"><label>Amount</label><input class="ssm-input" type="number" step="0.01"></div>
                    <div class="ssm-field"><label>Method</label>
                        <select class="ssm-select"><option>Cash</option><option>Card</option><option>UPI</option><option>Bank Transfer</option><option>Cheque</option></select>
                    </div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Date</label><input class="ssm-input" type="date" value="<?php echo esc_attr( date('Y-m-d') ); ?>"></div>
                    <div class="ssm-field"><label>Reference</label><input class="ssm-input" placeholder="TXN / Cheque #"></div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Note</label><textarea class="ssm-textarea"></textarea></div>
                <div style="margin-top:14px;display:flex;gap:8px">
                    <button class="ssm-btn ssm-btn-primary" type="button"><span class="dashicons dashicons-saved"></span> Record Payment</button>
                    <button class="ssm-btn ssm-btn-ghost" type="button"><span class="dashicons dashicons-printer"></span> Print Receipt</button>
                </div>
            </form>
        </div>
    </div>
</div>
