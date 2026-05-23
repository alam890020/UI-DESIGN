<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'sms_';
$sessions = $wpdb->get_results( "SELECT * FROM {$p}sessions ORDER BY id DESC" );
?>
<div class="sms-page">
    <?php SMS_Helper::page_header( 'Academic Sessions', 'Manage academic year sessions', 'calendar' ); ?>
    <div class="sms-grid-2-1">
        <div class="sms-card">
            <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('list',18); ?> All Sessions</div></div>
            <div class="sms-table-wrap">
                <table class="sms-table">
                    <thead><tr><th>Session</th><th>Start</th><th>End</th><th>Current</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $sessions ) : foreach ( $sessions as $s ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $s->name ); ?></strong></td>
                            <td><?php echo esc_html( $s->start_date ); ?></td>
                            <td><?php echo esc_html( $s->end_date ); ?></td>
                            <td><?php echo $s->is_current ? '<span class="sms-pill sms-pill-success">Current</span>' : '—'; ?></td>
                            <td><div class="sms-row-actions">
                                <a href="#"><?php echo SMS_Icons::svg('edit',14); ?></a>
                                <a href="#" class="del" data-entity="session_item" data-id="<?php echo (int) $s->id; ?>"><?php echo SMS_Icons::svg('trash',14); ?></a>
                            </div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="5" class="sms-muted" style="text-align:center;padding:30px">No sessions.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="sms-card">
            <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('plus',18); ?> Create Session</div></div>
            <form class="sms-ajax-form" data-entity="session_item">
                <div class="sms-field"><label>Name</label><input class="sms-input" name="name" placeholder="2025-2026" required></div>
                <div class="sms-grid-2" style="margin-top:12px">
                    <div class="sms-field"><label>Start</label><input class="sms-input" type="date" name="start_date"></div>
                    <div class="sms-field"><label>End</label><input class="sms-input" type="date" name="end_date"></div>
                </div>
                <div class="sms-grid-2" style="margin-top:12px">
                    <div class="sms-field"><label>Mark Current</label>
                        <select class="sms-select" name="is_current"><option value="0">No</option><option value="1">Yes</option></select>
                    </div>
                    <div class="sms-field"><label>Status</label>
                        <select class="sms-select" name="status"><option value="1">Active</option><option value="0">Inactive</option></select>
                    </div>
                </div>
                <button class="sms-btn sms-btn-primary" style="margin-top:16px" type="submit"><?php echo SMS_Icons::svg('check',14); ?><span>Save</span></button>
            </form>
        </div>
    </div>
</div>
