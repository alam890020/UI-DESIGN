<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}events ORDER BY start_date DESC LIMIT 50" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Events', 'School events and calendar', 'dashicons-calendar-alt' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-calendar"></span> Upcoming & Past</div></div>
            <div class="ssm-list">
            <?php if ( $rows ) : foreach ( $rows as $e ) : ?>
                <div class="ssm-list-item" style="border:1px solid var(--ssm-border)">
                    <span class="ssm-avatar ssm-grad-pink"><span class="dashicons dashicons-calendar-alt" style="color:#fff"></span></span>
                    <div style="flex:1">
                        <strong><?php echo esc_html( $e->title ); ?></strong>
                        <div class="meta"><?php echo esc_html( $e->location ); ?> · <?php echo esc_html( $e->start_date ); ?></div>
                    </div>
                    <div class="ssm-row-actions"><a href="#" class="del" data-entity="event" data-id="<?php echo (int) $e->id; ?>"><span class="dashicons dashicons-trash"></span></a></div>
                </div>
            <?php endforeach; else : ?><p class="ssm-muted">No events yet.</p><?php endif; ?>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> New Event</div></div>
            <form class="ssm-ajax-form" data-entity="event">
                <div class="ssm-field"><label>Title</label><input class="ssm-input" name="title" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Start</label><input class="ssm-input" type="datetime-local" name="start_date"></div>
                    <div class="ssm-field"><label>End</label><input class="ssm-input" type="datetime-local" name="end_date"></div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Location</label><input class="ssm-input" name="location"></div>
                <div class="ssm-field" style="margin-top:12px"><label>Description</label><textarea class="ssm-textarea" name="description"></textarea></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save Event</button>
            </form>
        </div>
    </div>
</div>
