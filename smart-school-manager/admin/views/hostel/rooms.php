<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT r.*, h.name AS hostel_name FROM {$p}hostel_rooms r LEFT JOIN {$p}hostels h ON h.id=r.hostel_id ORDER BY r.id DESC" );
$hostels = $wpdb->get_results( "SELECT id, name FROM {$p}hostels" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Hostel Rooms', 'Room allocation and occupancy', 'dashicons-admin-home' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Rooms</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Room</th><th>Hostel</th><th>Type</th><th>Occupancy</th><th>Fee</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $r->room_no ); ?></strong></td>
                            <td><?php echo esc_html( $r->hostel_name ); ?></td>
                            <td><?php echo esc_html( $r->type ); ?></td>
                            <td><?php echo (int) $r->occupied; ?> / <?php echo (int) $r->capacity; ?></td>
                            <td><?php echo SSM_Helper::money( $r->fee ); ?></td>
                            <td><div class="ssm-row-actions"><a href="#" class="del" data-entity="room" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a></div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="6" class="ssm-muted" style="text-align:center;padding:30px">No rooms.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Room</div></div>
            <form class="ssm-ajax-form" data-entity="room">
                <div class="ssm-form-grid">
                    <div class="ssm-field"><label>Hostel</label>
                        <select class="ssm-select" name="hostel_id" required>
                            <option value="">— Select —</option>
                            <?php foreach ( $hostels as $h ) : ?><option value="<?php echo (int) $h->id; ?>"><?php echo esc_html( $h->name ); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="ssm-field"><label>Room No</label><input class="ssm-input" name="room_no"></div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Type</label>
                        <select class="ssm-select" name="type"><option>Single</option><option>Double</option><option>Triple</option><option>Dormitory</option></select>
                    </div>
                    <div class="ssm-field"><label>Capacity</label><input class="ssm-input" type="number" name="capacity" value="1"></div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Monthly Fee</label><input class="ssm-input" type="number" step="0.01" name="fee"></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
