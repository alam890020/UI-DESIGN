<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT f.*, c.name AS class_name FROM {$p}fee_structures f LEFT JOIN {$p}classes c ON c.id=f.class_id ORDER BY f.id DESC" );
$classes = $wpdb->get_results( "SELECT id, name FROM {$p}classes" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Fee Structures', 'Define fee components per class', 'dashicons-money-alt' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Fee Structures</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Name</th><th>Class</th><th>Amount</th><th>Frequency</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $r->name ); ?></strong></td>
                            <td><?php echo esc_html( $r->class_name ); ?></td>
                            <td><?php echo SSM_Helper::money( $r->amount ); ?></td>
                            <td><span class="ssm-badge ssm-badge-info"><?php echo esc_html( $r->frequency ); ?></span></td>
                            <td><div class="ssm-row-actions"><a href="#" class="del" data-entity="fee_structure" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a></div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="5" class="ssm-muted" style="text-align:center;padding:30px">No fee structures yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Fee</div></div>
            <form class="ssm-ajax-form" data-entity="fee_structure">
                <div class="ssm-field"><label>Name</label><input class="ssm-input" name="name" placeholder="Tuition Fee" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Class</label>
                        <select class="ssm-select" name="class_id">
                            <option value="0">— Any —</option>
                            <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name ); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="ssm-field"><label>Amount</label><input class="ssm-input" type="number" step="0.01" name="amount"></div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Frequency</label>
                        <select class="ssm-select" name="frequency"><option>monthly</option><option>quarterly</option><option>annually</option><option>one-time</option></select>
                    </div>
                    <div class="ssm-field"><label>Due Day</label><input class="ssm-input" type="number" min="1" max="28" name="due_day" value="5"></div>
                </div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
