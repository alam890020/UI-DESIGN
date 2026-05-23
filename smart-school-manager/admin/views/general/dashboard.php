<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$stats = array(
    'students_active' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE status='active'" ),
    'admissions_pending' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}admissions WHERE status='pending'" ),
    'staff' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}staff WHERE status='active'" ),
    'invoices_unpaid' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}invoices WHERE status='unpaid'" ),
);
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'School Admin Dashboard', 'Day-to-day school operations overview', 'dashicons-dashboard' ); ?>

    <div class="ssm-stats-grid">
        <?php
        echo SSM_Helper::stat_card( 'Active Students', number_format( $stats['students_active'] ), 'dashicons-groups', 'indigo' );
        echo SSM_Helper::stat_card( 'Pending Admissions', number_format( $stats['admissions_pending'] ), 'dashicons-welcome-add-page', 'orange' );
        echo SSM_Helper::stat_card( 'Active Staff', number_format( $stats['staff'] ), 'dashicons-businessman', 'cyan' );
        echo SSM_Helper::stat_card( 'Unpaid Invoices', number_format( $stats['invoices_unpaid'] ), 'dashicons-warning', 'red' );
        ?>
    </div>

    <div class="ssm-card">
        <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-admin-tools"></span> School Admin Quick Tools</div></div>
        <div class="ssm-quick-actions">
            <a class="ssm-quick-action" href="<?php echo esc_url( SSM_Helper::admin_url('ssm-students') ); ?>"><span class="dashicons dashicons-groups"></span><span>Students</span></a>
            <a class="ssm-quick-action" href="<?php echo esc_url( SSM_Helper::admin_url('ssm-admissions') ); ?>"><span class="dashicons dashicons-welcome-add-page"></span><span>Admissions</span></a>
            <a class="ssm-quick-action" href="<?php echo esc_url( SSM_Helper::admin_url('ssm-staff') ); ?>"><span class="dashicons dashicons-businessman"></span><span>Staff</span></a>
            <a class="ssm-quick-action" href="<?php echo esc_url( SSM_Helper::admin_url('ssm-id-cards') ); ?>"><span class="dashicons dashicons-id"></span><span>ID Cards</span></a>
            <a class="ssm-quick-action" href="<?php echo esc_url( SSM_Helper::admin_url('ssm-certificates') ); ?>"><span class="dashicons dashicons-awards"></span><span>Certificates</span></a>
            <a class="ssm-quick-action" href="<?php echo esc_url( SSM_Helper::admin_url('ssm-promote') ); ?>"><span class="dashicons dashicons-controls-fastforward"></span><span>Promote</span></a>
            <a class="ssm-quick-action" href="<?php echo esc_url( SSM_Helper::admin_url('ssm-notifications') ); ?>"><span class="dashicons dashicons-email-alt"></span><span>Notify</span></a>
            <a class="ssm-quick-action" href="<?php echo esc_url( SSM_Helper::admin_url('ssm-birthdays') ); ?>"><span class="dashicons dashicons-buddicons-buddypress-logo"></span><span>Birthdays</span></a>
            <a class="ssm-quick-action" href="<?php echo esc_url( SSM_Helper::admin_url('ssm-logs') ); ?>"><span class="dashicons dashicons-list-view"></span><span>Logs</span></a>
        </div>
    </div>
</div>
