<?php
/**
 * SS_Login — [ss_login] shortcode.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Login {

	public static function render( $atts ) {
		if ( is_user_logged_in() ) {
			$url = isset( $atts['redirect'] ) ? esc_url( $atts['redirect'] ) : home_url( '/student-portal/' );
			return '<p class="ss-public-notice">' . sprintf(
				wp_kses( __( 'You are logged in. <a href="%s">Go to your portal →</a>', 'school-softwere' ), array( 'a' => array( 'href' => array() ) ) ),
				$url
			) . '</p>';
		}

		ob_start();
		$redirect = isset( $atts['redirect'] ) ? esc_url( $atts['redirect'] ) : home_url( '/student-portal/' );
		?>
		<div class="ss-public-login-wrap">
			<div class="ss-public-login-card">
				<div class="ss-public-login-header">
					<h2>🎓 <?php esc_html_e( 'Student / Parent Login', 'school-softwere' ); ?></h2>
					<p><?php esc_html_e( 'Sign in to access your school portal.', 'school-softwere' ); ?></p>
				</div>
				<form id="ss-public-login-form" method="post">
					<?php wp_nonce_field( 'ss_nonce', 'nonce' ); ?>
					<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect ); ?>">
					<div class="ss-pub-form-group">
						<label for="ss_username"><?php esc_html_e( 'Username / Email', 'school-softwere' ); ?></label>
						<input type="text" id="ss_username" name="ss_username" required autocomplete="username">
					</div>
					<div class="ss-pub-form-group">
						<label for="ss_password"><?php esc_html_e( 'Password', 'school-softwere' ); ?></label>
						<input type="password" id="ss_password" name="ss_password" required autocomplete="current-password">
					</div>
					<div id="ss-login-error" style="color:#EF4444;font-size:13px;margin-bottom:12px;display:none;"></div>
					<button type="submit" class="ss-pub-btn ss-pub-btn-primary" id="ss-login-submit">
						<?php esc_html_e( 'Sign In', 'school-softwere' ); ?>
					</button>
					<p style="text-align:center;margin-top:12px;font-size:13px;">
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot password?', 'school-softwere' ); ?></a>
					</p>
				</form>
			</div>
		</div>
		<script>
		jQuery(function($){
			$('#ss-public-login-form').on('submit',function(e){
				e.preventDefault();
				var $btn=$('#ss-login-submit');
				$btn.text('<?php esc_html_e( 'Signing in…', 'school-softwere' ); ?>').prop('disabled',true);
				$.post(SS_Public.ajax_url,{action:'ss_frontend_login',username:$('#ss_username').val(),password:$('#ss_password').val(),redirect_to:$('[name="redirect_to"]').val(),nonce:SS_Public.nonce},function(res){
					if(res.success){ window.location.href=res.data.redirect; }
					else{ $('#ss-login-error').text(res.data.message||'<?php esc_html_e( 'Invalid credentials.', 'school-softwere' ); ?>').show(); $btn.text('<?php esc_html_e( 'Sign In', 'school-softwere' ); ?>').prop('disabled',false); }
				});
			});
		});
		</script>
		<?php
		return ob_get_clean();
	}
}

// AJAX login handler
add_action( 'wp_ajax_nopriv_ss_frontend_login', function () {
	check_ajax_referer( 'ss_nonce', 'nonce' );
	$username    = sanitize_text_field( wp_unslash( $_POST['username'] ?? '' ) );
	$password    = sanitize_text_field( wp_unslash( $_POST['password'] ?? '' ) );
	$redirect_to = esc_url_raw( wp_unslash( $_POST['redirect_to'] ?? home_url() ) );

	$user = wp_authenticate( $username, $password );
	if ( is_wp_error( $user ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid username or password.', 'school-softwere' ) ) );
	}
	wp_set_auth_cookie( $user->ID );
	wp_send_json_success( array( 'redirect' => $redirect_to ) );
} );
