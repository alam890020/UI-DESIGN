<?php
/**
 * Setup Wizard View — 6 Steps
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

$current_step = (int) get_option( 'ss_wizard_step', 1 );
$school_id    = (int) get_option( 'ss_default_school_id', 1 );
global $wpdb;
$classes = $wpdb->get_results( $wpdb->prepare(
	"SELECT cs.ID, c.label FROM {$wpdb->prefix}ss_class_school cs INNER JOIN {$wpdb->prefix}ss_classes c ON cs.class_id=c.ID WHERE cs.school_id=%d ORDER BY c.label",
	$school_id
) );
?>
<style>
body.ss-admin-page #adminmenuback, body.ss-admin-page #adminmenuwrap { display:none; }
body.ss-admin-page #wpcontent { margin-left:0; }
</style>
<div class="ss-wrap" style="background:linear-gradient(135deg,#EEF2FF 0%,#F8FAFF 100%);min-height:100vh;display:flex;align-items:flex-start;justify-content:center;padding:40px 20px;">
	<div class="ss-wizard-wrap" style="width:100%;max-width:680px;">
		<!-- Logo -->
		<div style="text-align:center;margin-bottom:36px;">
			<h1 style="font-family:'Nunito',sans-serif;font-size:32px;font-weight:800;background:linear-gradient(135deg,#4F46E5,#0EA5E9);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin:0;">🎓 School Softwere</h1>
			<p style="color:var(--ss-text-muted);margin-top:6px;"><?php esc_html_e( 'Setup Wizard — Let\'s get your school ready!', 'school-softwere' ); ?></p>
		</div>

		<!-- Steps indicator -->
		<div class="ss-wizard-steps" style="margin-bottom:36px;">
			<?php
			$steps = array(
				1 => __( 'School', 'school-softwere' ),
				2 => __( 'Classes', 'school-softwere' ),
				3 => __( 'Session', 'school-softwere' ),
				4 => __( 'Admin', 'school-softwere' ),
				5 => __( 'Fees', 'school-softwere' ),
				6 => __( 'Done', 'school-softwere' ),
			);
			foreach ( $steps as $num => $label ) :
				$cls = $num < $current_step ? 'done' : ( $num === $current_step ? 'active' : '' );
			?>
			<div class="ss-wizard-step <?php echo esc_attr( $cls ); ?>">
				<div class="ss-wizard-step-num"><?php echo $cls === 'done' ? '✓' : esc_html( $num ); ?></div>
				<div class="ss-wizard-step-label"><?php echo esc_html( $label ); ?></div>
			</div>
			<?php endforeach; ?>
		</div>

		<!-- Panes -->
		<div class="ss-card" style="padding:32px;">
			<div id="wizard-step-1" class="ss-wizard-pane <?php echo $current_step === 1 ? 'active' : ''; ?>">
				<h3 style="font-family:'Nunito',sans-serif;font-weight:700;font-size:20px;margin:0 0 20px;">🏫 <?php esc_html_e( 'School Details', 'school-softwere' ); ?></h3>
				<div class="ss-form-row">
					<div class="ss-form-group" style="grid-column:1/-1;"><label><?php esc_html_e( 'School Name', 'school-softwere' ); ?> <span class="req">*</span></label><input type="text" id="w1_school_name" required placeholder="<?php esc_attr_e( 'e.g. Springfield Elementary', 'school-softwere' ); ?>"></div>
				</div>
				<div class="ss-form-row">
					<div class="ss-form-group"><label><?php esc_html_e( 'Phone', 'school-softwere' ); ?></label><input type="tel" id="w1_phone"></div>
					<div class="ss-form-group"><label><?php esc_html_e( 'Email', 'school-softwere' ); ?></label><input type="email" id="w1_email"></div>
				</div>
				<div class="ss-form-group"><label><?php esc_html_e( 'Address', 'school-softwere' ); ?></label><textarea id="w1_address" rows="2"></textarea></div>
				<div class="ss-form-group"><label><?php esc_html_e( 'Registration Number', 'school-softwere' ); ?></label><input type="text" id="w1_reg"></div>
				<div class="ss-wizard-nav">
					<span></span>
					<button type="button" class="ss-btn ss-btn-primary ss-btn-lg" onclick="wizardNext(1)">
						<?php esc_html_e( 'Next', 'school-softwere' ); ?> →
					</button>
				</div>
			</div>

			<div id="wizard-step-2" class="ss-wizard-pane <?php echo $current_step === 2 ? 'active' : ''; ?>">
				<h3 style="font-family:'Nunito',sans-serif;font-weight:700;font-size:20px;margin:0 0 20px;">📚 <?php esc_html_e( 'Create Classes', 'school-softwere' ); ?></h3>
				<p style="color:var(--ss-text-muted);font-size:13px;margin-bottom:16px;"><?php esc_html_e( 'Enter class names, one per line (e.g. Class 1, Class 2…)', 'school-softwere' ); ?></p>
				<div class="ss-form-group"><label><?php esc_html_e( 'Class Names', 'school-softwere' ); ?></label><textarea id="w2_classes" rows="6" placeholder="Nursery&#10;KG&#10;Class 1&#10;Class 2"></textarea></div>
				<div class="ss-wizard-nav">
					<button type="button" class="ss-btn ss-btn-secondary" onclick="wizardGoTo(1)">← <?php esc_html_e( 'Back', 'school-softwere' ); ?></button>
					<button type="button" class="ss-btn ss-btn-primary ss-btn-lg" onclick="wizardNext(2)"><?php esc_html_e( 'Next', 'school-softwere' ); ?> →</button>
				</div>
			</div>

			<div id="wizard-step-3" class="ss-wizard-pane <?php echo $current_step === 3 ? 'active' : ''; ?>">
				<h3 style="font-family:'Nunito',sans-serif;font-weight:700;font-size:20px;margin:0 0 20px;">📅 <?php esc_html_e( 'Academic Session', 'school-softwere' ); ?></h3>
				<div class="ss-form-row">
					<div class="ss-form-group" style="grid-column:1/-1;"><label><?php esc_html_e( 'Session Label', 'school-softwere' ); ?> <span class="req">*</span></label><input type="text" id="w3_label" placeholder="<?php esc_attr_e( 'e.g. 2025-2026', 'school-softwere' ); ?>"></div>
				</div>
				<div class="ss-form-row">
					<div class="ss-form-group"><label><?php esc_html_e( 'Start Date', 'school-softwere' ); ?></label><input type="text" id="w3_start" class="ss-datepicker"></div>
					<div class="ss-form-group"><label><?php esc_html_e( 'End Date', 'school-softwere' ); ?></label><input type="text" id="w3_end" class="ss-datepicker"></div>
				</div>
				<div class="ss-wizard-nav">
					<button type="button" class="ss-btn ss-btn-secondary" onclick="wizardGoTo(2)">← <?php esc_html_e( 'Back', 'school-softwere' ); ?></button>
					<button type="button" class="ss-btn ss-btn-primary ss-btn-lg" onclick="wizardNext(3)"><?php esc_html_e( 'Next', 'school-softwere' ); ?> →</button>
				</div>
			</div>

			<div id="wizard-step-4" class="ss-wizard-pane <?php echo $current_step === 4 ? 'active' : ''; ?>">
				<h3 style="font-family:'Nunito',sans-serif;font-weight:700;font-size:20px;margin:0 0 20px;">👤 <?php esc_html_e( 'Add Admin Staff', 'school-softwere' ); ?></h3>
				<div class="ss-form-row">
					<div class="ss-form-group"><label><?php esc_html_e( 'Full Name', 'school-softwere' ); ?></label><input type="text" id="w4_name"></div>
					<div class="ss-form-group"><label><?php esc_html_e( 'Username', 'school-softwere' ); ?></label><input type="text" id="w4_user"></div>
				</div>
				<div class="ss-form-row">
					<div class="ss-form-group"><label><?php esc_html_e( 'Email', 'school-softwere' ); ?></label><input type="email" id="w4_email"></div>
					<div class="ss-form-group"><label><?php esc_html_e( 'Password', 'school-softwere' ); ?></label><input type="password" id="w4_pass"></div>
				</div>
				<div class="ss-wizard-nav">
					<button type="button" class="ss-btn ss-btn-secondary" onclick="wizardGoTo(3)">← <?php esc_html_e( 'Back', 'school-softwere' ); ?></button>
					<button type="button" class="ss-btn ss-btn-primary ss-btn-lg" onclick="wizardNext(4)"><?php esc_html_e( 'Next', 'school-softwere' ); ?> →</button>
				</div>
			</div>

			<div id="wizard-step-5" class="ss-wizard-pane <?php echo $current_step === 5 ? 'active' : ''; ?>">
				<h3 style="font-family:'Nunito',sans-serif;font-weight:700;font-size:20px;margin:0 0 20px;">💰 <?php esc_html_e( 'Fee Structure', 'school-softwere' ); ?></h3>
				<div class="ss-form-group" style="margin-bottom:16px;">
					<label><?php esc_html_e( 'Class', 'school-softwere' ); ?></label>
					<select id="w5_class" class="ss-select2">
						<option value=""><?php esc_html_e( '— Select Class —', 'school-softwere' ); ?></option>
						<?php foreach ( $classes as $c ) : ?><option value="<?php echo esc_attr( $c->ID ); ?>"><?php echo esc_html( $c->label ); ?></option><?php endforeach; ?>
					</select>
				</div>
				<div id="w5_fees_rows">
					<div style="display:grid;grid-template-columns:1fr 1fr auto;gap:10px;align-items:end;margin-bottom:10px;">
						<input type="text" placeholder="<?php esc_attr_e( 'Fee Name (e.g. Tuition)', 'school-softwere' ); ?>" class="w5_fee_label">
						<input type="number" placeholder="<?php esc_attr_e( 'Amount', 'school-softwere' ); ?>" class="w5_fee_amount">
						<button type="button" class="ss-btn ss-btn-danger ss-btn-sm" onclick="jQuery(this).closest('div').remove()">−</button>
					</div>
				</div>
				<button type="button" class="ss-btn ss-btn-secondary ss-btn-sm" onclick="addFeeRow()">+ <?php esc_html_e( 'Add Fee', 'school-softwere' ); ?></button>
				<div class="ss-wizard-nav" style="margin-top:20px;">
					<button type="button" class="ss-btn ss-btn-secondary" onclick="wizardGoTo(4)">← <?php esc_html_e( 'Back', 'school-softwere' ); ?></button>
					<button type="button" class="ss-btn ss-btn-primary ss-btn-lg" onclick="wizardNext(5)"><?php esc_html_e( 'Next', 'school-softwere' ); ?> →</button>
				</div>
			</div>

			<div id="wizard-step-6" class="ss-wizard-pane <?php echo $current_step === 6 ? 'active' : ''; ?>" style="text-align:center;padding:20px 0;">
				<div style="font-size:64px;margin-bottom:16px;">🎉</div>
				<h3 style="font-family:'Nunito',sans-serif;font-size:28px;font-weight:800;color:var(--ss-success);"><?php esc_html_e( 'Setup Complete!', 'school-softwere' ); ?></h3>
				<p style="color:var(--ss-text-muted);font-size:15px;margin-bottom:28px;"><?php esc_html_e( 'Your school management system is ready to use.', 'school-softwere' ); ?></p>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere' ) ); ?>" class="ss-btn ss-btn-primary ss-btn-lg">🚀 <?php esc_html_e( 'Go to Dashboard', 'school-softwere' ); ?></a>
			</div>
		</div>
	</div>
</div>

<script>
function wizardGoTo(step) {
	jQuery('.ss-wizard-pane').removeClass('active');
	jQuery('#wizard-step-' + step).addClass('active');
}
function addFeeRow() {
	jQuery('#w5_fees_rows').append('<div style="display:grid;grid-template-columns:1fr 1fr auto;gap:10px;align-items:end;margin-bottom:10px;"><input type="text" placeholder="<?php esc_html_e( 'Fee Name', 'school-softwere' ); ?>" class="w5_fee_label"><input type="number" placeholder="<?php esc_html_e( 'Amount', 'school-softwere' ); ?>" class="w5_fee_amount"><button type="button" class="ss-btn ss-btn-danger ss-btn-sm" onclick="jQuery(this).closest(\'div\').remove()">−</button></div>');
}
function wizardNext(step) {
	var postData = { action: 'ss_wizard_step', step: step, nonce: SS.nonce };
	if (step === 1) { postData.school_name = jQuery('#w1_school_name').val(); postData.phone = jQuery('#w1_phone').val(); postData.email = jQuery('#w1_email').val(); postData.address = jQuery('#w1_address').val(); postData.registration_number = jQuery('#w1_reg').val(); if (!postData.school_name) { SS_UI.toast('<?php esc_html_e( 'School name is required.', 'school-softwere' ); ?>', 'warning'); return; } }
	if (step === 2) { var classes = jQuery('#w2_classes').val().split('\n').filter(function(l){return l.trim();}); postData['classes[]'] = classes; }
	if (step === 3) { postData.label = jQuery('#w3_label').val(); postData.start_date = jQuery('#w3_start').val(); postData.end_date = jQuery('#w3_end').val(); if (!postData.label) { SS_UI.toast('<?php esc_html_e( 'Session label is required.', 'school-softwere' ); ?>', 'warning'); return; } }
	if (step === 4) { postData.full_name = jQuery('#w4_name').val(); postData.username = jQuery('#w4_user').val(); postData.email = jQuery('#w4_email').val(); postData.password = jQuery('#w4_pass').val(); }
	if (step === 5) { postData.class_school_id = jQuery('#w5_class').val(); postData['fees[]'] = []; jQuery('#w5_fees_rows > div').each(function(){ var l=jQuery(this).find('.w5_fee_label').val(), a=jQuery(this).find('.w5_fee_amount').val(); if(l&&a) postData['fees[]'].push({label:l,amount:a}); }); }
	if (step === 6) { postData.step = 6; }
	SS_UI.showLoader();
	jQuery.post(SS.ajax_url, postData, function(res) {
		SS_UI.hideLoader();
		if (res.success) { SS_UI.toast(res.message, 'success'); wizardGoTo(step + 1); if (res.data && res.data.redirect) setTimeout(function(){ window.location.href = res.data.redirect; }, 1200); }
		else { SS_UI.toast(res.message || SS.i18n.error, 'error'); }
	});
}
</script>
