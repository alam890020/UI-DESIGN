<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}meetings ORDER BY meeting_date DESC LIMIT 50" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Meetings', 'Parent-Teacher and other meetings', 'dashicons-groups' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Meetings</div></div>
            <div class="ssm-list">
            <?php if ( $rows ) : foreach ( $rows as $m ) : ?>
                <div class="ssm-list-item" style="border:1px solid var(--ssm-border)">
                    <span class="ssm-avatar ssm-grad-blue"><span class="dashicons dashicons-groups" style="color:#fff"></span></span>
                    <div style="flex:1">
                        <strong><?php echo esc_html( $m->title ); ?></strong>
                        <div class="meta">With <?php echo esc_html( $m->with_whom ); ?> · <?php echo esc_html( $m->meeting_date ); ?></div>
                    </div>
                    <div class="ssm-row-actions"><a href="#" class="del" data-entity="meeting" data-id="<?php echo (int) $m->id; ?>"><span class="dashicons dashicons-trash"></span></a></div>
                </div>
            <?php endforeach; else : ?><p class="ssm-muted">No meetings scheduled.</p><?php endif; ?>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Schedule Meeting</div></div>
            <form class="ssm-ajax-form" data-entity="meeting">
                <div class="ssm-field"><label>Title</label><input class="ssm-input" name="title" required></div>
                <div class="ssm-field" style="margin-top:12px"><label>With</label><input class="ssm-input" name="with_whom"></div>
                <div class="ssm-field" style="margin-top:12px"><label>When</label><input class="ssm-input" type="datetime-local" name="meeting_date"></div>
                <div class="ssm-field" style="margin-top:12px"><label>Agenda</label><textarea class="ssm-textarea" name="agenda"></textarea></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save Meeting</button>
            </form>
        </div>
    </div>
</div>
