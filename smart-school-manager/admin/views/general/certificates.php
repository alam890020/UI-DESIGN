<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Certificates', 'Generate beautiful certificates for students and staff', 'dashicons-awards' ); ?>

    <div class="ssm-grid-3">
        <?php
        $types = array(
            array( 'Character Certificate', 'dashicons-shield', 'For documenting student conduct' ),
            array( 'Bonafide Certificate', 'dashicons-yes-alt', 'Verify enrolled status' ),
            array( 'Merit Certificate', 'dashicons-star-filled', 'Award academic achievement' ),
            array( 'Participation', 'dashicons-flag', 'Event participation proof' ),
            array( 'Leaving Certificate', 'dashicons-controls-skipforward', 'For students leaving school' ),
            array( 'Custom Certificate', 'dashicons-edit', 'Build your own template' ),
        );
        foreach ( $types as $t ) : ?>
            <div class="ssm-card" style="text-align:center;padding:28px">
                <div class="ssm-empty-icon" style="margin-bottom:14px"><span class="dashicons <?php echo esc_attr( $t[1] ); ?>"></span></div>
                <h3 style="margin:0 0 6px"><?php echo esc_html( $t[0] ); ?></h3>
                <p class="ssm-muted" style="font-size:12px;margin:0 0 14px"><?php echo esc_html( $t[2] ); ?></p>
                <button class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-printer"></span> Generate</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>
