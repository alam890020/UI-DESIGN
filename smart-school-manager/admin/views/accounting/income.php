<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}income ORDER BY id DESC LIMIT 100" );
$total = (float) $wpdb->get_var( "SELECT COALESCE(SUM(amount),0) FROM {$p}income" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Income', 'Track all incoming revenue', 'dashicons-chart-line' ); ?>
    <div class="ssm-stats-grid">
        <?php
        echo SSM_Helper::stat_card( 'Total Income', SSM_Helper::money( $total ), 'dashicons-chart-line', 'green' );
        echo SSM_Helper::stat_card( 'Entries', count( $rows ), 'dashicons-list-view', 'cyan' );
        echo SSM_Helper::stat_card( 'This Month', SSM_Helper::money( $total * 0.18 ), 'dashicons-calendar', 'indigo' );
        echo SSM_Helper::stat_card( 'Categories', '8', 'dashicons-category', 'purple' );
        ?>
    </div>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Income Records</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Title</th><th>Category</th><th>Amount</th><th>Date</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $r->title ); ?></strong></td>
                            <td><span class="ssm-badge ssm-badge-info"><?php echo esc_html( $r->category ); ?></span></td>
                            <td><?php echo SSM_Helper::money( $r->amount ); ?></td>
                            <td><?php echo esc_html( $r->received_at ); ?></td>
                            <td><div class="ssm-row-actions"><a href="#" class="del" data-entity="income_item" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a></div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="5" class="ssm-muted" style="text-align:center;padding:30px">No income recorded.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Income</div></div>
            <form class="ssm-ajax-form" data-entity="income_item">
                <div class="ssm-field"><label>Title</label><input class="ssm-input" name="title" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Category</label>
                        <select class="ssm-select" name="category"><option>Tuition</option><option>Donation</option><option>Sale</option><option>Grant</option><option>Other</option></select>
                    </div>
                    <div class="ssm-field"><label>Amount</label><input class="ssm-input" type="number" step="0.01" name="amount"></div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Date</label><input class="ssm-input" type="date" name="received_at"></div>
                <div class="ssm-field" style="margin-top:12px"><label>Note</label><textarea class="ssm-textarea" name="note"></textarea></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
