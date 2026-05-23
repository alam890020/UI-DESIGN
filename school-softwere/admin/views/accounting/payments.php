<?php defined( 'ABSPATH' ) || exit;
global $wpdb;
$school_id = GMA_Helper::get_current_school_id();
?>
<div class="gma-wrap">
<?php include GMA_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
<div class="gma-page-content">
<div class="gma-page-title-row">
  <div><h1 class="gma-page-title">💳 Payments</h1><p class="gma-breadcrumb"><a href="<?php echo esc_url(admin_url('admin.php?page=gma-school')); ?>">Dashboard</a> › <?php echo esc_html('💳 Payments'); ?></p></div>
  <div style="display:flex;gap:8px;"><button class="gma-btn gma-btn-primary" data-modal="gma-pay-modal">💳 Collect</button></div>
</div>
<div class="gma-card" id="gma-payments-list">
  <p style="color:var(--gma-text-muted);padding:20px;"><?php esc_html_e('Loading 💳 Payments...','gma-school'); ?></p>
</div>
</div>
</div>
