<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Transport Reports', 'Routes, vehicles and student usage', 'dashicons-chart-bar' ); ?>
    <div class="ssm-stats-grid">
        <?php
        echo SSM_Helper::stat_card( 'Active Routes', '0', 'dashicons-location-alt', 'indigo' );
        echo SSM_Helper::stat_card( 'Vehicles', '0', 'dashicons-car', 'cyan' );
        echo SSM_Helper::stat_card( 'Students Using', '0', 'dashicons-groups', 'green' );
        echo SSM_Helper::stat_card( 'Monthly Revenue', SSM_Helper::money(0), 'dashicons-money-alt', 'orange' );
        ?>
    </div>
    <div class="ssm-card ssm-empty">
        <div class="ssm-empty-icon"><span class="dashicons dashicons-chart-bar"></span></div>
        <h2>Detailed Transport Analytics</h2>
        <p>Pick a date range and report type to view route utilisation, daily attendance, student-wise pickup time, fuel cost, maintenance schedules and revenue.</p>
        <button class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-printer"></span> Generate Report</button>
    </div>
</div>
