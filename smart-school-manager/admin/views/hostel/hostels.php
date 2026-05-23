<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}hostels ORDER BY id DESC" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Hostels', 'Hostel setup & management', 'dashicons-building' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Hostels</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Name</th><th>Type</th><th>Warden</th><th>Capacity</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $r->name ); ?></strong></td>
                            <td><span class="ssm-badge ssm-badge-info"><?php echo esc_html( $r->type ); ?></span></td>
                            <td><?php echo esc_html( $r->warden ); ?></td>
                            <td><?php echo (int) $r->capacity; ?></td>
                            <td><div class="ssm-row-actions"><a href="#" class="del" data-entity="hostel" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a></div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="5" class="ssm-muted" style="text-align:center;padding:30px">No hostels yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Hostel</div></div>
            <form class="ssm-ajax-form" data-entity="hostel">
                <div class="ssm-field"><label>Name</label><input class="ssm-input" name="name" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Type</label>
                        <select class="ssm-select" name="type"><option>boys</option><option>girls</option><option>mixed</option></select>
                    </div>
                    <div class="ssm-field"><label>Capacity</label><input class="ssm-input" type="number" name="capacity"></div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Warden</label><input class="ssm-input" name="warden"></div>
                    <div class="ssm-field"><label>Phone</label><input class="ssm-input" name="phone"></div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Address</label><textarea class="ssm-textarea" name="address"></textarea></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
