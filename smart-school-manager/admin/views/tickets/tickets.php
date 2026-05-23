<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}tickets ORDER BY id DESC LIMIT 100" );
$open = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}tickets WHERE status='open'" );
$closed = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}tickets WHERE status='closed'" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Support Tickets', 'Internal helpdesk', 'dashicons-tickets-alt' ); ?>
    <div class="ssm-stats-grid">
        <?php
        echo SSM_Helper::stat_card( 'Total', count( $rows ), 'dashicons-tickets-alt', 'indigo' );
        echo SSM_Helper::stat_card( 'Open', $open, 'dashicons-warning', 'orange' );
        echo SSM_Helper::stat_card( 'Closed', $closed, 'dashicons-yes-alt', 'green' );
        echo SSM_Helper::stat_card( 'Avg Resolve', '2.1d', 'dashicons-clock', 'cyan' );
        ?>
    </div>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> All Tickets</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>#</th><th>Subject</th><th>By</th><th>Priority</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $t ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $t->ticket_no ); ?></strong></td>
                            <td><?php echo esc_html( $t->subject ); ?></td>
                            <td><?php echo esc_html( $t->opened_by ); ?></td>
                            <td><span class="ssm-badge ssm-badge-warning"><?php echo esc_html( $t->priority ); ?></span></td>
                            <td><?php echo SSM_Helper::badge( $t->status ); ?></td>
                            <td><div class="ssm-row-actions"><a href="#" class="del" data-entity="ticket" data-id="<?php echo (int) $t->id; ?>"><span class="dashicons dashicons-trash"></span></a></div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="6" class="ssm-muted" style="text-align:center;padding:30px">No tickets yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> New Ticket</div>
            <form class="ssm-ajax-form" data-entity="ticket" style="margin-top:12px">
                <div class="ssm-field"><label>Ticket No</label><input class="ssm-input" name="ticket_no" placeholder="TCK-001" required></div>
                <div class="ssm-field" style="margin-top:12px"><label>Subject</label><input class="ssm-input" name="subject" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Opened By</label><input class="ssm-input" name="opened_by"></div>
                    <div class="ssm-field"><label>Assign To</label><input class="ssm-input" name="assigned_to"></div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Priority</label>
                        <select class="ssm-select" name="priority"><option>low</option><option>medium</option><option>high</option><option>urgent</option></select>
                    </div>
                    <div class="ssm-field"><label>Status</label>
                        <select class="ssm-select" name="status"><option>open</option><option>in-progress</option><option>closed</option></select>
                    </div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Description</label><textarea class="ssm-textarea" name="description"></textarea></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Create</button>
            </form>
        </div>
    </div>
</div>
