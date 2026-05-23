<?php
/**
 * Staff Management — List View
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

global $wpdb;
$school_id  = SS_Helper::get_current_school_id();
$search     = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
$per_page   = 20;
$current_page = max( 1, isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1 );
$offset     = ( $current_page - 1 ) * $per_page;

$where = array( 'school_id = ' . (int) $school_id );
$args  = array();
if ( $search ) { $where[] = '(first_name LIKE %s OR last_name LIKE %s OR email LIKE %s)'; $args = array( "%$search%", "%$search%", "%$search%" ); }

$where_sql = 'WHERE ' . implode( ' AND ', $where );
$base_q    = "SELECT * FROM {$wpdb->prefix}ss_staff $where_sql ORDER BY created_at DESC";
if ( $args ) $base_q = $wpdb->prepare( $base_q, $args ); // phpcs:ignore

$total  = (int) $wpdb->get_var( preg_replace( '/SELECT .+ FROM/iU', 'SELECT COUNT(*) FROM', $base_q ) );
$staff  = $wpdb->get_results( $base_q . " LIMIT $per_page OFFSET $offset" ); // phpcs:ignore
$pages  = (int) ceil( $total / $per_page );
$roles  = SS_M_Role::get_roles( $school_id );
?>
<div class="ss-wrap">
	<?php include SS_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
	<div class="ss-page-content">
		<div class="ss-page-title-row">
			<div>
				<h1 class="ss-page-title">👩‍🏫 <?php esc_html_e( 'Staff', 'school-softwere' ); ?></h1>
				<p class="ss-breadcrumb"><a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere' ) ); ?>"><?php esc_html_e( 'Dashboard', 'school-softwere' ); ?></a> › <?php esc_html_e( 'Staff', 'school-softwere' ); ?></p>
			</div>
			<div style="display:flex;gap:10px;">
				<a href="#" class="ss-btn ss-btn-secondary" data-modal="ss-role-modal">🔑 <?php esc_html_e( 'Manage Roles', 'school-softwere' ); ?></a>
				<a href="#" class="ss-btn ss-btn-primary" data-modal="ss-staff-modal">➕ <?php esc_html_e( 'Add Staff', 'school-softwere' ); ?></a>
			</div>
		</div>

		<div class="ss-card" style="margin-bottom:20px;">
			<form method="get" style="display:flex;gap:12px;align-items:flex-end;">
				<input type="hidden" name="page" value="school-softwere-staff">
				<div class="ss-form-group" style="flex:1;max-width:280px;">
					<label><?php esc_html_e( 'Search', 'school-softwere' ); ?></label>
					<input type="text" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Name or email…', 'school-softwere' ); ?>">
				</div>
				<button type="submit" class="ss-btn ss-btn-primary">🔍 <?php esc_html_e( 'Search', 'school-softwere' ); ?></button>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere-staff' ) ); ?>" class="ss-btn ss-btn-secondary"><?php esc_html_e( 'Reset', 'school-softwere' ); ?></a>
			</form>
		</div>

		<div class="ss-table-wrap">
			<div style="padding:14px 18px;border-bottom:1px solid var(--ss-border);display:flex;justify-content:space-between;align-items:center;">
				<span style="font-size:13px;font-weight:600;"><?php echo esc_html( sprintf( __( '%d staff members', 'school-softwere' ), $total ) ); ?></span>
			</div>
			<table class="ss-table">
				<thead><tr>
					<th>#</th>
					<th><?php esc_html_e( 'Photo', 'school-softwere' ); ?></th>
					<th><?php esc_html_e( 'Name', 'school-softwere' ); ?></th>
					<th><?php esc_html_e( 'Designation', 'school-softwere' ); ?></th>
					<th><?php esc_html_e( 'Phone', 'school-softwere' ); ?></th>
					<th><?php esc_html_e( 'Email', 'school-softwere' ); ?></th>
					<th><?php esc_html_e( 'Joining Date', 'school-softwere' ); ?></th>
					<th><?php esc_html_e( 'Salary', 'school-softwere' ); ?></th>
					<th><?php esc_html_e( 'Status', 'school-softwere' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'school-softwere' ); ?></th>
				</tr></thead>
				<tbody>
				<?php if ( $staff ) :
					$n = $offset + 1;
					foreach ( $staff as $m ) : ?>
				<tr>
					<td><?php echo esc_html( $n++ ); ?></td>
					<td><img src="<?php echo $m->photo ? esc_url( $m->photo ) : esc_url( SS_PLUGIN_URL . 'assets/images/default-avatar.png' ); ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;" alt=""></td>
					<td><strong><?php echo esc_html( $m->first_name . ' ' . $m->last_name ); ?></strong></td>
					<td><?php echo esc_html( $m->designation ?: '—' ); ?></td>
					<td><?php echo esc_html( $m->phone ?: '—' ); ?></td>
					<td><?php echo esc_html( $m->email ?: '—' ); ?></td>
					<td><?php echo esc_html( SS_Helper::format_date( $m->joining_date ) ); ?></td>
					<td><?php echo esc_html( SS_Helper::format_currency( $m->salary ) ); ?></td>
					<td><?php echo wp_kses_post( SS_Helper::status_badge( $m->is_active ? 'active' : 'inactive' ) ); ?></td>
					<td>
						<button type="button" class="ss-btn ss-btn-info ss-btn-sm ss-btn-icon ss-edit-staff" data-id="<?php echo esc_attr( $m->ID ); ?>" title="Edit">✏️</button>
						<button type="button" class="ss-btn ss-btn-danger ss-btn-sm ss-btn-icon ss-delete-btn" data-action="ss_delete_staff" data-id="<?php echo esc_attr( $m->ID ); ?>" title="Delete">🗑️</button>
					</td>
				</tr>
				<?php endforeach; else : ?>
				<tr><td colspan="10" style="text-align:center;padding:32px;color:var(--ss-text-muted);"><?php esc_html_e( 'No staff found.', 'school-softwere' ); ?></td></tr>
				<?php endif; ?>
				</tbody>
			</table>
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
	</div>
</div>

<!-- Add/Edit Staff Modal -->
<div id="ss-staff-modal" class="ss-modal-overlay">
	<div class="ss-modal" style="max-width:700px;">
		<div class="ss-modal-header">
			<h3 class="ss-modal-title">➕ <?php esc_html_e( 'Add Staff Member', 'school-softwere' ); ?></h3>
			<button class="ss-modal-close">✕</button>
		</div>
		<div class="ss-modal-body">
			<form id="ss-staff-form" class="ss-ajax-form" data-reload="1" data-close-modal="1">
				<input type="hidden" name="action" value="ss_save_staff">
				<input type="hidden" name="id" id="ss-staff-id" value="">
				<input type="hidden" name="school_id" value="<?php echo esc_attr( $school_id ); ?>">
				<div class="ss-form-row">
					<div class="ss-form-group"><label><?php esc_html_e( 'First Name', 'school-softwere' ); ?> <span class="req">*</span></label><input type="text" name="first_name" required><span class="ss-error"></span></div>
					<div class="ss-form-group"><label><?php esc_html_e( 'Last Name', 'school-softwere' ); ?></label><input type="text" name="last_name"></div>
				</div>
				<div class="ss-form-row">
					<div class="ss-form-group"><label><?php esc_html_e( 'Role', 'school-softwere' ); ?></label>
						<select name="role_id" class="ss-select2">
							<option value=""><?php esc_html_e( '— Select Role —', 'school-softwere' ); ?></option>
							<?php foreach ( $roles as $r ) : ?><option value="<?php echo esc_attr( $r->ID ); ?>"><?php echo esc_html( $r->label ); ?></option><?php endforeach; ?>
						</select>
					</div>
					<div class="ss-form-group"><label><?php esc_html_e( 'Designation', 'school-softwere' ); ?></label><input type="text" name="designation"></div>
				</div>
				<div class="ss-form-row">
					<div class="ss-form-group"><label><?php esc_html_e( 'Phone', 'school-softwere' ); ?></label><input type="tel" name="phone"></div>
					<div class="ss-form-group"><label><?php esc_html_e( 'Email', 'school-softwere' ); ?></label><input type="email" name="email"></div>
				</div>
				<div class="ss-form-row">
					<div class="ss-form-group"><label><?php esc_html_e( 'Date of Birth', 'school-softwere' ); ?></label><input type="text" name="dob" class="ss-datepicker"></div>
					<div class="ss-form-group"><label><?php esc_html_e( 'Gender', 'school-softwere' ); ?></label>
						<select name="gender"><option value=""><?php esc_html_e( '— Select —', 'school-softwere' ); ?></option><option value="male"><?php esc_html_e( 'Male', 'school-softwere' ); ?></option><option value="female"><?php esc_html_e( 'Female', 'school-softwere' ); ?></option></select>
					</div>
				</div>
				<div class="ss-form-row">
					<div class="ss-form-group"><label><?php esc_html_e( 'Joining Date', 'school-softwere' ); ?></label><input type="text" name="joining_date" class="ss-datepicker"></div>
					<div class="ss-form-group"><label><?php esc_html_e( 'Monthly Salary', 'school-softwere' ); ?></label><input type="number" name="salary" step="0.01" min="0"></div>
				</div>
				<div class="ss-form-group"><label><?php esc_html_e( 'Address', 'school-softwere' ); ?></label><textarea name="address"></textarea></div>
				<div class="ss-form-group">
					<label><?php esc_html_e( 'Photo', 'school-softwere' ); ?></label>
					<div class="ss-photo-upload">
						<img src="<?php echo esc_url( SS_PLUGIN_URL . 'assets/images/default-avatar.png' ); ?>" class="ss-photo-preview" id="ss-staff-photo-prev" alt="">
						<label class="ss-btn ss-btn-secondary ss-btn-sm ss-photo-upload-btn">📷 <?php esc_html_e( 'Upload', 'school-softwere' ); ?><input type="file" name="photo" class="ss-photo-input" accept="image/*"></label>
					</div>
				</div>
				<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;">
					<button type="button" class="ss-btn ss-btn-secondary ss-modal-close"><?php esc_html_e( 'Cancel', 'school-softwere' ); ?></button>
					<button type="submit" class="ss-btn ss-btn-primary">💾 <?php esc_html_e( 'Save Staff', 'school-softwere' ); ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
jQuery(function($){
	$(document).on('click','.ss-edit-staff',function(){
		var id=$(this).data('id');
		$.post(SS.ajax_url,{action:'ss_get_staff',id:id,nonce:SS.nonce},function(res){
			if(!res.success) return;
			var s=res.data, $f=$('#ss-staff-form');
			$f.find('[name]').each(function(){ var n=$(this).attr('name'); if(s[n]!==undefined)$(this).val(s[n]); });
			$('#ss-staff-id').val(s.ID);
			if(s.photo) $('#ss-staff-photo-prev').attr('src',s.photo);
			SS_UI.openModal('ss-staff-modal');
		});
	});
});
</script>
