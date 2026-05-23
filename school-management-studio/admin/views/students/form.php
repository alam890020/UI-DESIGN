<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'sms_';
$classes  = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes ORDER BY name" );
$mediums  = $wpdb->get_results( "SELECT id, name FROM {$p}mediums ORDER BY name" );
$types    = $wpdb->get_results( "SELECT id, name FROM {$p}student_types ORDER BY name" );
$sessions = $wpdb->get_results( "SELECT id, name, is_current FROM {$p}sessions ORDER BY id DESC" );
?>
<div class="sms-page">
    <?php SMS_Helper::page_header( 'New Student Wizard', 'Multi-step enrollment form', 'user-plus',
        array( array( 'href' => SMS_Helper::admin_url('sms-students'), 'label' => 'View Students', 'icon' => 'users', 'class' => 'sms-btn-ghost' ) ) ); ?>

    <div class="sms-wiz-steps">
        <div class="sms-wiz-step active" data-step="1"><span class="num">1</span><div><strong>Personal</strong><div class="sms-muted" style="font-size:11px">Identity</div></div></div>
        <div class="sms-wiz-step" data-step="2"><span class="num">2</span><div><strong>Academic</strong><div class="sms-muted" style="font-size:11px">Class & session</div></div></div>
        <div class="sms-wiz-step" data-step="3"><span class="num">3</span><div><strong>Family</strong><div class="sms-muted" style="font-size:11px">Parents</div></div></div>
        <div class="sms-wiz-step" data-step="4"><span class="num">4</span><div><strong>Contact</strong><div class="sms-muted" style="font-size:11px">Address</div></div></div>
        <div class="sms-wiz-step" data-step="5"><span class="num">5</span><div><strong>Review</strong><div class="sms-muted" style="font-size:11px">Submit</div></div></div>
    </div>

    <form class="sms-card sms-ajax-form" data-entity="student">
        <div class="sms-wiz-page" data-step="1">
            <h3 style="margin-top:0">Personal Details</h3>
            <div class="sms-grid-3">
                <div class="sms-field"><label>Admission No</label><input class="sms-input" name="admission_no" placeholder="ADM-001"></div>
                <div class="sms-field"><label>Roll No</label><input class="sms-input" name="roll_no"></div>
                <div class="sms-field"><label>Admission Date</label><input class="sms-input" type="date" name="admission_date" value="<?php echo esc_attr( date('Y-m-d') ); ?>"></div>
            </div>
            <div class="sms-grid-3" style="margin-top:14px">
                <div class="sms-field"><label>First Name *</label><input class="sms-input" name="first_name" required></div>
                <div class="sms-field"><label>Last Name</label><input class="sms-input" name="last_name"></div>
                <div class="sms-field"><label>Gender</label>
                    <select class="sms-select" name="gender"><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
                </div>
            </div>
            <div class="sms-grid-3" style="margin-top:14px">
                <div class="sms-field"><label>Date of Birth</label><input class="sms-input" type="date" name="dob"></div>
                <div class="sms-field"><label>Blood Group</label>
                    <select class="sms-select" name="blood_group"><?php foreach ( array('','A+','A-','B+','B-','O+','O-','AB+','AB-') as $bg ) : ?><option><?php echo esc_html( $bg ); ?></option><?php endforeach; ?></select>
                </div>
                <div class="sms-field"><label>Photo URL</label><input class="sms-input" name="photo"></div>
            </div>
        </div>

        <div class="sms-wiz-page" data-step="2" style="display:none">
            <h3 style="margin-top:0">Academic</h3>
            <div class="sms-grid-3">
                <div class="sms-field"><label>Class *</label>
                    <select class="sms-select" name="class_id" required>
                        <option value="">— Select —</option>
                        <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="sms-field"><label>Section</label><input class="sms-input" name="section"></div>
                <div class="sms-field"><label>Session</label>
                    <select class="sms-select" name="session_id">
                        <option value="0">— Select —</option>
                        <?php foreach ( $sessions as $s ) : ?><option value="<?php echo (int) $s->id; ?>" <?php selected( $s->is_current, 1 ); ?>><?php echo esc_html( $s->name ); ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="sms-grid-2" style="margin-top:14px">
                <div class="sms-field"><label>Student Type</label>
                    <select class="sms-select" name="student_type_id">
                        <option value="0">— Select —</option>
                        <?php foreach ( $types as $t ) : ?><option value="<?php echo (int) $t->id; ?>"><?php echo esc_html( $t->name ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="sms-field"><label>Medium</label>
                    <select class="sms-select" name="medium_id">
                        <option value="0">— Select —</option>
                        <?php foreach ( $mediums as $m ) : ?><option value="<?php echo (int) $m->id; ?>"><?php echo esc_html( $m->name ); ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="sms-wiz-page" data-step="3" style="display:none">
            <h3 style="margin-top:0">Family</h3>
            <div class="sms-grid-2">
                <div class="sms-field"><label>Father's Name</label><input class="sms-input" name="father_name"></div>
                <div class="sms-field"><label>Mother's Name</label><input class="sms-input" name="mother_name"></div>
            </div>
            <div class="sms-grid-2" style="margin-top:14px">
                <div class="sms-field"><label>Guardian Phone</label><input class="sms-input" name="guardian_phone"></div>
                <div class="sms-field"><label>Email</label><input class="sms-input" type="email" name="email"></div>
            </div>
        </div>

        <div class="sms-wiz-page" data-step="4" style="display:none">
            <h3 style="margin-top:0">Contact</h3>
            <div class="sms-field"><label>Phone</label><input class="sms-input" name="phone"></div>
            <div class="sms-field" style="margin-top:14px"><label>Full Address</label><textarea class="sms-textarea" name="address" rows="4"></textarea></div>
        </div>

        <div class="sms-wiz-page" data-step="5" style="display:none">
            <h3 style="margin-top:0">Review &amp; Submit</h3>
            <p class="sms-muted">Click <strong>Save Student</strong> to enroll. You can edit details later.</p>
        </div>

        <input type="hidden" name="status" value="active">

        <div class="sms-divider"></div>
        <div class="sms-flex">
            <button type="button" class="sms-btn sms-btn-ghost" id="sms-wiz-prev" disabled><?php echo SMS_Icons::svg('arrow-left',14); ?><span>Back</span></button>
            <div class="sms-spacer"></div>
            <button type="button" class="sms-btn sms-btn-primary" id="sms-wiz-next"><span>Next</span><?php echo SMS_Icons::svg('arrow-right',14); ?></button>
            <button type="submit" class="sms-btn sms-btn-success" id="sms-wiz-submit" style="display:none"><?php echo SMS_Icons::svg('check',14); ?><span>Save Student</span></button>
        </div>
    </form>

    <script>
    jQuery(function($){ if (typeof window.smsShowStep === 'function') { window.smsShowStep(1); } });
    </script>
</div>
