<?php
/**
 * Student Management — List View
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

global $wpdb;
$school_id  = SS_Helper::get_current_school_id();
$session_id = SS_Helper::get_active_session_id( $school_id );

// Filters
$filter_class   = isset( $_GET['class_school_id'] ) ? (int) $_GET['class_school_id'] : 0;
$filter_section = isset( $_GET['section_id'] )      ? (int) $_GET['section_id']      : 0;
$filter_gender  = isset( $_GET['gender'] )          ? sanitize_text_field( $_GET['gender'] ) : '';
$filter_active  = isset( $_GET['is_active'] )       ? (int) $_GET['is_active']        : 1;
$search         = isset( $_GET['s'] )               ? sanitize_text_field( $_GET['s'] ) : '';
$per_page       = 20;
$current_page   = max( 1, isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1 );

// Build query
$where    = array( 'sr.school_id = ' . (int) $school_id, 'sr.is_active = ' . (int) $filter_active );
$where_args = array();
if ( $filter_class )   { $where[] = 'sr.class_school_id = %d'; $where_args[] = $filter_class; }
if ( $filter_section ) { $where[] = 'sr.section_id = %d';      $where_args[] = $filter_section; }
if ( $filter_gender )  { $where[] = 'sr.gender = %s';          $where_args[] = $filter_gender; }
if ( $search )         { $where[] = '(sr.first_name LIKE %s OR sr.last_name LIKE %s OR sr.admission_number LIKE %s)'; $where_args[] = "%$search%"; $where_args[] = "%$search%"; $where_args[] = "%$search%"; }

$where_sql = 'WHERE ' . implode( ' AND ', $where );
$base_query = "SELECT sr.*, c.label AS class_label, sec.label AS section_label
               FROM {$wpdb->prefix}ss_student_records sr
               LEFT JOIN {$wpdb->prefix}ss_class_school cs  ON sr.class_school_id = cs.ID
               LEFT JOIN {$wpdb->prefix}ss_classes c        ON cs.class_id = c.ID
               LEFT JOIN {$wpdb->prefix}ss_sections sec     ON sr.section_id = sec.ID
               $where_sql ORDER BY sr.created_at DESC";

if ( $where_args ) {
	$base_query = $wpdb->prepare( $base_query, $where_args ); // phpcs:ignore
}

$total    = (int) $wpdb->get_var( preg_replace( '/SELECT .+ FROM/iU', 'SELECT COUNT(*) FROM', $base_query ) );
$offset   = ( $current_page - 1 ) * $per_page;
$students = $wpdb->get_results( $base_query . " LIMIT $per_page OFFSET $offset" ); // phpcs:ignore
$pages    = (int) ceil( $total / $per_page );

// Dropdowns
$classes  = $wpdb->get_results( $wpdb->prepare( "SELECT cs.ID, c.label FROM {$wpdb->prefix}ss_class_school cs INNER JOIN {$wpdb->prefix}ss_classes c ON cs.class_id=c.ID WHERE cs.school_id=%d AND cs.session_id=%d ORDER BY c.label", $school_id, $session_id ) );
$sections = $filter_class ? $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_sections WHERE class_school_id=%d ORDER BY label", $filter_class ) ) : array();
?>
<div class="ss-wrap">
	<?php include SS_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
	<div class="ss-page-content">
		<div class="ss-page-title-row">
			<div>
				<h1 class="ss-page-title">🎓 <?php esc_html_e( 'Students', 'school-softwere' ); ?></h1>
				<p class="ss-breadcrumb"><a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere' ) ); ?>"><?php esc_html_e( 'Dashboard', 'school-softwere' ); ?></a> › <?php esc_html_e( 'Students', 'school-softwere' ); ?></p>
			</div>
			<div style="display:flex;gap:10px;">
				<a href="#" class="ss-btn ss-btn-secondary" data-modal="ss-import-modal">⬆️ <?php esc_html_e( 'Import', 'school-softwere' ); ?></a>
				<a href="#" class="ss-btn ss-btn-secondary ss-export-btn" data-type="excel">📊 <?php esc_html_e( 'Export', 'school-softwere' ); ?></a>
				<a href="#" class="ss-btn ss-btn-primary" data-modal="ss-student-modal" id="ss-add-student-btn">➕ <?php esc_html_e( 'Add Student', 'school-softwere' ); ?></a>
			</div>
		</div>

		<!-- Filter bar -->
		<div class="ss-card" style="margin-bottom:20px;">
			<form method="get" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
				<input type="hidden" name="page" value="school-softwere-students">
				<div class="ss-form-group" style="min-width:160px;">
					<label><?php esc_html_e( 'Class', 'school-softwere' ); ?></label>
					<select name="class_school_id" class="ss-select2" onchange="this.form.submit()">
						<option value=""><?php esc_html_e( 'All Classes', 'school-softwere' ); ?></option>
						<?php foreach ( $classes as $c ) : ?>
						<option value="<?php echo esc_attr( $c->ID ); ?>" <?php selected( $filter_class, $c->ID ); ?>><?php echo esc_html( $c->label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="ss-form-group" style="min-width:130px;">
					<label><?php esc_html_e( 'Section', 'school-softwere' ); ?></label>
					<select name="section_id" onchange="this.form.submit()">
						<option value=""><?php esc_html_e( 'All Sections', 'school-softwere' ); ?></option>
						<?php foreach ( $sections as $s ) : ?>
						<option value="<?php echo esc_attr( $s->ID ); ?>" <?php selected( $filter_section, $s->ID ); ?>><?php echo esc_html( $s->label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="ss-form-group" style="min-width:120px;">
					<label><?php esc_html_e( 'Gender', 'school-softwere' ); ?></label>
					<select name="gender" onchange="this.form.submit()">
						<option value=""><?php esc_html_e( 'All', 'school-softwere' ); ?></option>
						<option value="male"   <?php selected( $filter_gender, 'male' ); ?>><?php esc_html_e( 'Male', 'school-softwere' ); ?></option>
						<option value="female" <?php selected( $filter_gender, 'female' ); ?>><?php esc_html_e( 'Female', 'school-softwere' ); ?></option>
					</select>
				</div>
				<div class="ss-form-group" style="min-width:200px;">
					<label><?php esc_html_e( 'Search', 'school-softwere' ); ?></label>
					<input type="text" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Name or Admission No...', 'school-softwere' ); ?>">
				</div>
				<button type="submit" class="ss-btn ss-btn-primary">🔍 <?php esc_html_e( 'Filter', 'school-softwere' ); ?></button>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere-students' ) ); ?>" class="ss-btn ss-btn-secondary"><?php esc_html_e( 'Reset', 'school-softwere' ); ?></a>
			</form>
		</div>

		<!-- Bulk action + table -->
		<form id="ss-students-form" method="post">
			<?php wp_nonce_field( 'ss_nonce', 'nonce' ); ?>
			<div class="ss-table-wrap">
				<div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--ss-border);">
					<div style="display:flex;align-items:center;gap:12px;">
						<select name="bulk_action" style="padding:6px 10px;border:1px solid var(--ss-border);border-radius:8px;font-size:13px;">
							<option value=""><?php esc_html_e( 'Bulk Actions', 'school-softwere' ); ?></option>
							<option value="ss_bulk_delete_students"><?php esc_html_e( 'Delete Selected', 'school-softwere' ); ?></option>
							<option value="ss_bulk_deactivate_students"><?php esc_html_e( 'Deactivate Selected', 'school-softwere' ); ?></option>
						</select>
						<button type="button" class="ss-btn ss-btn-secondary ss-btn-sm ss-bulk-action-btn" data-action="bulk_action"><?php esc_html_e( 'Apply', 'school-softwere' ); ?></button>
					</div>
					<span style="font-size:13px;color:var(--ss-text-muted);"><?php echo esc_html( sprintf( __( '%d students found', 'school-softwere' ), $total ) ); ?></span>
				</div>
				<table class="ss-table">
					<thead><tr>
						<th class="ss-col-check"><input type="checkbox" class="ss-check-all"></th>
						<th>#</th>
						<th><?php esc_html_e( 'Photo', 'school-softwere' ); ?></th>
						<th><?php esc_html_e( 'Name', 'school-softwere' ); ?></th>
						<th><?php esc_html_e( 'Admission No', 'school-softwere' ); ?></th>
						<th><?php esc_html_e( 'Class', 'school-softwere' ); ?></th>
						<th><?php esc_html_e( 'Section', 'school-softwere' ); ?></th>
						<th><?php esc_html_e( 'Gender', 'school-softwere' ); ?></th>
						<th><?php esc_html_e( 'Phone', 'school-softwere' ); ?></th>
						<th><?php esc_html_e( 'Status', 'school-softwere' ); ?></th>
						<th class="ss-col-actions"><?php esc_html_e( 'Actions', 'school-softwere' ); ?></th>
					</tr></thead>
					<tbody>
					<?php if ( $students ) :
						$row_num = $offset + 1;
						foreach ( $students as $s ) : ?>
					<tr>
						<td><input type="checkbox" name="student_ids[]" value="<?php echo esc_attr( $s->ID ); ?>" class="ss-check-row"></td>
						<td><?php echo esc_html( $row_num++ ); ?></td>
						<td>
							<img src="<?php echo $s->photo ? esc_url( $s->photo ) : esc_url( SS_PLUGIN_URL . 'assets/images/default-avatar.png' ); ?>"
								 style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid var(--ss-border);" alt="">
						</td>
						<td>
							<strong><?php echo esc_html( $s->first_name . ' ' . $s->last_name ); ?></strong>
							<?php if ( $s->email ) : ?><br><small style="color:var(--ss-text-muted);"><?php echo esc_html( $s->email ); ?></small><?php endif; ?>
						</td>
						<td><code><?php echo esc_html( $s->admission_number ); ?></code></td>
						<td><?php echo esc_html( $s->class_label ?? '—' ); ?></td>
						<td><?php echo esc_html( $s->section_label ?? '—' ); ?></td>
						<td><?php echo esc_html( ucfirst( $s->gender ?? '—' ) ); ?></td>
						<td><?php echo esc_html( $s->phone ?: '—' ); ?></td>
						<td><?php echo wp_kses_post( SS_Helper::status_badge( $s->is_active ? 'active' : 'inactive' ) ); ?></td>
						<td>
							<button type="button" class="ss-btn ss-btn-info ss-btn-sm ss-btn-icon ss-edit-student" data-id="<?php echo esc_attr( $s->ID ); ?>" title="<?php esc_attr_e( 'Edit', 'school-softwere' ); ?>">✏️</button>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere-students&action=view&id=' . $s->ID ) ); ?>" class="ss-btn ss-btn-secondary ss-btn-sm ss-btn-icon" title="<?php esc_attr_e( 'View', 'school-softwere' ); ?>">👁️</a>
							<button type="button" class="ss-btn ss-btn-danger ss-btn-sm ss-btn-icon ss-delete-btn" data-action="ss_delete_student" data-id="<?php echo esc_attr( $s->ID ); ?>" title="<?php esc_attr_e( 'Delete', 'school-softwere' ); ?>">🗑️</button>
						</td>
					</tr>
					<?php endforeach;
					else : ?>
					<tr><td colspan="11" style="text-align:center;padding:32px;color:var(--ss-text-muted);">
						<?php esc_html_e( 'No students found.', 'school-softwere' ); ?>
					</td></tr>
					<?php endif; ?>
					</tbody>
				</table>
				<!-- Pagination -->
				<?php if ( $pages > 1 ) : ?>
				<div class="ss-pagination">
					<div class="ss-pagination-info"><?php echo esc_html( sprintf( __( 'Page %1$d of %2$d', 'school-softwere' ), $current_page, $pages ) ); ?></div>
					<div class="ss-page-links">
						<?php for ( $p = 1; $p <= $pages; $p++ ) : ?>
						<a href="<?php echo esc_url( add_query_arg( 'paged', $p ) ); ?>" class="ss-page-link <?php echo $p === $current_page ? 'active' : ''; ?>"><?php echo esc_html( $p ); ?></a>
						<?php endfor; ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</form>
	</div><!-- .ss-page-content -->
</div><!-- .ss-wrap -->

<!-- Add/Edit Student Modal -->
<div id="ss-student-modal" class="ss-modal-overlay">
	<div class="ss-modal" style="max-width:800px;">
		<div class="ss-modal-header">
			<h3 class="ss-modal-title" id="ss-student-modal-title">➕ <?php esc_html_e( 'Add Student', 'school-softwere' ); ?></h3>
			<button class="ss-modal-close">✕</button>
		</div>
		<div class="ss-modal-body">
			<div class="ss-tabs-container">
				<div class="ss-tabs">
					<button class="ss-tab-btn active" data-tab="tab-personal">👤 <?php esc_html_e( 'Personal', 'school-softwere' ); ?></button>
					<button class="ss-tab-btn" data-tab="tab-guardian">👨‍👩‍👧 <?php esc_html_e( 'Guardian', 'school-softwere' ); ?></button>
					<button class="ss-tab-btn" data-tab="tab-academic">📚 <?php esc_html_e( 'Academic', 'school-softwere' ); ?></button>
				</div>
				<form id="ss-student-form" class="ss-ajax-form" data-reload="1" data-close-modal="1">
					<input type="hidden" name="action" value="ss_save_student">
					<input type="hidden" name="id" id="ss-student-id" value="">
					<input type="hidden" name="school_id" value="<?php echo esc_attr( $school_id ); ?>">

					<!-- Personal Tab -->
					<div id="tab-personal" class="ss-tab-pane active">
						<div class="ss-form-row" style="margin-top:16px;">
							<div class="ss-form-group">
								<label><?php esc_html_e( 'First Name', 'school-softwere' ); ?> <span class="req">*</span></label>
								<input type="text" name="first_name" required>
								<span class="ss-error"></span>
							</div>
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Last Name', 'school-softwere' ); ?></label>
								<input type="text" name="last_name">
							</div>
						</div>
						<div class="ss-form-row">
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Date of Birth', 'school-softwere' ); ?></label>
								<input type="text" name="dob" class="ss-datepicker" placeholder="DD/MM/YYYY">
							</div>
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Gender', 'school-softwere' ); ?></label>
								<select name="gender">
									<option value=""><?php esc_html_e( '— Select —', 'school-softwere' ); ?></option>
									<option value="male"><?php esc_html_e( 'Male', 'school-softwere' ); ?></option>
									<option value="female"><?php esc_html_e( 'Female', 'school-softwere' ); ?></option>
									<option value="other"><?php esc_html_e( 'Other', 'school-softwere' ); ?></option>
								</select>
							</div>
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Blood Group', 'school-softwere' ); ?></label>
								<select name="blood_group">
									<option value=""><?php esc_html_e( '— Select —', 'school-softwere' ); ?></option>
									<?php foreach ( array( 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-' ) as $bg ) : ?>
									<option value="<?php echo esc_attr( $bg ); ?>"><?php echo esc_html( $bg ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="ss-form-row">
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Phone', 'school-softwere' ); ?></label>
								<input type="tel" name="phone">
							</div>
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Email', 'school-softwere' ); ?></label>
								<input type="email" name="email">
							</div>
						</div>
						<div class="ss-form-row">
							<div class="ss-form-group" style="grid-column:1/-1;">
								<label><?php esc_html_e( 'Address', 'school-softwere' ); ?></label>
								<textarea name="address" rows="2"></textarea>
							</div>
						</div>
						<div class="ss-form-row">
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Photo', 'school-softwere' ); ?></label>
								<div class="ss-photo-upload">
									<img src="<?php echo esc_url( SS_PLUGIN_URL . 'assets/images/default-avatar.png' ); ?>" class="ss-photo-preview" id="ss-student-photo-preview" alt="">
									<label class="ss-btn ss-btn-secondary ss-btn-sm ss-photo-upload-btn">
										📷 <?php esc_html_e( 'Upload Photo', 'school-softwere' ); ?>
										<input type="file" name="photo" class="ss-photo-input" accept="image/*">
									</label>
								</div>
							</div>
						</div>
					</div>

					<!-- Guardian Tab -->
					<div id="tab-guardian" class="ss-tab-pane">
						<div class="ss-form-row" style="margin-top:16px;">
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Father Name', 'school-softwere' ); ?></label>
								<input type="text" name="father_name">
							</div>
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Mother Name', 'school-softwere' ); ?></label>
								<input type="text" name="mother_name">
							</div>
						</div>
						<div class="ss-form-row">
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Guardian Name', 'school-softwere' ); ?></label>
								<input type="text" name="guardian_name">
							</div>
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Relation', 'school-softwere' ); ?></label>
								<input type="text" name="guardian_relation">
							</div>
						</div>
					</div>

					<!-- Academic Tab -->
					<div id="tab-academic" class="ss-tab-pane">
						<div class="ss-form-row" style="margin-top:16px;">
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Class', 'school-softwere' ); ?> <span class="req">*</span></label>
								<select name="class_school_id" required class="ss-select2">
									<option value=""><?php esc_html_e( '— Select Class —', 'school-softwere' ); ?></option>
									<?php foreach ( $classes as $c ) : ?>
									<option value="<?php echo esc_attr( $c->ID ); ?>"><?php echo esc_html( $c->label ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Section', 'school-softwere' ); ?> <span class="req">*</span></label>
								<select name="section_id" required class="ss-select2">
									<option value=""><?php esc_html_e( '— Select Section —', 'school-softwere' ); ?></option>
								</select>
							</div>
						</div>
						<div class="ss-form-row">
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Admission Date', 'school-softwere' ); ?></label>
								<input type="text" name="admission_date" class="ss-datepicker">
							</div>
							<div class="ss-form-group">
								<label><?php esc_html_e( 'Roll Number', 'school-softwere' ); ?></label>
								<input type="text" name="roll_number">
							</div>
						</div>
					</div>

					<div class="ss-modal-footer" style="margin-top:20px;padding:0;">
						<button type="button" class="ss-btn ss-btn-secondary ss-modal-close"><?php esc_html_e( 'Cancel', 'school-softwere' ); ?></button>
						<button type="submit" class="ss-btn ss-btn-primary">💾 <?php esc_html_e( 'Save Student', 'school-softwere' ); ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
jQuery(function($){
	// Load sections when class changes
	$(document).on('change','[name="class_school_id"]',function(){
		var csid = $(this).val();
		var $sec = $(this).closest('form').find('[name="section_id"]');
		$sec.html('<option>Loading…</option>');
		$.post(SS.ajax_url,{action:'ss_get_sections',class_school_id:csid,nonce:SS.nonce},function(res){
			$sec.html('<option value=""><?php esc_html_e( "— Select Section —", "school-softwere" ); ?></option>');
			if(res.success && res.data){ $.each(res.data,function(i,s){ $sec.append('<option value="'+s.ID+'">'+s.label+'</option>'); }); }
		});
	});
	// Edit student — populate form
	$(document).on('click','.ss-edit-student',function(){
		var id=$(this).data('id');
		$.post(SS.ajax_url,{action:'ss_get_student',id:id,nonce:SS.nonce},function(res){
			if(!res.success) return;
			var s=res.data;
			var $f=$('#ss-student-form');
			$f.find('[name]').each(function(){ var n=$(this).attr('name'); if(s[n]!==undefined) $(this).val(s[n]); });
			$('#ss-student-id').val(s.ID);
			$('#ss-student-modal-title').text('✏️ <?php esc_html_e( "Edit Student", "school-softwere" ); ?>');
			if(s.photo) $('#ss-student-photo-preview').attr('src',s.photo);
			SS_UI.openModal('ss-student-modal');
		});
	});
});
</script>
