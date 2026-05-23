<?php defined( 'ABSPATH' ) || exit;
global $wpdb;
$school_id  = GMA_Helper::get_current_school_id();
$per_page   = 50;
$current    = max(1, isset($_GET['paged']) ? (int)$_GET['paged'] : 1);
$search     = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$where      = "WHERE school_id=" . (int)$school_id;
if($search) $where .= $wpdb->prepare(" AND (action LIKE %s OR details LIKE %s)", "%$search%", "%$search%");
$total  = (int)$wpdb->get_var("SELECT COUNT(*) FROM " . GMA_TABLE_LOGS . " $where");
$offset = ($current-1)*$per_page;
$logs   = $wpdb->get_results("SELECT l.*, u.display_name FROM " . GMA_TABLE_LOGS . " l LEFT JOIN {$wpdb->users} u ON l.user_id=u.ID $where ORDER BY l.created_at DESC LIMIT $per_page OFFSET $offset");
?>
<div class="gma-wrap">
  <?php include GMA_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
  <div class="gma-page-content">
    <div class="gma-page-title-row">
      <div><h1 class="gma-page-title">📋 Activity Logs</h1><p class="gma-breadcrumb"><a href="<?php echo esc_url(admin_url('admin.php?page=gma-school')); ?>">Dashboard</a> › Logs</p></div>
    </div>
    <div class="gma-card" style="margin-bottom:20px;">
      <form method="get" style="display:flex;gap:10px;align-items:flex-end;">
        <input type="hidden" name="page" value="gma-logs">
        <div class="gma-form-group" style="min-width:300px;"><label>Search</label><input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Search action or details…"></div>
        <button type="submit" class="gma-btn gma-btn-primary">🔍 Search</button>
        <a href="<?php echo esc_url(admin_url('admin.php?page=gma-logs')); ?>" class="gma-btn gma-btn-secondary">Reset</a>
      </form>
    </div>
    <div class="gma-table-wrap">
      <table class="gma-table">
        <thead><tr><th>#</th><th>Date & Time</th><th>User</th><th>Action</th><th>Details</th><th>IP</th></tr></thead>
        <tbody>
        <?php if($logs): $n=$offset+1; foreach($logs as $l): ?>
        <tr>
          <td><?php echo $n++; ?></td>
          <td style="white-space:nowrap;font-size:12px;"><?php echo esc_html(GMA_Helper::format_date($l->created_at,'d M Y H:i')); ?></td>
          <td><?php echo esc_html($l->display_name ?: 'System'); ?></td>
          <td><code><?php echo esc_html($l->action); ?></code></td>
          <td><?php echo esc_html($l->details ?: '—'); ?></td>
          <td style="font-size:12px;color:var(--gma-text-muted);"><?php echo esc_html($l->ip ?: '—'); ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--gma-text-muted);">No logs found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
      <?php echo GMA_Helper::paginate($total, $per_page, $current, admin_url('admin.php?page=gma-logs')); ?>
    </div>
  </div>
</div>
