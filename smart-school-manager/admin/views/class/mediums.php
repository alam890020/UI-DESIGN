<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}mediums ORDER BY id" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Medium of Instruction', 'Languages used for teaching', 'dashicons-translation' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Name</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $r->name ); ?></strong></td>
                            <td><div class="ssm-row-actions"><a href="#" class="del" data-entity="medium" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a></div></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Medium</div>
            <form class="ssm-ajax-form" data-entity="medium" style="margin-top:12px">
                <div class="ssm-field"><label>Name</label><input class="ssm-input" name="name" required></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
