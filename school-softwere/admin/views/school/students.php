<?php defined( 'ABSPATH' ) || exit;
global $wpdb;
$school_id  = GMA_Helper::get_current_school_id();
$session_id = GMA_Helper::get_active_session_id( $school_id );
$filter_class   = isset( $_GET['class_school_id'] ) ? (int) $_GET['class_school_id'] : 0;
$filter_section = isset( $_GET['section_id'] )      ? (int) $_GET['section_id']      : 0;
$filter_gender  = isset( $_GET['gender'] )          ? sanitize_text_field( $_GET['gender'] ) : '';
$filter_active  = isset( $_GET['is_active'] )       ? (int) $_GET['is_active'] : 1;
$search         = isset( $_GET['s'] )               ? sanitize_text_field( $_GET['s'] ) : '';
$per_page       = 20;
$current_page   = max( 1, isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1 );

$where = array( 'sr.school_id = ' . (int) $school_id, 'sr.is_active = ' . (int) $filter_active );
$args  = array();
if ( $filter_class )   { $where[] = 'sr.class_school_id = %d'; $args[] = $filter_class; }
if ( $filter_section ) { $where[] = 'sr.section_id = %d';      $args[] = $filter_section; }
if ( $filter_gender )  { $where[] = 'sr.gender = %s';          $args[] = $filter_gender; }
if ( $search )         { $where[] = '(sr.first_name LIKE %s OR sr.last_name LIKE %s OR sr.admission_number LIKE %s)'; $args[] = "%$search%"; $args[] = "%$search%"; $args[] = "%$search%"; }

$where_sql  = 'WHERE ' . implode( ' AND ', $where );
$base       = "SELECT sr.*, c.label AS class_label, sec.label AS section_label FROM " . GMA_TABLE_STUDENT_RECORDS . " sr LEFT JOIN " . GMA_TABLE_CLASS_SCHOOL . " cs ON sr.class_school_id=cs.ID LEFT JOIN " . GMA_TABLE_CLASSES . " c ON cs.class_id=c.ID LEFT JOIN " . GMA_TABLE_SECTIONS . " sec ON sr.section_id=sec.ID $where_sql ORDER BY sr.created_at DESC";
$full_query = $args ? $wpdb->prepare( $base, $args ) : $base; // phpcs:ignore
$total      = (int) $wpdb->get_var( preg_replace( '/^SELECT .+ FROM/iU', 'SELECT COUNT(*) FROM', $full_query ) );
$offset     = ( $current_page - 1 ) * $per_page;
$students   = $wpdb->get_results( $full_query . " LIMIT $per_page OFFSET $offset" ); // phpcs:ignore
$classes    = $wpdb->get_results( $wpdb->prepare( "SELECT cs.ID, c.label FROM " . GMA_TABLE_CLASS_SCHOOL . " cs INNER JOIN " . GMA_TABLE_CLASSES . " c ON cs.class_id=c.ID WHERE cs.school_id=%d AND cs.session_id=%d ORDER BY c.label", $school_id, $session_id ) );
?>
<div class="gma-wrap">
  <?php include GMA_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
  <div class="gma-page-content">
    <div class="gma-page-title-row">
      <div><h1 class="gma-page-title">🎓 Students</h1><p class="gma-breadcrumb"><a href="<?php echo esc_url(admin_url('admin.php?page=gma-school')); ?>">Dashboard</a> › Students</p></div>
      <div style="display:flex;gap:8px;">
        <a href="<?php echo esc_url(admin_url('admin.php?page=gma-students&export=excel')); ?>" class="gma-btn gma-btn-secondary">📊 Export</a>
        <button class="gma-btn gma-btn-primary" data-modal="gma-student-modal" id="gma-add-student-btn">➕ Add Student</button>
      </div>
    </div>

    <!-- Filters -->
    <div class="gma-card" style="margin-bottom:20px;">
      <form method="get" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
        <input type="hidden" name="page" value="gma-students">
        <div class="gma-form-group" style="min-width:160px;"><label>Class</label>
          <select name="class_school_id" class="gma-select2" onchange="this.form.submit()">
            <option value="">All Classes</option>
            <?php foreach($classes as $c): ?><option value="<?php echo esc_attr($c->ID); ?>" <?php selected($filter_class,$c->ID); ?>><?php echo esc_html($c->label); ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="gma-form-group" style="min-width:120px;"><label>Gender</label>
          <select name="gender" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="male" <?php selected($filter_gender,'male'); ?>>Male</option>
            <option value="female" <?php selected($filter_gender,'female'); ?>>Female</option>
          </select>
        </div>
        <div class="gma-form-group" style="min-width:200px;"><label>Search</label>
          <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Name or Admission No…">
        </div>
        <button type="submit" class="gma-btn gma-btn-primary">🔍 Filter</button>
        <a href="<?php echo esc_url(admin_url('admin.php?page=gma-students')); ?>" class="gma-btn gma-btn-secondary">Reset</a>
      </form>
    </div>

    <!-- Bulk + Table -->
    <form id="gma-students-form" method="post">
      <?php wp_nonce_field('gma_nonce','nonce'); ?>
      <div class="gma-table-wrap">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--gma-border);">
          <div style="display:flex;gap:10px;align-items:center;">
            <select name="bulk_action" style="padding:6px 10px;border:1px solid var(--gma-border);border-radius:8px;font-size:13px;">
              <option value="">Bulk Actions</option>
              <option value="gma_bulk_delete_students">Delete Selected</option>
              <option value="gma_bulk_deactivate_students">Deactivate Selected</option>
            </select>
            <button type="button" class="gma-btn gma-btn-secondary gma-btn-sm gma-bulk-action-btn">Apply</button>
          </div>
          <span style="font-size:13px;color:var(--gma-text-muted);"><?php echo esc_html( $total ); ?> students found</span>
        </div>
        <table class="gma-table">
          <thead><tr>
            <th class="gma-col-check"><input type="checkbox" class="gma-check-all"></th>
            <th>#</th><th>Photo</th><th>Name</th><th>Admission No</th>
            <th>Class</th><th>Section</th><th>Gender</th><th>Phone</th><th>Status</th>
            <th class="gma-col-actions">Actions</th>
          </tr></thead>
          <tbody>
          <?php if($students): $n = $offset+1; foreach($students as $s): ?>
          <tr>
            <td><input type="checkbox" name="ids[]" value="<?php echo esc_attr($s->ID); ?>" class="gma-check-row"></td>
            <td><?php echo $n++; ?></td>
            <td><img src="<?php echo $s->photo ? esc_url($s->photo) : esc_url(GMA_PLUGIN_URL.'assets/images/default-avatar.png'); ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid var(--gma-border);" alt=""></td>
            <td><strong><?php echo esc_html($s->first_name.' '.$s->last_name); ?></strong><?php if($s->email): ?><br><small style="color:var(--gma-text-muted);"><?php echo esc_html($s->email); ?></small><?php endif; ?></td>
            <td><code><?php echo esc_html($s->admission_number); ?></code></td>
            <td><?php echo esc_html($s->class_label ?? '—'); ?></td>
            <td><?php echo esc_html($s->section_label ?? '—'); ?></td>
            <td><?php echo esc_html(ucfirst($s->gender ?? '—')); ?></td>
            <td><?php echo esc_html($s->phone ?: '—'); ?></td>
            <td><?php echo wp_kses_post( GMA_Helper::status_badge($s->is_active ? 'active' : 'inactive') ); ?></td>
            <td style="text-align:right;">
              <button type="button" class="gma-btn gma-btn-info gma-btn-sm gma-btn-icon gma-edit-student" data-id="<?php echo esc_attr($s->ID); ?>" title="Edit">✏️</button>
              <a href="<?php echo esc_url(GMA_PLUGIN_URL.'admin/views/print/index.php?type=student_id&id='.$s->ID); ?>" target="_blank" class="gma-btn gma-btn-secondary gma-btn-sm gma-btn-icon" title="ID Card">🪪</a>
              <button type="button" class="gma-btn gma-btn-danger gma-btn-sm gma-btn-icon gma-delete-btn" data-action="gma_delete_student" data-id="<?php echo esc_attr($s->ID); ?>" title="Delete">🗑️</button>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="11" style="text-align:center;padding:32px;color:var(--gma-text-muted);">No students found.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
        <?php echo GMA_Helper::paginate($total, $per_page, $current_page, admin_url('admin.php?page=gma-students')); ?>
      </div>
    </form>
  </div>
</div>

<!-- Student Modal -->
<div id="gma-student-modal" class="gma-modal-overlay">
  <div class="gma-modal" style="max-width:820px;">
    <div class="gma-modal-header">
      <h3 class="gma-modal-title" id="gma-student-modal-title">➕ Add Student</h3>
      <button class="gma-modal-close">✕</button>
    </div>
    <div class="gma-modal-body">
      <div class="gma-tabs-container">
        <div class="gma-tabs">
          <button class="gma-tab-btn active" data-tab="tab-personal">👤 Personal</button>
          <button class="gma-tab-btn" data-tab="tab-guardian">👨‍👩‍👧 Guardian</button>
          <button class="gma-tab-btn" data-tab="tab-academic">📚 Academic</button>
        </div>
        <form id="gma-student-form" class="gma-ajax-form" data-reload="1" data-close-modal="1">
          <input type="hidden" name="action" value="gma_save_student">
          <input type="hidden" name="id" id="gma-student-id" value="">
          <input type="hidden" name="school_id" value="<?php echo esc_attr($school_id); ?>">

          <div id="tab-personal" class="gma-tab-pane active" style="padding-top:16px;">
            <div class="gma-form-row">
              <div class="gma-form-group"><label>First Name <span class="req">*</span></label><input type="text" name="first_name" required></div>
              <div class="gma-form-group"><label>Last Name</label><input type="text" name="last_name"></div>
            </div>
            <div class="gma-form-row">
              <div class="gma-form-group"><label>Date of Birth</label><input type="text" name="dob" class="gma-datepicker"></div>
              <div class="gma-form-group"><label>Gender</label><select name="gender"><option value="">— Select —</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
              <div class="gma-form-group"><label>Blood Group</label><select name="blood_group"><option value="">— Select —</option><?php foreach(GMA_Helper::blood_groups() as $bg): ?><option value="<?php echo esc_attr($bg); ?>"><?php echo esc_html($bg); ?></option><?php endforeach; ?></select></div>
            </div>
            <div class="gma-form-row">
              <div class="gma-form-group"><label>Phone</label><input type="tel" name="phone"></div>
              <div class="gma-form-group"><label>Email</label><input type="email" name="email"></div>
            </div>
            <div class="gma-form-row">
              <div class="gma-form-group" style="grid-column:1/-1;"><label>Address</label><textarea name="address" rows="2"></textarea></div>
            </div>
            <div class="gma-form-group">
              <label>Religion</label><input type="text" name="religion">
            </div>
            <div class="gma-form-group" style="margin-top:12px;">
              <label>Photo</label>
              <div class="gma-photo-upload">
                <img src="<?php echo esc_url(GMA_PLUGIN_URL.'assets/images/default-avatar.png'); ?>" class="gma-photo-preview" id="gma-student-photo-preview" alt="">
                <label class="gma-btn gma-btn-secondary gma-btn-sm">📷 Upload<input type="file" name="photo" class="gma-photo-input" accept="image/*"></label>
              </div>
            </div>
          </div>

          <div id="tab-guardian" class="gma-tab-pane" style="padding-top:16px;">
            <div class="gma-form-row">
              <div class="gma-form-group"><label>Father Name</label><input type="text" name="father_name"></div>
              <div class="gma-form-group"><label>Mother Name</label><input type="text" name="mother_name"></div>
            </div>
            <div class="gma-form-row">
              <div class="gma-form-group"><label>Guardian Name</label><input type="text" name="guardian_name"></div>
              <div class="gma-form-group"><label>Relation</label><input type="text" name="guardian_relation"></div>
            </div>
          </div>

          <div id="tab-academic" class="gma-tab-pane" style="padding-top:16px;">
            <div class="gma-form-row">
              <div class="gma-form-group"><label>Class <span class="req">*</span></label>
                <select name="class_school_id" required class="gma-select2">
                  <option value="">— Select Class —</option>
                  <?php foreach($classes as $c): ?><option value="<?php echo esc_attr($c->ID); ?>"><?php echo esc_html($c->label); ?></option><?php endforeach; ?>
                </select>
              </div>
              <div class="gma-form-group"><label>Section <span class="req">*</span></label>
                <select name="section_id" required class="gma-select2"><option value="">— Select Section —</option></select>
              </div>
            </div>
            <div class="gma-form-row">
              <div class="gma-form-group"><label>Admission Date</label><input type="text" name="admission_date" class="gma-datepicker"></div>
              <div class="gma-form-group"><label>Roll Number</label><input type="text" name="roll_number"></div>
            </div>
          </div>

          <div class="gma-modal-footer">
            <button type="button" class="gma-btn gma-btn-secondary gma-modal-close">Cancel</button>
            <button type="submit" class="gma-btn gma-btn-primary">💾 Save Student</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
jQuery(function($){
  $(document).on('change','[name="class_school_id"]',function(){
    var csid=$(this).val();
    var $sec=$(this).closest('form').find('[name="section_id"]');
    $sec.html('<option>Loading…</option>');
    GMA_Ajax.post('gma_get_sections',{class_school_id:csid},function(data){
      $sec.html('<option value="">— Select Section —</option>');
      $.each(data,function(i,s){ $sec.append('<option value="'+s.ID+'">'+s.label+'</option>'); });
      if($.fn.select2) $sec.trigger('change');
    });
  });
  $(document).on('click','.gma-edit-student',function(){
    var id=$(this).data('id');
    GMA_Ajax.post('gma_get_student',{id:id},function(s){
      var $f=$('#gma-student-form');
      GMA_UI.resetForm($f);
      $.each(s,function(k,v){ $f.find('[name="'+k+'"]').val(v); });
      $('#gma-student-id').val(s.ID);
      $('#gma-student-modal-title').text('✏️ Edit Student');
      if(s.photo) $('#gma-student-photo-preview').attr('src',s.photo);
      GMA_UI.openModal('gma-student-modal');
    });
  });
  $('#gma-add-student-btn').on('click',function(){
    GMA_UI.resetForm($('#gma-student-form'));
    $('#gma-student-id').val('');
    $('#gma-student-modal-title').text('➕ Add Student');
  });
});
</script>
