<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Academic Report', 'Comprehensive performance report cards', 'dashicons-chart-pie' ); ?>

    <div class="ssm-grid-3">
        <?php
        $reports = array(
            array('Term Report', 'dashicons-clipboard', 'Marks per subject for a term'),
            array('Annual Report', 'dashicons-awards', 'Full-year performance summary'),
            array('Subject-wise', 'dashicons-book', 'Performance by subject'),
            array('Class Average', 'dashicons-chart-bar', 'Per-class averages and trends'),
            array('Top Performers', 'dashicons-star-filled', 'Class & school toppers'),
            array('Custom Report', 'dashicons-edit', 'Build your own combination'),
        );
        foreach ( $reports as $r ) : ?>
            <div class="ssm-card" style="text-align:center;padding:26px">
                <div class="ssm-empty-icon" style="margin-bottom:12px"><span class="dashicons <?php echo esc_attr( $r[1] ); ?>"></span></div>
                <h3 style="margin:0 0 6px"><?php echo esc_html( $r[0] ); ?></h3>
                <p class="ssm-muted" style="margin:0 0 14px;font-size:12px"><?php echo esc_html( $r[2] ); ?></p>
                <button class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-printer"></span> Generate</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>
