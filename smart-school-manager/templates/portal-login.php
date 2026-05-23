<?php
/**
 * Frontend portal login (alternate template).
 *
 * @package SmartSchoolManager
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="ssm-public ssm-public-card ssm-public-login">
    <h2>Sign in to your portal</h2>
    <?php
    wp_login_form( array(
        'redirect'       => SSM_Helper::portal_url(),
        'label_username' => 'Email or Username',
        'label_password' => 'Password',
        'label_log_in'   => 'Sign In',
    ) );
    ?>
    <p><a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">Forgot password?</a></p>
</div>
