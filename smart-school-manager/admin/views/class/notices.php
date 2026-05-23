<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}notices ORDER BY id DESC LIMIT 50" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Notices', 'Class & school-wide announcements', 'dashicons-megaphone' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> All Notices</div></div>
            <div class="ssm-list">
            <?php if ( $rows ) : foreach ( $rows as $n ) : ?>
                <div class="ssm-list-item" style="border:1px solid var(--ssm-border)">
                    <span class="ssm-avatar ssm-grad-purple"><span class="dashicons dashicons-megaphone" style="color:#fff"></span></span>
                    <div style="flex:1">
                        <strong><?php echo esc_html( $n->title ); ?></strong>
                        <div class="meta"><?php echo esc_html( $n->audience ); ?> · <?php echo esc_html( $n->posted_at ); ?></div>
                    </div>
                    <div class="ssm-row-actions"><a href="#" class="del" data-entity="notice" data-id="<?php echo (int) $n->id; ?>"><span class="dashicons dashicons-trash"></span></a></div>
                </div>
            <?php endforeach; else : ?><p class="ssm-muted">No notices yet.</p><?php endif; ?>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Post Notice</div></div>
            <form class="ssm-ajax-form" data-entity="notice">
                <div class="ssm-field"><label>Title</label><input class="ssm-input" name="title" required></div>
                <div class="ssm-field" style="margin-top:12px"><label>Audience</label>
                    <select class="ssm-select" name="audience"><option>all</option><option>students</option><option>staff</option><option>parents</option></select>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Body</label><textarea class="ssm-textarea" name="body" rows="5"></textarea></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Publish</button>
            </form>
        </div>
    </div>
</div>
