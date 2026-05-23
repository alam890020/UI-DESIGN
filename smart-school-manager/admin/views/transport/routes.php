<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}routes ORDER BY id DESC" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Transport Routes', 'Bus routes & stops', 'dashicons-location-alt' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Route</th><th>From</th><th>To</th><th>Distance</th><th>Fee</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $r->name ); ?></strong></td>
                            <td><?php echo esc_html( $r->start_point ); ?></td>
                            <td><?php echo esc_html( $r->end_point ); ?></td>
                            <td><?php echo esc_html( $r->distance ); ?></td>
                            <td><?php echo SSM_Helper::money( $r->fee ); ?></td>
                            <td><div class="ssm-row-actions"><a href="#" class="del" data-entity="route" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a></div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="6" class="ssm-muted" style="text-align:center;padding:30px">No routes.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Route</div>
            <form class="ssm-ajax-form" data-entity="route" style="margin-top:12px">
                <div class="ssm-field"><label>Name</label><input class="ssm-input" name="name" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Start</label><input class="ssm-input" name="start_point"></div>
                    <div class="ssm-field"><label>End</label><input class="ssm-input" name="end_point"></div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Distance</label><input class="ssm-input" name="distance"></div>
                    <div class="ssm-field"><label>Fee</label><input class="ssm-input" type="number" step="0.01" name="fee"></div>
                </div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
