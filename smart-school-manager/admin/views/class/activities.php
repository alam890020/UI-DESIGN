<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT a.*, c.name AS class_name FROM {$p}activities a LEFT JOIN {$p}classes c ON c.id=a.class_id ORDER BY a.id DESC LIMIT 50" );
$classes = $wpdb->get_results( "SELECT id, name FROM {$p}classes" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Activities', 'Co-curricular and class activities', 'dashicons-superhero-alt' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Activities</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Activity</th><th>Class</th><th>Date</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $a ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $a->title ); ?></strong></td>
                            <td><?php echo esc_html( $a->class_name ); ?></td>
                            <td><?php echo esc_html( $a->activity_date ); ?></td>
                            <td><div class="ssm-row-actions">
                                <a href="#" class="del" data-entity="activity" data-id="<?php echo (int) $a->id; ?>"><span class="dashicons dashicons-trash"></span></a>
                            </div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="4" class="ssm-muted" style="text-align:center;padding:30px">No activities yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> New Activity</div></div>
            <form class="ssm-ajax-form" data-entity="activity">
                <div class="ssm-field"><label>Title</label><input class="ssm-input" name="title" required></div>
                <div class="ssm-field" style="margin-top:12px"><label>Class</label>
                    <select class="ssm-select" name="class_id">
                        <option value="0">— Any —</option>
                        <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Date</label><input class="ssm-input" type="date" name="activity_date"></div>
                <div class="ssm-field" style="margin-top:12px"><label>Description</label><textarea class="ssm-textarea" name="description"></textarea></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
