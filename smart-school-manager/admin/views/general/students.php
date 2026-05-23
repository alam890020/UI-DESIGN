<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes ORDER BY name" );
$students = $wpdb->get_results( "SELECT s.*, c.name AS class_name FROM {$p}students s LEFT JOIN {$p}classes c ON c.id=s.class_id ORDER BY s.id DESC LIMIT 100" );
$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students" );
$active = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE status='active'" );
$male = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE gender='male'" );
$female = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE gender='female'" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Students', 'Enrollment, profiles and student management', 'dashicons-groups',
        array( array( 'href' => '#add-student', 'label' => 'Add Student', 'icon' => 'dashicons-plus', 'class' => 'ssm-btn-primary' ) ) ); ?>

    <div class="ssm-stats-grid">
        <?php
        echo SSM_Helper::stat_card( 'Total', number_format( $total ), 'dashicons-groups', 'indigo' );
        echo SSM_Helper::stat_card( 'Active', number_format( $active ), 'dashicons-yes-alt', 'green' );
        echo SSM_Helper::stat_card( 'Male', number_format( $male ), 'dashicons-businessman', 'blue' );
        echo SSM_Helper::stat_card( 'Female', number_format( $female ), 'dashicons-admin-users', 'pink' );
        ?>
    </div>

    <div class="ssm-card">
        <div class="ssm-card-header">
            <div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> All Students</div>
            <div class="ssm-toolbar-filters">
                <select class="ssm-select" style="min-width:140px">
                    <option>All Classes</option>
                    <?php foreach ( $classes as $c ) : ?>
                        <option><?php echo esc_html( $c->name . ' - ' . $c->section ); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" class="ssm-input" placeholder="Search students…">
                <a href="#add-student" class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-plus"></span> New</a>
            </div>
        </div>
        <div class="ssm-table-wrap">
            <table class="ssm-table">
                <thead><tr>
                    <th>Student</th><th>Adm. No</th><th>Class</th><th>Phone</th><th>Gender</th><th>Status</th><th></th>
                </tr></thead>
                <tbody>
                <?php if ( $students ) : foreach ( $students as $s ) : $name = trim( $s->first_name . ' ' . $s->last_name ); ?>
                    <tr>
                        <td><div class="ssm-row-name"><?php echo SSM_Helper::avatar( $name, $s->photo ); ?> <?php echo esc_html( $name ); ?></div></td>
                        <td><?php echo esc_html( $s->admission_no ); ?></td>
                        <td><?php echo esc_html( $s->class_name ); ?></td>
                        <td><?php echo esc_html( $s->phone ); ?></td>
                        <td><?php echo esc_html( ucfirst( $s->gender ) ); ?></td>
                        <td><?php echo SSM_Helper::badge( $s->status ); ?></td>
                        <td><div class="ssm-row-actions">
                            <a href="#"><span class="dashicons dashicons-id-alt"></span></a>
                            <a href="#"><span class="dashicons dashicons-edit"></span></a>
                            <a href="#" class="del" data-entity="student" data-id="<?php echo (int) $s->id; ?>"><span class="dashicons dashicons-trash"></span></a>
                        </div></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="7" class="ssm-muted" style="text-align:center;padding:30px">No students yet — add one below.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="add-student" class="ssm-card">
        <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> New Student</div></div>
        <form class="ssm-ajax-form" data-entity="student">
            <div class="ssm-form-grid-3">
                <div class="ssm-field"><label>Admission No</label><input class="ssm-input" name="admission_no" placeholder="ADM-001"></div>
                <div class="ssm-field"><label>Roll No</label><input class="ssm-input" name="roll_no"></div>
                <div class="ssm-field"><label>Admission Date</label><input class="ssm-input" type="date" name="admission_date"></div>
            </div>
            <div class="ssm-form-grid-3" style="margin-top:12px">
                <div class="ssm-field"><label>First Name</label><input class="ssm-input" name="first_name" required></div>
                <div class="ssm-field"><label>Last Name</label><input class="ssm-input" name="last_name"></div>
                <div class="ssm-field"><label>Gender</label>
                    <select class="ssm-select" name="gender"><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
                </div>
            </div>
            <div class="ssm-form-grid-3" style="margin-top:12px">
                <div class="ssm-field"><label>Date of Birth</label><input class="ssm-input" type="date" name="dob"></div>
                <div class="ssm-field"><label>Email</label><input class="ssm-input" type="email" name="email"></div>
                <div class="ssm-field"><label>Phone</label><input class="ssm-input" name="phone"></div>
            </div>
            <div class="ssm-form-grid-3" style="margin-top:12px">
                <div class="ssm-field"><label>Class</label>
                    <select class="ssm-select" name="class_id">
                        <option value="0">— Select —</option>
                        <?php foreach ( $classes as $c ) : ?>
                            <option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="ssm-field"><label>Section</label><input class="ssm-input" name="section"></div>
                <div class="ssm-field"><label>Blood Group</label>
                    <select class="ssm-select" name="blood_group">
                        <?php foreach ( array('','A+','A-','B+','B-','O+','O-','AB+','AB-') as $bg ) : ?>
                            <option><?php echo esc_html( $bg ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="ssm-form-grid" style="margin-top:12px">
                <div class="ssm-field"><label>Father Name</label><input class="ssm-input" name="father_name"></div>
                <div class="ssm-field"><label>Mother Name</label><input class="ssm-input" name="mother_name"></div>
            </div>
            <div class="ssm-form-grid" style="margin-top:12px">
                <div class="ssm-field"><label>Guardian Phone</label><input class="ssm-input" name="guardian_phone"></div>
                <div class="ssm-field"><label>Photo URL</label><input class="ssm-input" name="photo"></div>
            </div>
            <div class="ssm-field" style="margin-top:12px"><label>Address</label><textarea class="ssm-textarea" name="address"></textarea></div>
            <input type="hidden" name="status" value="active">
            <div style="margin-top:16px;display:flex;gap:8px">
                <button class="ssm-btn ssm-btn-primary" type="submit"><span class="dashicons dashicons-saved"></span> Save Student</button>
                <button class="ssm-btn ssm-btn-ghost" type="reset">Reset</button>
            </div>
        </form>
    </div>
</div>
