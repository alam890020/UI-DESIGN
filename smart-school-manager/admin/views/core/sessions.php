<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$sessions = $wpdb->get_results( "SELECT * FROM {$p}sessions ORDER BY id DESC" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Academic Sessions', 'Configure your academic year sessions', 'dashicons-calendar' ); ?>

    <div class="ssm-grid-2">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> All Sessions</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Session</th><th>Start</th><th>End</th><th>Current</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $sessions ) : foreach ( $sessions as $s ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $s->name ); ?></strong></td>
                            <td><?php echo esc_html( $s->start_date ); ?></td>
                            <td><?php echo esc_html( $s->end_date ); ?></td>
                            <td><?php echo $s->is_current ? '<span class="ssm-badge ssm-badge-success">Current</span>' : '—'; ?></td>
                            <td><?php echo SSM_Helper::badge( $s->status ? 'Active' : 'Inactive' ); ?></td>
                            <td><div class="ssm-row-actions">
                                <a href="#"><span class="dashicons dashicons-edit"></span></a>
                                <a href="#" class="del" data-entity="session_item" data-id="<?php echo (int) $s->id; ?>"><span class="dashicons dashicons-trash"></span></a>
                            </div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="6" class="ssm-muted" style="text-align:center;padding:30px">No sessions yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Create Session</div></div>
            <form class="ssm-ajax-form" data-entity="session_item">
                <div class="ssm-field"><label>Session Name</label><input class="ssm-input" name="name" placeholder="2025-2026" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Start Date</label><input class="ssm-input" type="date" name="start_date"></div>
                    <div class="ssm-field"><label>End Date</label><input class="ssm-input" type="date" name="end_date"></div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Mark Current</label>
                        <select class="ssm-select" name="is_current"><option value="0">No</option><option value="1">Yes</option></select>
                    </div>
                    <div class="ssm-field"><label>Status</label>
                        <select class="ssm-select" name="status"><option value="1">Active</option><option value="0">Inactive</option></select>
                    </div>
                </div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:16px" type="submit"><span class="dashicons dashicons-saved"></span> Save Session</button>
            </form>
        </div>
    </div>
</div>
