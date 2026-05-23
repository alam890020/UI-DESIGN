<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}concession_types ORDER BY id" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Concession Types', 'Define discount/concession categories', 'dashicons-tag' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Name</th><th>Type</th><th>Value</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $r->name ); ?></strong></td>
                            <td><span class="ssm-badge ssm-badge-info"><?php echo esc_html( $r->type ); ?></span></td>
                            <td><strong><?php echo esc_html( $r->value ); ?><?php echo $r->type === 'percent' ? '%' : ''; ?></strong></td>
                            <td><div class="ssm-row-actions"><a href="#" class="del" data-entity="concession_type" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a></div></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Concession</div>
            <form class="ssm-ajax-form" data-entity="concession_type" style="margin-top:12px">
                <div class="ssm-field"><label>Name</label><input class="ssm-input" name="name" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Type</label>
                        <select class="ssm-select" name="type"><option value="percent">Percent (%)</option><option value="amount">Flat Amount</option></select>
                    </div>
                    <div class="ssm-field"><label>Value</label><input class="ssm-input" type="number" step="0.01" name="value"></div>
                </div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
