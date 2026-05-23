<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT c.*, cat.name AS cat_name FROM {$p}classes c LEFT JOIN {$p}categories cat ON cat.id=c.category_id ORDER BY c.id DESC" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Classes', 'Class listing and management', 'dashicons-welcome-write-blog',
        array( array( 'href' => SSM_Helper::admin_url('ssm-classes-categories'), 'label' => 'Manage Categories', 'icon' => 'dashicons-category', 'class' => 'ssm-btn-ghost' ) ) ); ?>
    <div class="ssm-grid-3">
        <?php $colors = array('indigo','cyan','green','orange','purple','pink','blue','red','teal','amber'); $i=0;
        if ( $rows ) : foreach ( $rows as $c ) : $color = $colors[ $i++ % count($colors) ];
            $count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$p}students WHERE class_id=%d", $c->id ) ); ?>
            <div class="ssm-stat-card ssm-grad-<?php echo esc_attr( $color ); ?>">
                <div class="ssm-stat-icon"><span class="dashicons dashicons-welcome-learn-more"></span></div>
                <div class="ssm-stat-body">
                    <div class="ssm-stat-value"><?php echo esc_html( $c->name . ' ' . $c->section ); ?></div>
                    <div class="ssm-stat-label"><?php echo esc_html( $c->cat_name ?: 'Uncategorized' ); ?></div>
                    <div class="ssm-stat-trend"><?php echo (int) $count; ?> students &middot; cap. <?php echo (int) $c->capacity; ?></div>
                </div>
            </div>
        <?php endforeach; else : ?>
            <div class="ssm-card ssm-empty" style="grid-column:1/-1"><div class="ssm-empty-icon"><span class="dashicons dashicons-welcome-write-blog"></span></div><h2>No classes yet</h2><p>Create classes from Smart School &rarr; Classes & Categories.</p></div>
        <?php endif; ?>
    </div>
</div>
