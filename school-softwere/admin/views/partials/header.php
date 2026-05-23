<?php defined( 'ABSPATH' ) || exit;
$school_id = GMA_Helper::get_current_school_id();
$school    = GMA_Helper::get_school( $school_id );
global $wpdb;
$schools   = $wpdb->get_results( "SELECT ID, label FROM " . GMA_TABLE_SCHOOLS . " WHERE is_active=1 ORDER BY label" );
?>
<div class="gma-header">
  <div class="gma-header-left">
    <h1 class="gma-header-title">🏫 GMA School</h1>
    <select id="gma-school-switcher" title="Switch School">
      <?php foreach ( $schools as $s ) : ?>
        <option value="<?php echo esc_attr( $s->ID ); ?>" <?php selected( $s->ID, $school_id ); ?>><?php echo esc_html( $s->label ); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="gma-header-right">
    <button class="gma-notif-btn">🔔 <span class="gma-notif-badge">0</span></button>
    <div class="gma-user-info">
      <img src="<?php echo esc_url( get_avatar_url( get_current_user_id(), array( 'size' => 36 ) ) ); ?>" class="gma-avatar" alt="">
      <div>
        <div class="gma-user-name"><?php echo esc_html( wp_get_current_user()->display_name ); ?></div>
        <span class="gma-role-badge"><?php esc_html_e( 'Admin', 'gma-school' ); ?></span>
      </div>
    </div>
  </div>
</div>
