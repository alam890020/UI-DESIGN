<?php
/**
 * Frontend shortcodes — embed SSM data in pages/posts.
 *
 * Shortcodes:
 *   [ssm_login]            login form for student/parent portal
 *   [ssm_portal]           student/parent portal dashboard
 *   [ssm_notices]          public notice board
 *   [ssm_events]           public events list / calendar
 *   [ssm_admission_form]   public admission application form
 *   [ssm_inquiry_form]     public inquiry form
 *   [ssm_gallery]          embed school gallery (placeholder)
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Shortcodes {

    public function register() {
        add_shortcode( 'ssm_login',           array( $this, 'sc_login' ) );
        add_shortcode( 'ssm_portal',          array( $this, 'sc_portal' ) );
        add_shortcode( 'ssm_notices',         array( $this, 'sc_notices' ) );
        add_shortcode( 'ssm_events',          array( $this, 'sc_events' ) );
        add_shortcode( 'ssm_admission_form',  array( $this, 'sc_admission_form' ) );
        add_shortcode( 'ssm_inquiry_form',    array( $this, 'sc_inquiry_form' ) );
        add_shortcode( 'ssm_student_form',    array( $this, 'sc_student_form' ) );
        add_shortcode( 'ssm_gallery',         array( $this, 'sc_gallery' ) );
    }

    public function sc_login() {
        if ( is_user_logged_in() ) {
            return '<div class="ssm-public ssm-public-card"><p>You are logged in. <a class="ssm-public-btn" href="' . esc_url( SSM_Helper::portal_url() ) . '">Go to Portal</a></p></div>';
        }
        return wp_login_form( array(
            'echo'           => false,
            'redirect'       => SSM_Helper::portal_url(),
            'label_username' => 'Email or Username',
            'label_password' => 'Password',
            'label_log_in'   => 'Sign In',
        ) );
    }

    public function sc_portal() {
        ob_start();
        if ( ! is_user_logged_in() ) {
            echo '<div class="ssm-public ssm-public-card"><p>Please <a href="' . esc_url( wp_login_url( get_permalink() ) ) . '">sign in</a> to access your portal.</p></div>';
        } else {
            $tpl = SSM_PLUGIN_DIR . 'templates/portal-dashboard.php';
            if ( file_exists( $tpl ) ) include $tpl;
        }
        return ob_get_clean();
    }

    public function sc_notices( $atts ) {
        $a = shortcode_atts( array( 'limit' => 10 ), $atts );
        global $wpdb; $p = $wpdb->prefix . 'ssm_';
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$p}notices ORDER BY id DESC LIMIT %d", absint( $a['limit'] )
        ) );
        ob_start(); ?>
        <div class="ssm-public ssm-public-notices">
            <?php if ( $rows ) : foreach ( $rows as $n ) : ?>
                <article class="ssm-public-card">
                    <h3><?php echo esc_html( $n->title ); ?></h3>
                    <small><?php echo esc_html( $n->posted_at ); ?> &middot; <?php echo esc_html( $n->audience ); ?></small>
                    <p><?php echo wp_kses_post( wpautop( $n->body ) ); ?></p>
                </article>
            <?php endforeach; else : ?>
                <p>No notices yet.</p>
            <?php endif; ?>
        </div>
        <?php return ob_get_clean();
    }

    public function sc_events( $atts ) {
        $a = shortcode_atts( array( 'limit' => 10 ), $atts );
        global $wpdb; $p = $wpdb->prefix . 'ssm_';
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$p}events ORDER BY start_date ASC LIMIT %d", absint( $a['limit'] )
        ) );
        ob_start(); ?>
        <div class="ssm-public ssm-public-events">
            <?php if ( $rows ) : foreach ( $rows as $e ) : ?>
                <article class="ssm-public-card">
                    <h3><?php echo esc_html( $e->title ); ?></h3>
                    <small><?php echo esc_html( $e->start_date ); ?> &middot; <?php echo esc_html( $e->location ); ?></small>
                    <p><?php echo wp_kses_post( wpautop( $e->description ) ); ?></p>
                </article>
            <?php endforeach; else : ?>
                <p>No upcoming events.</p>
            <?php endif; ?>
        </div>
        <?php return ob_get_clean();
    }

    public function sc_admission_form() {
        global $wpdb; $p = $wpdb->prefix . 'ssm_';
        $classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes ORDER BY name" );
        ob_start(); ?>
        <div class="ssm-public ssm-public-card">
            <h2>Admission Application</h2>
            <form class="ssm-public-form" data-ssm-public="admission">
                <div class="ssm-public-grid">
                    <label>Applicant Name<input name="applicant_name" required></label>
                    <label>Email<input type="email" name="email"></label>
                </div>
                <div class="ssm-public-grid">
                    <label>Phone<input name="phone" required></label>
                    <label>Class
                        <select name="class_id">
                            <option value="0">— Select —</option>
                            <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?>
                        </select>
                    </label>
                </div>
                <label>Notes / Background<textarea name="notes" rows="4"></textarea></label>
                <button type="submit" class="ssm-public-btn">Submit Application</button>
                <div class="ssm-public-result"></div>
            </form>
        </div>
        <?php return ob_get_clean();
    }

    public function sc_inquiry_form() {
        ob_start(); ?>
        <div class="ssm-public ssm-public-card">
            <h2>General Inquiry</h2>
            <form class="ssm-public-form" data-ssm-public="inquiry">
                <div class="ssm-public-grid">
                    <label>Name<input name="name" required></label>
                    <label>Email<input type="email" name="email"></label>
                </div>
                <div class="ssm-public-grid">
                    <label>Phone<input name="phone"></label>
                    <label>Source
                        <select name="source"><option>Website</option><option>Referral</option><option>Walk-in</option><option>Phone</option></select>
                    </label>
                </div>
                <label>Message<textarea name="message" rows="4"></textarea></label>
                <button type="submit" class="ssm-public-btn">Send Inquiry</button>
                <div class="ssm-public-result"></div>
            </form>
        </div>
        <?php return ob_get_clean();
    }

    public function sc_gallery() {
        return '<div class="ssm-public ssm-public-card"><p>Use your favourite gallery plugin alongside Smart School Manager. The plugin focuses on management — galleries can be embedded with any standard WordPress gallery shortcode.</p></div>';
    }

    /**
     * Comprehensive frontend student registration form
     * Usage:  [ssm_student_form]
     */
    public function sc_student_form() {
        global $wpdb; $p = $wpdb->prefix . 'ssm_';
        $classes  = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes ORDER BY name" );
        $mediums  = $wpdb->get_results( "SELECT id, name FROM {$p}mediums ORDER BY name" );
        $types    = $wpdb->get_results( "SELECT id, name FROM {$p}student_types ORDER BY name" );
        ob_start(); ?>
        <div class="ssm-public ssm-public-card ssm-public-student-form">
            <h2>Student Registration</h2>
            <p class="ssm-muted">Fill out the form below to register a new student. Required fields are marked with *.</p>

            <form class="ssm-public-form" data-ssm-public="student">
                <h3>Personal Details</h3>
                <div class="ssm-public-grid">
                    <label>First Name *<input name="first_name" required></label>
                    <label>Last Name<input name="last_name"></label>
                </div>
                <div class="ssm-public-grid">
                    <label>Gender
                        <select name="gender"><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
                    </label>
                    <label>Date of Birth<input type="date" name="dob"></label>
                </div>
                <div class="ssm-public-grid">
                    <label>Blood Group
                        <select name="blood_group">
                            <?php foreach ( array('','A+','A-','B+','B-','O+','O-','AB+','AB-') as $bg ) : ?><option><?php echo esc_html( $bg ); ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label>Photo URL<input name="photo" placeholder="https://..."></label>
                </div>

                <h3>Academic</h3>
                <div class="ssm-public-grid">
                    <label>Class *
                        <select name="class_id" required>
                            <option value="">— Select —</option>
                            <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label>Medium
                        <select name="medium_id">
                            <option value="0">— Select —</option>
                            <?php foreach ( $mediums as $m ) : ?><option value="<?php echo (int) $m->id; ?>"><?php echo esc_html( $m->name ); ?></option><?php endforeach; ?>
                        </select>
                    </label>
                </div>
                <div class="ssm-public-grid">
                    <label>Student Type
                        <select name="student_type_id">
                            <option value="0">— Select —</option>
                            <?php foreach ( $types as $t ) : ?><option value="<?php echo (int) $t->id; ?>"><?php echo esc_html( $t->name ); ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label>Roll No<input name="roll_no"></label>
                </div>

                <h3>Family</h3>
                <div class="ssm-public-grid">
                    <label>Father's Name<input name="father_name"></label>
                    <label>Mother's Name<input name="mother_name"></label>
                </div>
                <div class="ssm-public-grid">
                    <label>Guardian Phone *<input name="guardian_phone" required></label>
                    <label>Email<input type="email" name="email"></label>
                </div>

                <h3>Address & Contact</h3>
                <div class="ssm-public-grid">
                    <label>Phone<input name="phone"></label>
                    <label>Photo (URL)<input name="photo"></label>
                </div>
                <label>Full Address<textarea name="address" rows="3"></textarea></label>

                <button type="submit" class="ssm-public-btn">Submit Registration</button>
                <div class="ssm-public-result"></div>
                <p class="ssm-muted" style="font-size:12px;margin-top:8px">Submitted forms appear in the admin under <strong>General Admin → Students</strong> with status <em>pending review</em>.</p>
            </form>
        </div>
        <?php return ob_get_clean();
    }
}
