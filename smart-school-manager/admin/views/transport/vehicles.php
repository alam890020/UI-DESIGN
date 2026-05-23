<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT v.*, r.name AS route_name FROM {$p}vehicles v LEFT JOIN {$p}routes r ON r.id=v.route_id ORDER BY v.id DESC" );
$routes = $wpdb->get_results( "SELECT id, name FROM {$p}routes" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Vehicles', 'Bus & vehicle fleet management', 'dashicons-car' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Number</th><th>Model</th><th>Capacity</th><th>Driver</th><th>Route</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $r->number ); ?></strong></td>
                            <td><?php echo esc_html( $r->model ); ?></td>
                            <td><?php echo (int) $r->capacity; ?></td>
                            <td><?php echo esc_html( $r->driver ); ?></td>
                            <td><?php echo esc_html( $r->route_name ); ?></td>
                            <td><div class="ssm-row-actions"><a href="#" class="del" data-entity="vehicle" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a></div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="6" class="ssm-muted" style="text-align:center;padding:30px">No vehicles.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Vehicle</div>
            <form class="ssm-ajax-form" data-entity="vehicle" style="margin-top:12px">
                <div class="ssm-field"><label>Number Plate</label><input class="ssm-input" name="number" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Model</label><input class="ssm-input" name="model"></div>
                    <div class="ssm-field"><label>Capacity</label><input class="ssm-input" type="number" name="capacity"></div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Driver</label><input class="ssm-input" name="driver"></div>
                    <div class="ssm-field"><label>Driver Phone</label><input class="ssm-input" name="driver_phone"></div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Route</label>
                    <select class="ssm-select" name="route_id">
                        <option value="0">— None —</option>
                        <?php foreach ( $routes as $r ) : ?><option value="<?php echo (int) $r->id; ?>"><?php echo esc_html( $r->name ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
