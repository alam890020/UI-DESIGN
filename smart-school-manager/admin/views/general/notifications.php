<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}notifications ORDER BY id DESC LIMIT 50" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Notifications', 'Send announcements via Email, SMS or in-app', 'dashicons-email-alt' ); ?>

    <div class="ssm-grid-1-2">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-megaphone"></span> Compose</div></div>
            <form>
                <div class="ssm-form-grid">
                    <div class="ssm-field"><label>Channel</label>
                        <select class="ssm-select"><option>In-App</option><option>Email</option><option>SMS</option><option>Push</option></select>
                    </div>
                    <div class="ssm-field"><label>Audience</label>
                        <select class="ssm-select"><option>All</option><option>Students</option><option>Staff</option><option>Class</option><option>Parents</option></select>
                    </div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Title</label><input class="ssm-input"></div>
                <div class="ssm-field" style="margin-top:12px"><label>Message</label><textarea class="ssm-textarea" rows="6"></textarea></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px"><span class="dashicons dashicons-email"></span> Send Notification</button>
            </form>
        </div>

        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Recent</div></div>
            <div class="ssm-list">
            <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                <div class="ssm-list-item">
                    <span class="ssm-avatar ssm-grad-blue"><span class="dashicons dashicons-email" style="color:#fff"></span></span>
                    <div style="flex:1">
                        <strong><?php echo esc_html( $r->title ); ?></strong>
                        <div class="meta"><?php echo esc_html( $r->channel ); ?> · <?php echo esc_html( $r->audience ); ?> · <?php echo esc_html( $r->sent_at ); ?></div>
                    </div>
                    <?php echo SSM_Helper::badge( $r->status ); ?>
                </div>
            <?php endforeach; else : ?>
                <p class="ssm-muted">No notifications sent yet.</p>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>
