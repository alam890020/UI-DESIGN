<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT h.*, c.name AS class_name FROM {$p}homework h LEFT JOIN {$p}classes c ON c.id=h.class_id ORDER BY h.id DESC LIMIT 50" );
$classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes" );
$subjects = $wpdb->get_results( "SELECT id, name FROM {$p}subjects" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Homework', 'Assign and track class homework', 'dashicons-clipboard' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Homework</div></div>
            <div class="ssm-list">
            <?php if ( $rows ) : foreach ( $rows as $h ) : ?>
                <div class="ssm-list-item" style="border:1px solid var(--ssm-border);background:#fff">
                    <span class="ssm-avatar ssm-grad-orange"><span class="dashicons dashicons-clipboard" style="color:#fff"></span></span>
                    <div style="flex:1">
                        <strong><?php echo esc_html( $h->title ); ?></strong>
                        <div class="meta"><?php echo esc_html( $h->class_name ); ?> · Due <?php echo esc_html( $h->due_date ); ?></div>
                    </div>
                    <div class="ssm-row-actions">
                        <a href="#"><span class="dashicons dashicons-edit"></span></a>
                        <a href="#" class="del" data-entity="homework_item" data-id="<?php echo (int) $h->id; ?>"><span class="dashicons dashicons-trash"></span></a>
                    </div>
                </div>
            <?php endforeach; else : ?>
                <p class="ssm-muted">No homework yet.</p>
            <?php endif; ?>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> New Homework</div></div>
            <form class="ssm-ajax-form" data-entity="homework_item">
                <div class="ssm-field"><label>Title</label><input class="ssm-input" name="title" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Class</label>
                        <select class="ssm-select" name="class_id" required>
                            <option value="">— Select —</option>
                            <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name ); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="ssm-field"><label>Subject</label>
                        <select class="ssm-select" name="subject_id">
                            <option value="0">— Select —</option>
                            <?php foreach ( $subjects as $sb ) : ?><option value="<?php echo (int) $sb->id; ?>"><?php echo esc_html( $sb->name ); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Assigned Date</label><input class="ssm-input" type="date" name="assigned_date"></div>
                    <div class="ssm-field"><label>Due Date</label><input class="ssm-input" type="date" name="due_date"></div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Description</label><textarea class="ssm-textarea" name="description"></textarea></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save Homework</button>
            </form>
        </div>
    </div>
</div>
