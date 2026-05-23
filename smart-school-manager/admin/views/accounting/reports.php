<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$income = (float) $wpdb->get_var( "SELECT COALESCE(SUM(amount),0) FROM {$p}income" );
$expenses = (float) $wpdb->get_var( "SELECT COALESCE(SUM(amount),0) FROM {$p}expenses" );
$net = $income - $expenses;
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Financial Reports', 'P&L, balance and revenue analytics', 'dashicons-chart-pie' ); ?>
    <div class="ssm-stats-grid">
        <?php
        echo SSM_Helper::stat_card( 'Total Income', SSM_Helper::money( $income ), 'dashicons-chart-line', 'green' );
        echo SSM_Helper::stat_card( 'Total Expense', SSM_Helper::money( $expenses ), 'dashicons-chart-bar', 'red' );
        echo SSM_Helper::stat_card( 'Net', SSM_Helper::money( $net ), 'dashicons-chart-pie', $net >= 0 ? 'indigo' : 'orange' );
        echo SSM_Helper::stat_card( 'Margin', $income ? round( $net / $income * 100, 1 ) . '%' : '0%', 'dashicons-chart-area', 'purple' );
        ?>
    </div>

    <div class="ssm-grid-3">
        <?php $reports = array(
            array('Income vs Expense','dashicons-chart-area','Compare across periods'),
            array('Outstanding Fees','dashicons-warning','Receivables aging'),
            array('Daily Collection','dashicons-clock','Cash collection report'),
            array('Category Breakdown','dashicons-category','Income/Expense by category'),
            array('Class-wise Revenue','dashicons-welcome-write-blog','Revenue per class'),
            array('Tax Report','dashicons-shield','For accounting filings'),
        );
        foreach ( $reports as $r ) : ?>
            <div class="ssm-card" style="text-align:center;padding:26px">
                <div class="ssm-empty-icon" style="margin-bottom:12px"><span class="dashicons <?php echo esc_attr( $r[1] ); ?>"></span></div>
                <h3 style="margin:0 0 6px"><?php echo esc_html( $r[0] ); ?></h3>
                <p class="ssm-muted" style="margin:0 0 14px;font-size:12px"><?php echo esc_html( $r[2] ); ?></p>
                <button class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-printer"></span> View</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>
