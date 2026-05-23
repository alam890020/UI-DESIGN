<?php
/**
 * Frontend shortcodes.
 *
 * @package SchoolManagementStudio
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_Shortcodes {

    public function register() {
        add_shortcode( 'sms_student_form',   array( $this, 'sc_student_form' ) );
        add_shortcode( 'sms_admission_form', array( $this, 'sc_admission_form' ) );
        add_shortcode( 'sms_inquiry_form',   array( $this, 'sc_inquiry_form' ) );
        add_shortcode( 'sms_notices',        array( $this, 'sc_notices' ) );
        add_shortcode( 'sms_events',         array( $this, 'sc_events' ) );
    }

    public function sc_student_form() {
        global $wpdb; $p = $wpdb->prefix . 'sms_';
        $classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes ORDER BY name" );
        $mediums = $wpdb->get_results( "SELECT id, name FROM {$p}mediums ORDER BY name" );
        $types   = $wpdb->get_results( "SELECT id, name FROM {$p}student_types ORDER BY name" );
        ob_start(); ?>
        <div class="sms-fp sms-fp-card">
            <h2>Student Registration</h2>
            <p class="sms-fp-muted">Fill the form to enroll a new student. The school will review and confirm your application.</p>
            <form class="sms-fp-form" data-sms-public="student">
                <h3>Personal</h3>
                <div class="sms-fp-grid">
                    <label>First Name *<input name="first_name" required></label>
                    <label>Last Name<input name="last_name"></label>
                </div>
                <div class="sms-fp-grid">
                    <label>Gender
                        <select name="gender"><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
                    </label>
                    <label>Date of Birth<input type="date" name="dob"></label>
                </div>
                <div class="sms-fp-grid">
                    <label>Blood Group
                        <select name="blood_group"><?php foreach ( array('','A+','A-','B+','B-','O+','O-','AB+','AB-') as $bg ) : ?><option><?php echo esc_html( $bg ); ?></option><?php endforeach; ?></select>
                    </label>
                    <label>Photo URL<input name="photo" placeholder="https://..."></label>
                </div>

                <h3>Academic</h3>
                <div class="sms-fp-grid">
                    <label>Class *
                        <select name="class_id" required>
                            <option value="">— Select —</option>
                            <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label>Medium
                        <select name="medium_id"><option value="0">— Select —</option><?php foreach ( $mediums as $m ) : ?><option value="<?php echo (int) $m->id; ?>"><?php echo esc_html( $m->name ); ?></option><?php endforeach; ?></select>
                    </label>
                </div>
                <div class="sms-fp-grid">
                    <label>Student Type
                        <select name="student_type_id"><option value="0">— Select —</option><?php foreach ( $types as $t ) : ?><option value="<?php echo (int) $t->id; ?>"><?php echo esc_html( $t->name ); ?></option><?php endforeach; ?></select>
                    </label>
                    <label>Roll No<input name="roll_no"></label>
                </div>

                <h3>Family</h3>
                <div class="sms-fp-grid">
                    <label>Father's Name<input name="father_name"></label>
                    <label>Mother's Name<input name="mother_name"></label>
                </div>
                <div class="sms-fp-grid">
                    <label>Guardian Phone *<input name="guardian_phone" required></label>
                    <label>Email<input type="email" name="email"></label>
                </div>

                <h3>Contact</h3>
                <div class="sms-fp-grid">
                    <label>Phone<input name="phone"></label>
                    <label>Photo URL<input name="photo"></label>
                </div>
                <label>Address<textarea name="address" rows="3"></textarea></label>

                <button type="submit" class="sms-fp-btn">Submit Registration</button>
                <div class="sms-fp-result"></div>
            </form>
        </div>
        <?php return ob_get_clean();
    }

    public function sc_admission_form() {
        global $wpdb; $p = $wpdb->prefix . 'sms_';
        $classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes ORDER BY name" );
        ob_start(); ?>
        <div class="sms-fp sms-fp-card">
            <h2>Admission Application</h2>
            <form class="sms-fp-form" data-sms-public="admission">
                <div class="sms-fp-grid">
                    <label>Applicant Name<input name="applicant_name" required></label>
                    <label>Email<input type="email" name="email"></label>
                </div>
                <div class="sms-fp-grid">
                    <label>Phone<input name="phone" required></label>
                    <label>Class<select name="class_id"><option value="0">— Select —</option><?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?></select></label>
                </div>
                <label>Notes<textarea name="notes" rows="4"></textarea></label>
                <button type="submit" class="sms-fp-btn">Submit Application</button>
                <div class="sms-fp-result"></div>
            </form>
        </div>
        <?php return ob_get_clean();
    }

    public function sc_inquiry_form() {
        ob_start(); ?>
        <div class="sms-fp sms-fp-card">
            <h2>Inquiry</h2>
            <form class="sms-fp-form" data-sms-public="inquiry">
                <div class="sms-fp-grid">
                    <label>Name<input name="name" required></label>
                    <label>Email<input type="email" name="email"></label>
                </div>
                <div class="sms-fp-grid">
                    <label>Phone<input name="phone"></label>
                    <label>Source<select name="source"><option>Website</option><option>Referral</option><option>Walk-in</option></select></label>
                </div>
                <label>Message<textarea name="message" rows="4"></textarea></label>
                <button type="submit" class="sms-fp-btn">Send</button>
                <div class="sms-fp-result"></div>
            </form>
        </div>
        <?php return ob_get_clean();
    }

    public function sc_notices( $atts ) {
        $a = shortcode_atts( array( 'limit' => 10 ), $atts );
        global $wpdb; $p = $wpdb->prefix . 'sms_';
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$p}notices ORDER BY id DESC LIMIT %d", absint( $a['limit'] ) ) );
        ob_start(); ?>
        <div class="sms-fp">
        <?php if ( $rows ) : foreach ( $rows as $n ) : ?>
            <article class="sms-fp-card">
                <h3><?php echo esc_html( $n->title ); ?></h3>
                <small><?php echo esc_html( $n->posted_at ); ?> · <?php echo esc_html( $n->audience ); ?></small>
                <p><?php echo wp_kses_post( wpautop( $n->body ) ); ?></p>
            </article>
        <?php endforeach; else : ?><p>No notices yet.</p><?php endif; ?>
        </div>
        <?php return ob_get_clean();
    }

    public function sc_events( $atts ) {
        $a = shortcode_atts( array( 'limit' => 10 ), $atts );
        global $wpdb; $p = $wpdb->prefix . 'sms_';
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$p}events ORDER BY start_date ASC LIMIT %d", absint( $a['limit'] ) ) );
        ob_start(); ?>
        <div class="sms-fp">
        <?php if ( $rows ) : foreach ( $rows as $e ) : ?>
            <article class="sms-fp-card">
                <h3><?php echo esc_html( $e->title ); ?></h3>
                <small><?php echo esc_html( $e->start_date ); ?> · <?php echo esc_html( $e->location ); ?></small>
                <p><?php echo wp_kses_post( wpautop( $e->description ) ); ?></p>
            </article>
        <?php endforeach; else : ?><p>No events yet.</p><?php endif; ?>
        </div>
        <?php return ob_get_clean();
    }
}
