<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}logs ORDER BY id DESC LIMIT 200" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Activity Logs', 'Complete audit trail of admin actions', 'dashicons-list-view' ); ?>
    <div class="ssm-card">
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>When</th><th>User</th><th>Module</th><th>Action</th><th>IP</th></tr></thead>
                <tbody>
                <?php if ( $rows ) : foreach ( $rows as $r ) : $u = get_userdata( $r->user_id ); ?>
                    <tr>
                        <td><?php echo esc_html( $r->created_at ); ?></td>
                        <td><?php echo esc_html( $u ? $u->display_name : 'System' ); ?></td>
                        <td><span class="ssm-badge ssm-badge-info"><?php echo esc_html( $r->module ); ?></span></td>
                        <td><?php echo esc_html( $r->action ); ?></td>
                        <td><code><?php echo esc_html( $r->ip ); ?></code></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="5" class="ssm-muted" style="text-align:center;padding:30px">No log entries yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
