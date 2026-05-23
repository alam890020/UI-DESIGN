<?php
/**
 * Shared admin header partial.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

global $wpdb;
$school_id   = SS_Helper::get_current_school_id();
$school      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_schools WHERE ID=%d", $school_id ) );
$all_schools = current_user_can( 'manage_options' ) ? $wpdb->get_results( "SELECT ID, label FROM {$wpdb->prefix}ss_schools ORDER BY label" ) : array();
$notices_count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}ss_notices WHERE school_id=%d AND date >= %s", $school_id, date( 'Y-m-01' ) ) );
?>
<div class="ss-header">
	<div class="ss-header-left">
		<h1 class="ss-header-title" style="font-size:16px;">🎓 <span style="background:linear-gradient(135deg,var(--ss-primary),var(--ss-secondary));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">School Softwere</span></h1>
		<?php if ( count( $all_schools ) > 1 ) : ?>
		<select id="ss-school-switcher" class="ss-school-pill" style="cursor:pointer;background:var(--ss-surface-2);border:1px solid var(--ss-border);border-radius:20px;padding:4px 14px;font-size:13px;color:var(--ss-primary);">
			<?php foreach ( $all_schools as $s ) : ?>
			<option value="<?php echo esc_attr( $s->ID ); ?>" <?php selected( $school_id, $s->ID ); ?>><?php echo esc_html( $s->label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php else : ?>
		<span class="ss-school-pill"><?php echo esc_html( $school ? $school->label : __( 'No School', 'school-softwere' ) ); ?></span>
		<?php endif; ?>
		<span class="ss-session-pill">📅 <?php echo esc_html( date_i18n( 'Y' ) ); ?></span>
	</div>
	<div class="ss-header-right">
		<button class="ss-notif-btn" title="<?php esc_attr_e( 'Notices', 'school-softwere' ); ?>">
			🔔 <?php if ( $notices_count ) : ?><span class="ss-notif-badge"><?php echo esc_html( $notices_count ); ?></span><?php endif; ?>
		</button>
		<div class="ss-user-info">
			<img src="<?php echo esc_url( get_avatar_url( get_current_user_id(), array( 'size' => 36 ) ) ); ?>" class="ss-avatar" alt="">
			<div>
				<div class="ss-user-name"><?php echo esc_html( wp_get_current_user()->display_name ); ?></div>
				<span class="ss-role-badge"><?php echo current_user_can( 'manage_options' ) ? esc_html__( 'Super Admin', 'school-softwere' ) : esc_html__( 'School Admin', 'school-softwere' ); ?></span>
			</div>
		</div>
	</div>
</div>
