<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT a.*, s.name AS school_name FROM {$p}admins a LEFT JOIN {$p}schools s ON s.id=a.school_id ORDER BY a.id DESC" );
$schools = $wpdb->get_results( "SELECT id, name FROM {$p}schools ORDER BY name" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'School Admins', 'Manage admins and access scopes', 'dashicons-admin-users' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Admins</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Admin</th><th>Email</th><th>School</th><th>Role</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><div class="ssm-row-name"><?php echo SSM_Helper::avatar( $r->name ); ?><?php echo esc_html( $r->name ); ?></div></td>
                            <td><?php echo esc_html( $r->email ); ?></td>
                            <td><?php echo esc_html( $r->school_name ); ?></td>
                            <td><?php echo esc_html( $r->role ); ?></td>
                            <td><?php echo SSM_Helper::badge( $r->status ? 'Active' : 'Inactive' ); ?></td>
                            <td><div class="ssm-row-actions">
                                <a href="#"><span class="dashicons dashicons-edit"></span></a>
                                <a href="#" class="del" data-entity="admin" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a>
                            </div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="6" class="ssm-muted" style="text-align:center;padding:30px">No admins yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Admin</div></div>
            <form class="ssm-ajax-form" data-entity="admin">
                <div class="ssm-field"><label>Name</label><input class="ssm-input" name="name" required></div>
                <div class="ssm-field" style="margin-top:12px"><label>Email</label><input class="ssm-input" name="email" type="email"></div>
                <div class="ssm-field" style="margin-top:12px"><label>School</label>
                    <select class="ssm-select" name="school_id"><option value="0">— Select —</option>
                        <?php foreach ( $schools as $s ) : ?><option value="<?php echo (int) $s->id; ?>"><?php echo esc_html( $s->name ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Role</label>
                    <select class="ssm-select" name="role"><option>admin</option><option>super-admin</option><option>manager</option></select>
                </div>
                <input type="hidden" name="status" value="1">
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
