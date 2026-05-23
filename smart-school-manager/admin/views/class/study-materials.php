<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT m.*, c.name AS class_name FROM {$p}study_materials m LEFT JOIN {$p}classes c ON c.id=m.class_id ORDER BY m.id DESC LIMIT 50" );
$classes = $wpdb->get_results( "SELECT id, name FROM {$p}classes" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Study Materials', 'Upload and share learning resources', 'dashicons-media-document' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Materials</div></div>
            <div class="ssm-list">
            <?php if ( $rows ) : foreach ( $rows as $m ) : ?>
                <div class="ssm-list-item" style="border:1px solid var(--ssm-border)">
                    <span class="ssm-avatar ssm-grad-cyan"><span class="dashicons dashicons-media-document" style="color:#fff"></span></span>
                    <div style="flex:1">
                        <strong><?php echo esc_html( $m->title ); ?></strong>
                        <div class="meta"><?php echo esc_html( $m->class_name ); ?> · uploaded <?php echo esc_html( $m->uploaded_at ); ?></div>
                    </div>
                    <a href="<?php echo esc_url( $m->file ); ?>" class="ssm-btn ssm-btn-ghost ssm-btn-sm"><span class="dashicons dashicons-download"></span></a>
                </div>
            <?php endforeach; else : ?><p class="ssm-muted">No materials yet.</p><?php endif; ?>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-upload"></span> Upload Material</div></div>
            <form class="ssm-ajax-form" data-entity="study_material">
                <div class="ssm-field"><label>Title</label><input class="ssm-input" name="title" required></div>
                <div class="ssm-field" style="margin-top:12px"><label>Class</label>
                    <select class="ssm-select" name="class_id" required>
                        <option value="">— Select —</option>
                        <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>File URL</label><input class="ssm-input" name="file" placeholder="https://…/file.pdf"></div>
                <div class="ssm-field" style="margin-top:12px"><label>Description</label><textarea class="ssm-textarea" name="description"></textarea></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
