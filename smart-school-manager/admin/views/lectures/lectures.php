<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT l.*, c.name AS class_name, sub.name AS subject FROM {$p}lectures l LEFT JOIN {$p}classes c ON c.id=l.class_id LEFT JOIN {$p}subjects sub ON sub.id=l.subject_id ORDER BY l.scheduled_at DESC LIMIT 50" );
$classes = $wpdb->get_results( "SELECT id, name FROM {$p}classes" );
$subjects = $wpdb->get_results( "SELECT id, name FROM {$p}subjects" );
$staff = $wpdb->get_results( "SELECT id, first_name, last_name FROM {$p}staff" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Live Lectures', 'Online & virtual classes for students/staff', 'dashicons-video-alt3' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Scheduled Lectures</div></div>
            <div class="ssm-list">
            <?php if ( $rows ) : foreach ( $rows as $l ) : ?>
                <div class="ssm-list-item" style="border:1px solid var(--ssm-border)">
                    <span class="ssm-avatar ssm-grad-red"><span class="dashicons dashicons-video-alt3" style="color:#fff"></span></span>
                    <div style="flex:1">
                        <strong><?php echo esc_html( $l->title ); ?></strong>
                        <div class="meta"><?php echo esc_html( $l->class_name ); ?> · <?php echo esc_html( $l->subject ); ?> · <?php echo esc_html( $l->scheduled_at ); ?></div>
                    </div>
                    <a href="<?php echo esc_url( $l->link ); ?>" class="ssm-btn ssm-btn-primary ssm-btn-sm" target="_blank"><span class="dashicons dashicons-external"></span> Join</a>
                </div>
            <?php endforeach; else : ?><p class="ssm-muted">No lectures scheduled.</p><?php endif; ?>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Schedule Lecture</div></div>
            <form class="ssm-ajax-form" data-entity="lecture">
                <div class="ssm-field"><label>Title</label><input class="ssm-input" name="title" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Class</label>
                        <select class="ssm-select" name="class_id">
                            <option value="0">— Any —</option>
                            <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name ); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="ssm-field"><label>Subject</label>
                        <select class="ssm-select" name="subject_id">
                            <option value="0">— Any —</option>
                            <?php foreach ( $subjects as $sb ) : ?><option value="<?php echo (int) $sb->id; ?>"><?php echo esc_html( $sb->name ); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Host</label>
                        <select class="ssm-select" name="staff_id">
                            <option value="0">— Select —</option>
                            <?php foreach ( $staff as $st ) : ?><option value="<?php echo (int) $st->id; ?>"><?php echo esc_html( trim( $st->first_name . ' ' . $st->last_name ) ); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="ssm-field"><label>Audience</label>
                        <select class="ssm-select" name="audience"><option>students</option><option>staff</option><option>all</option></select>
                    </div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>When</label><input class="ssm-input" type="datetime-local" name="scheduled_at"></div>
                    <div class="ssm-field"><label>Duration (min)</label><input class="ssm-input" type="number" name="duration" value="60"></div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Meeting Link</label><input class="ssm-input" name="link" placeholder="https://meet.google.com/…"></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Schedule</button>
            </form>
        </div>
    </div>
</div>
