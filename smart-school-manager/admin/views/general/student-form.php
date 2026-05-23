<?php
/**
 * Backend Student Registration Form (multi-step wizard).
 *
 * @package SmartSchoolManager
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb; $p = $wpdb->prefix . 'ssm_';
$classes  = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes ORDER BY name" );
$mediums  = $wpdb->get_results( "SELECT id, name FROM {$p}mediums ORDER BY name" );
$types    = $wpdb->get_results( "SELECT id, name FROM {$p}student_types ORDER BY name" );
$sessions = $wpdb->get_results( "SELECT id, name, is_current FROM {$p}sessions ORDER BY id DESC" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header(
        'Student Registration Form',
        'Multi-step wizard to enroll a new student',
        'dashicons-welcome-add-page',
        array( array( 'href' => SSM_Helper::admin_url('ssm-students'), 'label' => 'View Students', 'icon' => 'dashicons-groups', 'class' => 'ssm-btn-ghost' ) )
    ); ?>

    <div class="ssm-wizard-steps">
        <div class="ssm-wizard-step active" data-step="1"><span class="num">1</span><div><strong>Personal</strong><div class="ssm-muted" style="font-size:11px">Identity</div></div></div>
        <div class="ssm-wizard-step" data-step="2"><span class="num">2</span><div><strong>Academic</strong><div class="ssm-muted" style="font-size:11px">Class & Session</div></div></div>
        <div class="ssm-wizard-step" data-step="3"><span class="num">3</span><div><strong>Family</strong><div class="ssm-muted" style="font-size:11px">Parents</div></div></div>
        <div class="ssm-wizard-step" data-step="4"><span class="num">4</span><div><strong>Address</strong><div class="ssm-muted" style="font-size:11px">Contact</div></div></div>
        <div class="ssm-wizard-step" data-step="5"><span class="num">5</span><div><strong>Review</strong><div class="ssm-muted" style="font-size:11px">Submit</div></div></div>
    </div>

    <form class="ssm-card ssm-ajax-form" data-entity="student" id="ssm-student-wizard">
        <!-- Step 1 -->
        <div class="ssm-wizard-page" data-step="1">
            <h3 style="margin-top:0">Personal Details</h3>
            <div class="ssm-form-grid-3">
                <div class="ssm-field"><label>Admission No</label><input class="ssm-input" name="admission_no" placeholder="ADM-001"></div>
                <div class="ssm-field"><label>Roll No</label><input class="ssm-input" name="roll_no"></div>
                <div class="ssm-field"><label>Admission Date</label><input class="ssm-input" type="date" name="admission_date" value="<?php echo esc_attr( date('Y-m-d') ); ?>"></div>
            </div>
            <div class="ssm-form-grid-3" style="margin-top:12px">
                <div class="ssm-field"><label>First Name *</label><input class="ssm-input" name="first_name" required></div>
                <div class="ssm-field"><label>Last Name</label><input class="ssm-input" name="last_name"></div>
                <div class="ssm-field"><label>Gender</label>
                    <select class="ssm-select" name="gender"><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
                </div>
            </div>
            <div class="ssm-form-grid-3" style="margin-top:12px">
                <div class="ssm-field"><label>Date of Birth</label><input class="ssm-input" type="date" name="dob"></div>
                <div class="ssm-field"><label>Blood Group</label>
                    <select class="ssm-select" name="blood_group">
                        <?php foreach ( array('','A+','A-','B+','B-','O+','O-','AB+','AB-') as $bg ) : ?><option><?php echo esc_html( $bg ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="ssm-field"><label>Photo URL</label><input class="ssm-input" name="photo" placeholder="https://…"></div>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="ssm-wizard-page" data-step="2" style="display:none">
            <h3 style="margin-top:0">Academic Details</h3>
            <div class="ssm-form-grid-3">
                <div class="ssm-field"><label>Class *</label>
                    <select class="ssm-select" name="class_id" required>
                        <option value="">— Select —</option>
                        <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="ssm-field"><label>Section</label><input class="ssm-input" name="section"></div>
                <div class="ssm-field"><label>Session</label>
                    <select class="ssm-select" name="session_id">
                        <option value="0">— Select —</option>
                        <?php foreach ( $sessions as $s ) : ?><option value="<?php echo (int) $s->id; ?>" <?php selected( $s->is_current, 1 ); ?>><?php echo esc_html( $s->name ); ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="ssm-form-grid" style="margin-top:12px">
                <div class="ssm-field"><label>Student Type</label>
                    <select class="ssm-select" name="student_type_id">
                        <option value="0">— Select —</option>
                        <?php foreach ( $types as $t ) : ?><option value="<?php echo (int) $t->id; ?>"><?php echo esc_html( $t->name ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="ssm-field"><label>Medium</label>
                    <select class="ssm-select" name="medium_id">
                        <option value="0">— Select —</option>
                        <?php foreach ( $mediums as $m ) : ?><option value="<?php echo (int) $m->id; ?>"><?php echo esc_html( $m->name ); ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="ssm-wizard-page" data-step="3" style="display:none">
            <h3 style="margin-top:0">Family Details</h3>
            <div class="ssm-form-grid">
                <div class="ssm-field"><label>Father's Name</label><input class="ssm-input" name="father_name"></div>
                <div class="ssm-field"><label>Mother's Name</label><input class="ssm-input" name="mother_name"></div>
            </div>
            <div class="ssm-form-grid" style="margin-top:12px">
                <div class="ssm-field"><label>Guardian Phone</label><input class="ssm-input" name="guardian_phone"></div>
                <div class="ssm-field"><label>Email</label><input class="ssm-input" type="email" name="email"></div>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="ssm-wizard-page" data-step="4" style="display:none">
            <h3 style="margin-top:0">Address & Contact</h3>
            <div class="ssm-field"><label>Phone</label><input class="ssm-input" name="phone"></div>
            <div class="ssm-field" style="margin-top:12px"><label>Full Address</label><textarea class="ssm-textarea" name="address" rows="4"></textarea></div>
        </div>

        <!-- Step 5 -->
        <div class="ssm-wizard-page" data-step="5" style="display:none">
            <h3 style="margin-top:0">Review &amp; Submit</h3>
            <div id="ssm-wizard-review" class="ssm-card" style="background:#f8fafc"></div>
            <p class="ssm-muted">Click <strong>Save Student</strong> to enroll. You can edit any detail later from the Students page.</p>
        </div>

        <input type="hidden" name="status" value="active">

        <div class="ssm-divider"></div>
        <div style="display:flex;gap:8px">
            <button type="button" class="ssm-btn ssm-btn-ghost" id="ssm-wiz-prev" disabled><span class="dashicons dashicons-arrow-left-alt"></span> Back</button>
            <div class="ssm-spacer"></div>
            <button type="button" class="ssm-btn ssm-btn-primary" id="ssm-wiz-next">Next <span class="dashicons dashicons-arrow-right-alt"></span></button>
            <button type="submit" class="ssm-btn ssm-btn-success" id="ssm-wiz-submit" style="display:none"><span class="dashicons dashicons-saved"></span> Save Student</button>
        </div>
    </form>
</div>

<script>
jQuery(function($){
    var step = 1, max = 5;
    function show(s){
        $('.ssm-wizard-page').hide(); $('.ssm-wizard-page[data-step='+s+']').show();
        $('.ssm-wizard-step').removeClass('active done');
        $('.ssm-wizard-step').each(function(){
            var n = +$(this).data('step');
            if(n < s) $(this).addClass('done');
            else if(n === s) $(this).addClass('active');
        });
        $('#ssm-wiz-prev').prop('disabled', s === 1);
        $('#ssm-wiz-next').toggle(s < max);
        $('#ssm-wiz-submit').toggle(s === max);
        if(s === max) buildReview();
    }
    function buildReview(){
        var $f = $('#ssm-student-wizard');
        var data = {};
        $f.serializeArray().forEach(function(it){ data[it.name] = it.value; });
        var html = '<table class="ssm-table"><tbody>';
        ['admission_no','first_name','last_name','gender','dob','class_id','section','session_id','father_name','mother_name','phone','email','address'].forEach(function(k){
            if(data[k]) html += '<tr><th>'+k.replace('_',' ')+'</th><td>'+$('<i>').text(data[k]).html()+'</td></tr>';
        });
        html += '</tbody></table>';
        $('#ssm-wizard-review').html(html);
    }
    $('#ssm-wiz-next').on('click', function(){ if(step < max){ step++; show(step); } });
    $('#ssm-wiz-prev').on('click', function(){ if(step > 1){ step--; show(step); } });
    show(1);
});
</script>
