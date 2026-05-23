<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}student_types ORDER BY id" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Student Types', 'Categorize students by enrolment type', 'dashicons-tag' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Name</th><th>Description</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $r->name ); ?></strong></td>
                            <td><?php echo esc_html( $r->description ); ?></td>
                            <td><div class="ssm-row-actions"><a href="#" class="del" data-entity="student_type" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a></div></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Type</div>
            <form class="ssm-ajax-form" data-entity="student_type" style="margin-top:12px">
                <div class="ssm-field"><label>Name</label><input class="ssm-input" name="name" required></div>
                <div class="ssm-field" style="margin-top:12px"><label>Description</label><textarea class="ssm-textarea" name="description"></textarea></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
