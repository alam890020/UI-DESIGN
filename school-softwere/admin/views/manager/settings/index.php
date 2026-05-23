<?php
/**
 * Settings View
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

$school_id = SS_Helper::get_current_school_id();
$settings  = SS_Config::get_all( $school_id );
$defaults  = SS_Config::defaults();
foreach ( $defaults as $key => $default ) {
	if ( ! isset( $settings[ $key ] ) ) $settings[ $key ] = $default;
}
?>
<div class="ss-wrap">
	<?php include SS_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
	<div class="ss-page-content">
		<div class="ss-page-title-row">
			<div>
				<h1 class="ss-page-title">⚙️ <?php esc_html_e( 'Settings', 'school-softwere' ); ?></h1>
				<p class="ss-breadcrumb"><a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere' ) ); ?>"><?php esc_html_e( 'Dashboard', 'school-softwere' ); ?></a> › <?php esc_html_e( 'Settings', 'school-softwere' ); ?></p>
			</div>
		</div>

		<div class="ss-tabs-container">
			<div class="ss-tabs">
				<button class="ss-tab-btn active" data-tab="tab-general">🏫 <?php esc_html_e( 'General', 'school-softwere' ); ?></button>
				<button class="ss-tab-btn" data-tab="tab-email">📧 <?php esc_html_e( 'Email', 'school-softwere' ); ?></button>
				<button class="ss-tab-btn" data-tab="tab-fee">💰 <?php esc_html_e( 'Fee', 'school-softwere' ); ?></button>
				<button class="ss-tab-btn" data-tab="tab-print">🖨️ <?php esc_html_e( 'Print', 'school-softwere' ); ?></button>
			</div>

			<form id="ss-settings-form" class="ss-ajax-form" data-reload="0">
				<input type="hidden" name="action" value="ss_save_settings">
				<input type="hidden" name="school_id" value="<?php echo esc_attr( $school_id ); ?>">

				<!-- General -->
				<div id="tab-general" class="ss-tab-pane active">
					<div class="ss-card" style="margin-top:20px;">
						<div class="ss-card-header"><h4 class="ss-card-title"><?php esc_html_e( 'General Settings', 'school-softwere' ); ?></h4></div>
						<div class="ss-form-row">
							<div class="ss-form-group"><label><?php esc_html_e( 'School Name', 'school-softwere' ); ?></label><input type="text" name="school_name" value="<?php echo esc_attr( $settings['school_name'] ); ?>"></div>
							<div class="ss-form-group"><label><?php esc_html_e( 'Tagline', 'school-softwere' ); ?></label><input type="text" name="school_tagline" value="<?php echo esc_attr( $settings['school_tagline'] ); ?>"></div>
						</div>
						<div class="ss-form-row">
							<div class="ss-form-group"><label><?php esc_html_e( 'Currency Symbol', 'school-softwere' ); ?></label><input type="text" name="currency_symbol" value="<?php echo esc_attr( $settings['currency_symbol'] ); ?>"></div>
							<div class="ss-form-group"><label><?php esc_html_e( 'Date Format', 'school-softwere' ); ?></label>
								<select name="date_format">
									<option value="d/m/Y" <?php selected( $settings['date_format'], 'd/m/Y' ); ?>>DD/MM/YYYY</option>
									<option value="m/d/Y" <?php selected( $settings['date_format'], 'm/d/Y' ); ?>>MM/DD/YYYY</option>
									<option value="Y-m-d" <?php selected( $settings['date_format'], 'Y-m-d' ); ?>>YYYY-MM-DD</option>
								</select>
							</div>
							<div class="ss-form-group"><label><?php esc_html_e( 'Academic Year Start Month', 'school-softwere' ); ?></label>
								<select name="academic_year_month">
									<?php for ( $m = 1; $m <= 12; $m++ ) : ?><option value="<?php echo esc_attr( $m ); ?>" <?php selected( $settings['academic_year_month'], $m ); ?>><?php echo esc_html( date_i18n( 'F', mktime( 0, 0, 0, $m, 1 ) ) ); ?></option><?php endfor; ?>
								</select>
							</div>
						</div>
					</div>
				</div>

				<!-- Email -->
				<div id="tab-email" class="ss-tab-pane">
					<div class="ss-card" style="margin-top:20px;">
						<div class="ss-card-header"><h4 class="ss-card-title"><?php esc_html_e( 'Email / SMTP Settings', 'school-softwere' ); ?></h4></div>
						<div class="ss-form-row">
							<div class="ss-form-group"><label><?php esc_html_e( 'From Name', 'school-softwere' ); ?></label><input type="text" name="from_name" value="<?php echo esc_attr( $settings['from_name'] ); ?>"></div>
							<div class="ss-form-group"><label><?php esc_html_e( 'From Email', 'school-softwere' ); ?></label><input type="email" name="from_email" value="<?php echo esc_attr( $settings['from_email'] ); ?>"></div>
						</div>
						<div class="ss-form-row">
							<div class="ss-form-group"><label><?php esc_html_e( 'SMTP Host', 'school-softwere' ); ?></label><input type="text" name="smtp_host" value="<?php echo esc_attr( $settings['smtp_host'] ); ?>"></div>
							<div class="ss-form-group"><label><?php esc_html_e( 'SMTP Port', 'school-softwere' ); ?></label><input type="number" name="smtp_port" value="<?php echo esc_attr( $settings['smtp_port'] ); ?>"></div>
						</div>
						<div class="ss-form-row">
							<div class="ss-form-group"><label><?php esc_html_e( 'SMTP Username', 'school-softwere' ); ?></label><input type="text" name="smtp_user" value="<?php echo esc_attr( $settings['smtp_user'] ); ?>"></div>
							<div class="ss-form-group"><label><?php esc_html_e( 'SMTP Password', 'school-softwere' ); ?></label><input type="password" name="smtp_pass" value="<?php echo esc_attr( $settings['smtp_pass'] ); ?>"></div>
						</div>
					</div>
				</div>

				<!-- Fee -->
				<div id="tab-fee" class="ss-tab-pane">
					<div class="ss-card" style="margin-top:20px;">
						<div class="ss-card-header"><h4 class="ss-card-title"><?php esc_html_e( 'Fee Settings', 'school-softwere' ); ?></h4></div>
						<div class="ss-form-row">
							<div class="ss-form-group"><label><?php esc_html_e( 'Invoice Prefix', 'school-softwere' ); ?></label><input type="text" name="invoice_prefix" value="<?php echo esc_attr( $settings['invoice_prefix'] ); ?>"></div>
							<div class="ss-form-group"><label><?php esc_html_e( 'Invoice Number Padding', 'school-softwere' ); ?></label><input type="number" name="invoice_padding" value="<?php echo esc_attr( $settings['invoice_padding'] ); ?>"></div>
						</div>
						<div class="ss-form-row">
							<div class="ss-form-group"><label><?php esc_html_e( 'Late Fee Amount', 'school-softwere' ); ?></label><input type="number" step="0.01" name="late_fee_amount" value="<?php echo esc_attr( $settings['late_fee_amount'] ); ?>"></div>
							<div class="ss-form-group"><label><?php esc_html_e( 'Charge Late Fee After (Days)', 'school-softwere' ); ?></label><input type="number" name="late_fee_after_days" value="<?php echo esc_attr( $settings['late_fee_after_days'] ); ?>"></div>
						</div>
					</div>
				</div>

				<!-- Print -->
				<div id="tab-print" class="ss-tab-pane">
					<div class="ss-card" style="margin-top:20px;">
						<div class="ss-card-header"><h4 class="ss-card-title"><?php esc_html_e( 'Print Settings', 'school-softwere' ); ?></h4></div>
						<div class="ss-form-row">
							<div class="ss-form-group"><label><?php esc_html_e( 'Page Size', 'school-softwere' ); ?></label>
								<select name="print_page_size"><option value="A4" <?php selected( $settings['print_page_size'], 'A4' ); ?>>A4</option><option value="Letter" <?php selected( $settings['print_page_size'], 'Letter' ); ?>>Letter</option></select>
							</div>
							<div class="ss-form-group"><label><?php esc_html_e( 'Orientation', 'school-softwere' ); ?></label>
								<select name="print_orientation"><option value="portrait" <?php selected( $settings['print_orientation'], 'portrait' ); ?>><?php esc_html_e( 'Portrait', 'school-softwere' ); ?></option><option value="landscape" <?php selected( $settings['print_orientation'], 'landscape' ); ?>><?php esc_html_e( 'Landscape', 'school-softwere' ); ?></option></select>
							</div>
							<div class="ss-form-group"><label><?php esc_html_e( 'Show Watermark', 'school-softwere' ); ?></label>
								<select name="print_watermark"><option value="0" <?php selected( $settings['print_watermark'], '0' ); ?>><?php esc_html_e( 'No', 'school-softwere' ); ?></option><option value="1" <?php selected( $settings['print_watermark'], '1' ); ?>><?php esc_html_e( 'Yes', 'school-softwere' ); ?></option></select>
							</div>
						</div>
					</div>
				</div>

				<div style="margin-top:24px;display:flex;justify-content:flex-end;">
					<button type="submit" class="ss-btn ss-btn-primary ss-btn-lg">💾 <?php esc_html_e( 'Save Settings', 'school-softwere' ); ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
