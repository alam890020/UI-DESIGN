<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}roles ORDER BY id" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Roles & Permissions', 'Define staff roles and access levels', 'dashicons-shield' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Roles</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Role</th><th>Slug</th><th>Permissions</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $r->name ); ?></strong></td>
                            <td><code><?php echo esc_html( $r->slug ); ?></code></td>
                            <td><span class="ssm-muted">All modules</span></td>
                            <td><div class="ssm-row-actions">
                                <a href="#"><span class="dashicons dashicons-edit"></span></a>
                                <a href="#" class="del" data-entity="role" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a>
                            </div></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> New Role</div></div>
            <form class="ssm-ajax-form" data-entity="role">
                <div class="ssm-field"><label>Role Name</label><input class="ssm-input" name="name" required></div>
                <div class="ssm-field" style="margin-top:12px"><label>Slug</label><input class="ssm-input" name="slug" placeholder="teacher"></div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save Role</button>
            </form>
        </div>
    </div>
</div>
