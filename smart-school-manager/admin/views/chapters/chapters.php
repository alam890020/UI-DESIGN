<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT ch.*, sub.name AS subject FROM {$p}chapters ch LEFT JOIN {$p}subjects sub ON sub.id=ch.subject_id ORDER BY ch.subject_id, ch.sort_order LIMIT 100" );
$subjects = $wpdb->get_results( "SELECT id, name FROM {$p}subjects" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Chapters', 'Organize subject chapters and modules', 'dashicons-book' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Chapters</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>#</th><th>Chapter</th><th>Subject</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><?php echo (int) $r->sort_order; ?></td>
                            <td><strong><?php echo esc_html( $r->title ); ?></strong></td>
                            <td><span class="ssm-badge ssm-badge-info"><?php echo esc_html( $r->subject ); ?></span></td>
                            <td><div class="ssm-row-actions"><a href="#" class="del" data-entity="chapter" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a></div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="4" class="ssm-muted" style="text-align:center;padding:30px">No chapters yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Chapter</div>
            <form class="ssm-ajax-form" data-entity="chapter" style="margin-top:12px">
                <div class="ssm-field"><label>Subject</label>
                    <select class="ssm-select" name="subject_id" required>
                        <option value="">— Select —</option>
                        <?php foreach ( $subjects as $sb ) : ?><option value="<?php echo (int) $sb->id; ?>"><?php echo esc_html( $sb->name ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Title</label><input class="ssm-input" name="title" required></div>
                <div class="ssm-field" style="margin-top:12px"><label>Order</label><input class="ssm-input" type="number" name="sort_order" value="0"></div>
                <div class="ssm-field" style="margin-top:12px"><label>Description</label><textarea class="ssm-textarea" name="description"></textarea></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
