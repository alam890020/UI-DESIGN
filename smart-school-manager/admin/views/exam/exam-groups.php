<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}exam_groups ORDER BY id" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Exam Groups', 'Group exams (Term, Mid-term, Annual…)', 'dashicons-portfolio' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Group</th><th>Type</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $r->name ); ?></strong></td>
                            <td><?php echo esc_html( $r->type ); ?></td>
                            <td><div class="ssm-row-actions"><a href="#" class="del" data-entity="exam_group" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a></div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="3" class="ssm-muted" style="text-align:center;padding:24px">No groups.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Group</div>
            <form class="ssm-ajax-form" data-entity="exam_group" style="margin-top:12px">
                <div class="ssm-field"><label>Name</label><input class="ssm-input" name="name" required></div>
                <div class="ssm-field" style="margin-top:12px"><label>Type</label>
                    <select class="ssm-select" name="type"><option>Term</option><option>Mid-term</option><option>Annual</option><option>Unit Test</option></select>
                </div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
