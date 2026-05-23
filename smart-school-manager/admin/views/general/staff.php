<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$staff = $wpdb->get_results( "SELECT s.*, r.name AS role_name FROM {$p}staff s LEFT JOIN {$p}roles r ON r.id=s.role_id ORDER BY s.id DESC LIMIT 100" );
$roles = $wpdb->get_results( "SELECT id, name FROM {$p}roles ORDER BY name" );
$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}staff" );
$active = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}staff WHERE status='active'" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Employees / Staff', 'Manage all staff profiles, roles and assignments', 'dashicons-businessman',
        array( array( 'href' => '#add-staff', 'label' => 'Add Staff', 'icon' => 'dashicons-plus' ) ) ); ?>

    <div class="ssm-stats-grid">
        <?php
        echo SSM_Helper::stat_card( 'Total Staff', number_format( $total ), 'dashicons-businessman', 'cyan' );
        echo SSM_Helper::stat_card( 'Active', number_format( $active ), 'dashicons-yes-alt', 'green' );
        echo SSM_Helper::stat_card( 'Roles', count( $roles ), 'dashicons-shield', 'purple' );
        echo SSM_Helper::stat_card( 'Departments', '5', 'dashicons-networking', 'orange' );
        ?>
    </div>

    <div class="ssm-card">
        <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Staff Directory</div></div>
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr><th>Staff</th><th>Emp. No</th><th>Role</th><th>Designation</th><th>Phone</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php if ( $staff ) : foreach ( $staff as $s ) : $name = trim( $s->first_name . ' ' . $s->last_name ); ?>
                    <tr>
                        <td><div class="ssm-row-name"><?php echo SSM_Helper::avatar( $name, $s->photo ); ?><?php echo esc_html( $name ); ?></div></td>
                        <td><?php echo esc_html( $s->employee_no ); ?></td>
                        <td><?php echo esc_html( $s->role_name ); ?></td>
                        <td><?php echo esc_html( $s->designation ); ?></td>
                        <td><?php echo esc_html( $s->phone ); ?></td>
                        <td><?php echo SSM_Helper::badge( $s->status ); ?></td>
                        <td><div class="ssm-row-actions">
                            <a href="#"><span class="dashicons dashicons-id-alt"></span></a>
                            <a href="#"><span class="dashicons dashicons-edit"></span></a>
                            <a href="#" class="del" data-entity="staff" data-id="<?php echo (int) $s->id; ?>"><span class="dashicons dashicons-trash"></span></a>
                        </div></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="7" class="ssm-muted" style="text-align:center;padding:30px">No staff yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="add-staff" class="ssm-card">
        <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> New Staff</div></div>
        <form class="ssm-ajax-form" data-entity="staff">
            <div class="ssm-form-grid-3">
                <div class="ssm-field"><label>Employee No</label><input class="ssm-input" name="employee_no" placeholder="EMP-001"></div>
                <div class="ssm-field"><label>First Name</label><input class="ssm-input" name="first_name" required></div>
                <div class="ssm-field"><label>Last Name</label><input class="ssm-input" name="last_name"></div>
            </div>
            <div class="ssm-form-grid-3" style="margin-top:12px">
                <div class="ssm-field"><label>Email</label><input class="ssm-input" name="email" type="email"></div>
                <div class="ssm-field"><label>Phone</label><input class="ssm-input" name="phone"></div>
                <div class="ssm-field"><label>Gender</label>
                    <select class="ssm-select" name="gender"><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
                </div>
            </div>
            <div class="ssm-form-grid-3" style="margin-top:12px">
                <div class="ssm-field"><label>Date of Birth</label><input class="ssm-input" type="date" name="dob"></div>
                <div class="ssm-field"><label>Joining Date</label><input class="ssm-input" type="date" name="joining_date"></div>
                <div class="ssm-field"><label>Salary</label><input class="ssm-input" type="number" step="0.01" name="salary"></div>
            </div>
            <div class="ssm-form-grid-3" style="margin-top:12px">
                <div class="ssm-field"><label>Role</label>
                    <select class="ssm-select" name="role_id">
                        <option value="0">— Select —</option>
                        <?php foreach ( $roles as $r ) : ?><option value="<?php echo (int) $r->id; ?>"><?php echo esc_html( $r->name ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="ssm-field"><label>Designation</label><input class="ssm-input" name="designation"></div>
                <div class="ssm-field"><label>Qualification</label><input class="ssm-input" name="qualification"></div>
            </div>
            <div class="ssm-field" style="margin-top:12px"><label>Address</label><textarea class="ssm-textarea" name="address"></textarea></div>
            <input type="hidden" name="status" value="active">
            <button class="ssm-btn ssm-btn-primary" style="margin-top:16px" type="submit"><span class="dashicons dashicons-saved"></span> Save Staff</button>
        </form>
    </div>
</div>
