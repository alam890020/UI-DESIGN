<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT e.*, c.name AS class_name FROM {$p}exams e LEFT JOIN {$p}classes c ON c.id=e.class_id ORDER BY e.id DESC LIMIT 50" );
$classes = $wpdb->get_results( "SELECT id, name FROM {$p}classes" );
$groups = $wpdb->get_results( "SELECT id, name FROM {$p}exam_groups" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Exams', 'Schedule and manage examinations', 'dashicons-welcome-write-blog' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> All Exams</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Name</th><th>Class</th><th>Start</th><th>End</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $r->name ); ?></strong></td>
                            <td><?php echo esc_html( $r->class_name ); ?></td>
                            <td><?php echo esc_html( $r->start_date ); ?></td>
                            <td><?php echo esc_html( $r->end_date ); ?></td>
                            <td><div class="ssm-row-actions">
                                <a href="#"><span class="dashicons dashicons-edit"></span></a>
                                <a href="#" class="del" data-entity="exam_item" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a>
                            </div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="5" class="ssm-muted" style="text-align:center;padding:30px">No exams yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> New Exam</div></div>
            <form class="ssm-ajax-form" data-entity="exam_item">
                <div class="ssm-field"><label>Name</label><input class="ssm-input" name="name" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Class</label>
                        <select class="ssm-select" name="class_id">
                            <option value="0">— Any —</option>
                            <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name ); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="ssm-field"><label>Group</label>
                        <select class="ssm-select" name="exam_group_id">
                            <option value="0">— None —</option>
                            <?php foreach ( $groups as $g ) : ?><option value="<?php echo (int) $g->id; ?>"><?php echo esc_html( $g->name ); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Start</label><input class="ssm-input" type="date" name="start_date"></div>
                    <div class="ssm-field"><label>End</label><input class="ssm-input" type="date" name="end_date"></div>
                </div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save Exam</button>
            </form>
        </div>
    </div>
</div>
