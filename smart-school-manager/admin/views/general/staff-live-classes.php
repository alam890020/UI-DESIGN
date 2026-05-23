<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Staff Live Classes', 'Virtual sessions delivered by staff', 'dashicons-video-alt3',
        array( array( 'href' => SSM_Helper::admin_url('ssm-lectures'), 'label' => 'Manage Lectures', 'icon' => 'dashicons-arrow-right-alt' ) ) ); ?>

    <div class="ssm-stats-grid">
        <?php
        echo SSM_Helper::stat_card( 'Scheduled Today', '0', 'dashicons-clock', 'indigo' );
        echo SSM_Helper::stat_card( 'Live Now', '0', 'dashicons-video-alt3', 'red' );
        echo SSM_Helper::stat_card( 'Recordings', '0', 'dashicons-video-alt', 'green' );
        echo SSM_Helper::stat_card( 'Hosts', '0', 'dashicons-businessman', 'cyan' );
        ?>
    </div>

    <div class="ssm-card ssm-empty">
        <div class="ssm-empty-icon"><span class="dashicons dashicons-video-alt3"></span></div>
        <h2>Schedule a Staff Live Class</h2>
        <p>Use the Lectures module to schedule virtual sessions. Pick a host (staff), audience, time, duration and link.</p>
        <div class="ssm-empty-actions"><a href="<?php echo esc_url( SSM_Helper::admin_url('ssm-lectures') ); ?>" class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-plus"></span> Go to Lectures</a></div>
    </div>
</div>
