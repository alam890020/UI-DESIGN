<?php
/**
 * Attendance View
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

global $wpdb;
$school_id  = SS_Helper::get_current_school_id();
$session_id = SS_Helper::get_active_session_id( $school_id );
$classes    = $wpdb->get_results( $wpdb->prepare( "SELECT cs.ID, c.label FROM {$wpdb->prefix}ss_class_school cs INNER JOIN {$wpdb->prefix}ss_classes c ON cs.class_id=c.ID WHERE cs.school_id=%d AND cs.session_id=%d ORDER BY c.label", $school_id, $session_id ) );
$sel_class  = isset( $_GET['class_school_id'] ) ? (int) $_GET['class_school_id'] : 0;
$sel_section= isset( $_GET['section_id'] )      ? (int) $_GET['section_id']      : 0;
$sel_date   = isset( $_GET['date'] ) ? sanitize_text_field( $_GET['date'] ) : current_time( 'Y-m-d' );
$sections   = $sel_class ? $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_sections WHERE class_school_id=%d ORDER BY label", $sel_class ) ) : array();
?>
<div class="ss-wrap">
	<?php include SS_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
	<div class="ss-page-content">
		<div class="ss-page-title-row">
			<h1 class="ss-page-title">✅ <?php esc_html_e( 'Attendance', 'school-softwere' ); ?></h1>
		</div>
		<div class="ss-card" style="margin-bottom:20px;">
			<form method="get" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
				<input type="hidden" name="page" value="school-softwere-attendance">
				<div class="ss-form-group" style="min-width:180px;"><label><?php esc_html_e( 'Class', 'school-softwere' ); ?></label>
					<select name="class_school_id" onchange="this.form.submit()">
						<option value=""><?php esc_html_e( '— Select Class —', 'school-softwere' ); ?></option>
						<?php foreach ( $classes as $c ) : ?><option value="<?php echo esc_attr( $c->ID ); ?>" <?php selected( $sel_class, $c->ID ); ?>><?php echo esc_html( $c->label ); ?></option><?php endforeach; ?>
					</select>
				</div>
				<div class="ss-form-group" style="min-width:140px;"><label><?php esc_html_e( 'Section', 'school-softwere' ); ?></label>
					<select name="section_id" onchange="this.form.submit()">
						<option value=""><?php esc_html_e( '— Select —', 'school-softwere' ); ?></option>
						<?php foreach ( $sections as $s ) : ?><option value="<?php echo esc_attr( $s->ID ); ?>" <?php selected( $sel_section, $s->ID ); ?>><?php echo esc_html( $s->label ); ?></option><?php endforeach; ?>
					</select>
				</div>
				<div class="ss-form-group"><label><?php esc_html_e( 'Date', 'school-softwere' ); ?></label><input type="text" name="date" value="<?php echo esc_attr( $sel_date ); ?>" class="ss-datepicker"></div>
				<button type="submit" class="ss-btn ss-btn-primary">📋 <?php esc_html_e( 'Load', 'school-softwere' ); ?></button>
				<?php if ( $sel_class && $sel_section ) : ?>
				<button type="button" class="ss-btn ss-btn-success" id="ss-mark-all-present" data-class="<?php echo esc_attr( $sel_class ); ?>" data-section="<?php echo esc_attr( $sel_section ); ?>" data-date="<?php echo esc_attr( $sel_date ); ?>">✅ <?php esc_html_e( 'Mark All Present', 'school-softwere' ); ?></button>
				<?php endif; ?>
			</form>
		</div>

		<?php if ( $sel_class && $sel_section ) :
			$students = $wpdb->get_results( $wpdb->prepare(
				"SELECT sr.ID, sr.first_name, sr.last_name, sr.roll_number, sr.photo, a.status, a.note
				 FROM {$wpdb->prefix}ss_student_records sr
				 LEFT JOIN {$wpdb->prefix}ss_attendance a ON a.student_record_id=sr.ID AND a.date=%s
				 WHERE sr.class_school_id=%d AND sr.section_id=%d AND sr.is_active=1 ORDER BY sr.roll_number, sr.first_name",
				$sel_date, $sel_class, $sel_section
			) );
		?>
		<form id="ss-attendance-form">
			<div class="ss-table-wrap">
				<table class="ss-table">
					<thead><tr><th>#</th><th><?php esc_html_e( 'Photo', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Student', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Roll No', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Status', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Note', 'school-softwere' ); ?></th></tr></thead>
					<tbody>
					<?php $n=1; foreach ( $students as $st ) : $status = $st->status ?: 'present'; ?>
					<tr>
						<td><?php echo esc_html( $n++ ); ?></td>
						<td><img src="<?php echo $st->photo ? esc_url( $st->photo ) : esc_url( SS_PLUGIN_URL . 'assets/images/default-avatar.png' ); ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;" alt=""></td>
						<td><?php echo esc_html( $st->first_name . ' ' . $st->last_name ); ?></td>
						<td><?php echo esc_html( $st->roll_number ?: '—' ); ?></td>
						<td>
							<input type="hidden" name="att[<?php echo esc_attr( $st->ID ); ?>][student_record_id]" value="<?php echo esc_attr( $st->ID ); ?>">
							<input type="hidden" name="att[<?php echo esc_attr( $st->ID ); ?>][class_school_id]" value="<?php echo esc_attr( $sel_class ); ?>">
							<input type="hidden" name="att[<?php echo esc_attr( $st->ID ); ?>][section_id]" value="<?php echo esc_attr( $sel_section ); ?>">
							<select name="att[<?php echo esc_attr( $st->ID ); ?>][status]" class="ss-att-status" style="padding:6px 10px;border:1.5px solid var(--ss-border);border-radius:6px;font-size:12px;">
								<?php foreach ( array( 'present' => '✅ Present', 'absent' => '❌ Absent', 'late' => '🕐 Late', 'half_day' => '½ Half Day' ) as $val => $lbl ) : ?>
								<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $status, $val ); ?>><?php echo esc_html( $lbl ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
						<td><input type="text" name="att[<?php echo esc_attr( $st->ID ); ?>][note]" value="<?php echo esc_attr( $st->note ?: '' ); ?>" placeholder="<?php esc_attr_e( 'Note (optional)', 'school-softwere' ); ?>" style="width:140px;padding:6px;border:1.5px solid var(--ss-border);border-radius:6px;font-size:12px;"></td>
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<div style="display:flex;justify-content:flex-end;margin-top:16px;gap:12px;">
				<button type="button" class="ss-btn ss-btn-primary" id="ss-save-attendance" data-date="<?php echo esc_attr( $sel_date ); ?>" data-class="<?php echo esc_attr( $sel_class ); ?>" data-section="<?php echo esc_attr( $sel_section ); ?>">💾 <?php esc_html_e( 'Save Attendance', 'school-softwere' ); ?></button>
			</div>
		</form>
		<?php else : ?>
		<div class="ss-notice ss-notice-info">📋 <?php esc_html_e( 'Please select a class, section, and date to mark attendance.', 'school-softwere' ); ?></div>
		<?php endif; ?>
	</div>
</div>

<script>
jQuery(function($){
	$('#ss-save-attendance').on('click',function(){
		var records=[],date=$(this).data('date'),csid=$(this).data('class'),secid=$(this).data('section');
		$('#ss-attendance-form tbody tr').each(function(){
			var id = $(this).find('[name*="[student_record_id]"]').val();
			var status = $(this).find('.ss-att-status').val();
			var note = $(this).find('input[name*="[note]"]').val();
			if(id) records.push({student_record_id:id,class_school_id:csid,section_id:secid,status:status,note:note});
		});
		SS_UI.showLoader();
		$.post(SS.ajax_url,{action:'ss_save_attendance',attendance:records,date:date,nonce:SS.nonce},function(res){
			SS_UI.hideLoader();
			SS_UI.toast(res.success?res.message:SS.i18n.error,res.success?'success':'error');
		});
	});
	$('#ss-mark-all-present').on('click',function(){
		SS_UI.showLoader();
		$.post(SS.ajax_url,{action:'ss_bulk_attendance',class_school_id:$(this).data('class'),section_id:$(this).data('section'),date:$(this).data('date'),status:'present',nonce:SS.nonce},function(res){
			SS_UI.hideLoader();
			SS_UI.toast(res.success?res.message:SS.i18n.error,res.success?'success':'error');
			if(res.success) setTimeout(()=>location.reload(),900);
		});
	});
});
</script>
