<?php defined( 'ABSPATH' ) || exit;
global $wpdb;
$school_id  = GMA_Helper::get_current_school_id();
$session_id = GMA_Helper::get_active_session_id( $school_id );
$filter_class   = isset($_GET['class_school_id']) ? (int)$_GET['class_school_id'] : 0;
$filter_subject = isset($_GET['subject_id'])      ? (int)$_GET['subject_id']      : 0;
$classes = $wpdb->get_results( $wpdb->prepare( "SELECT cs.ID, c.label FROM " . GMA_TABLE_CLASS_SCHOOL . " cs INNER JOIN " . GMA_TABLE_CLASSES . " c ON cs.class_id=c.ID WHERE cs.school_id=%d AND cs.session_id=%d ORDER BY c.label", $school_id, $session_id ) );
$subjects = $filter_class ? $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_SUBJECTS . " WHERE class_school_id=%d ORDER BY label", $filter_class ) ) : array();
$chapters = $filter_subject ? $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_CHAPTERS . " WHERE subject_id=%d ORDER BY sort_order, label", $filter_subject ) ) : array();
?>
<div class="gma-wrap">
  <?php include GMA_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
  <div class="gma-page-content">
    <div class="gma-page-title-row">
      <div><h1 class="gma-page-title">📑 Chapters</h1><p class="gma-breadcrumb"><a href="<?php echo esc_url(admin_url('admin.php?page=gma-school')); ?>">Dashboard</a> › Chapters</p></div>
      <button class="gma-btn gma-btn-primary" data-modal="gma-chapter-modal">➕ Add Chapter</button>
    </div>
    <div class="gma-card" style="margin-bottom:20px;">
      <form method="get" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
        <input type="hidden" name="page" value="gma-chapters">
        <div class="gma-form-group" style="min-width:180px;"><label>Class</label>
          <select name="class_school_id" class="gma-select2" onchange="this.form.submit()">
            <option value="">— Select Class —</option>
            <?php foreach($classes as $c): ?><option value="<?php echo esc_attr($c->ID); ?>" <?php selected($filter_class,$c->ID); ?>><?php echo esc_html($c->label); ?></option><?php endforeach; ?>
          </select>
        </div>
        <?php if($subjects): ?>
        <div class="gma-form-group" style="min-width:180px;"><label>Subject</label>
          <select name="subject_id" onchange="this.form.submit()">
            <option value="">— Select Subject —</option>
            <?php foreach($subjects as $s): ?><option value="<?php echo esc_attr($s->ID); ?>" <?php selected($filter_subject,$s->ID); ?>><?php echo esc_html($s->label); ?></option><?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>
      </form>
    </div>
    <div class="gma-table-wrap">
      <table class="gma-table">
        <thead><tr><th>#</th><th>Chapter</th><th>Description</th><th>Order</th><th class="gma-col-actions">Actions</th></tr></thead>
        <tbody>
        <?php if($chapters): $n=1; foreach($chapters as $ch): ?>
        <tr>
          <td><?php echo $n++; ?></td>
          <td><strong><?php echo esc_html($ch->label); ?></strong></td>
          <td><?php echo esc_html(wp_trim_words($ch->description,10,'…')); ?></td>
          <td><?php echo esc_html($ch->sort_order); ?></td>
          <td style="text-align:right;">
            <button class="gma-btn gma-btn-info gma-btn-sm gma-btn-icon gma-edit-chapter" data-id="<?php echo esc_attr($ch->ID); ?>">✏️</button>
            <a href="<?php echo esc_url(admin_url('admin.php?page=gma-lectures&chapter_id='.$ch->ID)); ?>" class="gma-btn gma-btn-secondary gma-btn-sm">🎥 Lectures</a>
            <button class="gma-btn gma-btn-danger gma-btn-sm gma-btn-icon gma-delete-btn" data-action="gma_delete_chapter" data-id="<?php echo esc_attr($ch->ID); ?>">🗑️</button>
          </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--gma-text-muted);"><?php echo $filter_subject ? 'No chapters yet.' : 'Select a class and subject to view chapters.'; ?></td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- Chapter Modal -->
<div id="gma-chapter-modal" class="gma-modal-overlay">
  <div class="gma-modal" style="max-width:520px;">
    <div class="gma-modal-header"><h3 class="gma-modal-title" id="gma-chapter-modal-title">➕ Add Chapter</h3><button class="gma-modal-close">✕</button></div>
    <div class="gma-modal-body">
      <form id="gma-chapter-form" class="gma-ajax-form" data-reload="1" data-close-modal="1">
        <input type="hidden" name="action" value="gma_save_chapter">
        <input type="hidden" name="id" id="gma-chapter-id" value="">
        <input type="hidden" name="class_school_id" value="<?php echo esc_attr($filter_class); ?>">
        <input type="hidden" name="subject_id" value="<?php echo esc_attr($filter_subject); ?>">
        <div class="gma-form-row">
          <div class="gma-form-group" style="grid-column:1/-1;"><label>Chapter Title <span class="req">*</span></label><input type="text" name="label" required></div>
        </div>
        <div class="gma-form-row">
          <div class="gma-form-group" style="grid-column:1/-1;"><label>Description</label><textarea name="description" rows="3"></textarea></div>
        </div>
        <div class="gma-form-group"><label>Sort Order</label><input type="number" name="sort_order" value="0" min="0"></div>
        <div class="gma-modal-footer" style="padding:0;margin-top:16px;">
          <button type="button" class="gma-btn gma-btn-secondary gma-modal-close">Cancel</button>
          <button type="submit" class="gma-btn gma-btn-primary">💾 Save Chapter</button>
        </div>
      </form>
    </div>
  </div>
</div>
