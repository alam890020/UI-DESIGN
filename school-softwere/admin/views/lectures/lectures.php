<?php defined( 'ABSPATH' ) || exit;
global $wpdb;
$school_id  = GMA_Helper::get_current_school_id();
$chapter_id = isset($_GET['chapter_id']) ? (int)$_GET['chapter_id'] : 0;
$chapter    = $chapter_id ? $wpdb->get_row( $wpdb->prepare( "SELECT ch.*, sub.label AS subject_label, c.label AS class_label FROM " . GMA_TABLE_CHAPTERS . " ch INNER JOIN " . GMA_TABLE_SUBJECTS . " sub ON ch.subject_id=sub.ID INNER JOIN " . GMA_TABLE_CLASS_SCHOOL . " cs ON ch.class_school_id=cs.ID INNER JOIN " . GMA_TABLE_CLASSES . " c ON cs.class_id=c.ID WHERE ch.ID=%d", $chapter_id ) ) : null;
$lectures   = $chapter_id ? $wpdb->get_results( $wpdb->prepare( "SELECT l.*, CONCAT(s.first_name,' ',s.last_name) AS staff_name FROM " . GMA_TABLE_LECTURES . " l LEFT JOIN " . GMA_TABLE_STAFF . " s ON l.staff_id=s.ID WHERE l.chapter_id=%d ORDER BY l.created_at", $chapter_id ) ) : array();
$staff_list = $wpdb->get_results( $wpdb->prepare( "SELECT ID, CONCAT(first_name,' ',last_name) AS name FROM " . GMA_TABLE_STAFF . " WHERE school_id=%d AND is_active=1 ORDER BY first_name", $school_id ) );
?>
<div class="gma-wrap">
  <?php include GMA_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
  <div class="gma-page-content">
    <div class="gma-page-title-row">
      <div>
        <h1 class="gma-page-title">🎥 Lectures<?php if($chapter): ?> — <?php echo esc_html($chapter->class_label.' › '.$chapter->subject_label.' › '.$chapter->label); ?><?php endif; ?></h1>
        <p class="gma-breadcrumb"><a href="<?php echo esc_url(admin_url('admin.php?page=gma-school')); ?>">Dashboard</a> › <a href="<?php echo esc_url(admin_url('admin.php?page=gma-chapters')); ?>">Chapters</a> › Lectures</p>
      </div>
      <?php if($chapter): ?>
      <button class="gma-btn gma-btn-primary" data-modal="gma-lecture-modal">➕ Add Lecture</button>
      <?php endif; ?>
    </div>
    <?php if(!$chapter): ?>
    <div class="gma-notice gma-notice-info">Please select a chapter from the <a href="<?php echo esc_url(admin_url('admin.php?page=gma-chapters')); ?>">Chapters page</a> to manage lectures.</div>
    <?php else: ?>
    <div class="gma-table-wrap">
      <table class="gma-table">
        <thead><tr><th>#</th><th>Title</th><th>Staff</th><th>Video</th><th>Attachment</th><th>Date</th><th class="gma-col-actions">Actions</th></tr></thead>
        <tbody>
        <?php if($lectures): $n=1; foreach($lectures as $l): ?>
        <tr>
          <td><?php echo $n++; ?></td>
          <td><strong><?php echo esc_html($l->title); ?></strong><?php if($l->description): ?><br><small style="color:var(--gma-text-muted);"><?php echo esc_html(wp_trim_words($l->description,8,'…')); ?></small><?php endif; ?></td>
          <td><?php echo esc_html($l->staff_name ?: '—'); ?></td>
          <td><?php if($l->video_url): ?><a href="<?php echo esc_url($l->video_url); ?>" target="_blank" class="gma-btn gma-btn-info gma-btn-sm">▶ Watch</a><?php else: ?>—<?php endif; ?></td>
          <td><?php if($l->attachment): ?><a href="<?php echo esc_url($l->attachment); ?>" target="_blank" class="gma-btn gma-btn-secondary gma-btn-sm">📎 File</a><?php else: ?>—<?php endif; ?></td>
          <td style="font-size:12px;"><?php echo esc_html(GMA_Helper::format_date($l->created_at)); ?></td>
          <td style="text-align:right;">
            <button class="gma-btn gma-btn-info gma-btn-sm gma-btn-icon gma-edit-lecture" data-id="<?php echo esc_attr($l->ID); ?>">✏️</button>
            <button class="gma-btn gma-btn-danger gma-btn-sm gma-btn-icon gma-delete-btn" data-action="gma_delete_lecture" data-id="<?php echo esc_attr($l->ID); ?>">🗑️</button>
          </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--gma-text-muted);">No lectures yet for this chapter.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
<!-- Lecture Modal -->
<div id="gma-lecture-modal" class="gma-modal-overlay">
  <div class="gma-modal" style="max-width:600px;">
    <div class="gma-modal-header"><h3 class="gma-modal-title" id="gma-lecture-modal-title">➕ Add Lecture</h3><button class="gma-modal-close">✕</button></div>
    <div class="gma-modal-body">
      <form id="gma-lecture-form" class="gma-ajax-form" data-reload="1" data-close-modal="1">
        <input type="hidden" name="action" value="gma_save_lecture">
        <input type="hidden" name="id" id="gma-lecture-id" value="">
        <input type="hidden" name="chapter_id" value="<?php echo esc_attr($chapter_id); ?>">
        <div class="gma-form-row">
          <div class="gma-form-group" style="grid-column:1/-1;"><label>Title <span class="req">*</span></label><input type="text" name="title" required></div>
        </div>
        <div class="gma-form-row">
          <div class="gma-form-group"><label>Assigned Staff</label>
            <select name="staff_id" class="gma-select2">
              <option value="">— None —</option>
              <?php foreach($staff_list as $st): ?><option value="<?php echo esc_attr($st->ID); ?>"><?php echo esc_html($st->name); ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="gma-form-group"><label>Video URL (YouTube/Drive)</label><input type="url" name="video_url" placeholder="https://…"></div>
        </div>
        <div class="gma-form-group"><label>Description</label><textarea name="description" rows="3"></textarea></div>
        <div class="gma-form-group"><label>Attachment / PDF</label><input type="file" name="attachment" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip"></div>
        <div class="gma-modal-footer" style="padding:0;margin-top:16px;">
          <button type="button" class="gma-btn gma-btn-secondary gma-modal-close">Cancel</button>
          <button type="submit" class="gma-btn gma-btn-primary">💾 Save Lecture</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
jQuery(function($){
  $(document).on('click','.gma-edit-lecture',function(){
    var id=$(this).data('id');
    GMA_Ajax.post('gma_get_lecture',{id:id},function(l){
      var $f=$('#gma-lecture-form');
      GMA_UI.resetForm($f);
      $.each(l,function(k,v){ $f.find('[name="'+k+'"]').val(v); });
      $('#gma-lecture-id').val(l.ID);
      $('#gma-lecture-modal-title').text('✏️ Edit Lecture');
      GMA_UI.openModal('gma-lecture-modal');
    });
  });
});
</script>
