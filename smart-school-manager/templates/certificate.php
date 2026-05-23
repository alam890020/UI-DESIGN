<?php
/**
 * Print template: Generic Certificate.
 *
 * @package SmartSchoolManager
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$name = trim( ( $student->first_name ?? '' ) . ' ' . ( $student->last_name ?? '' ) );
?>
<div class="ssm-cert">
    <div class="ssm-cert-border" style="--ssm-print-color:<?php echo esc_attr( $theme_color ); ?>">
        <div class="ssm-cert-inner">
            <h1>CERTIFICATE</h1>
            <p class="sub">This is to certify that</p>
            <h2><?php echo esc_html( $name ); ?></h2>
            <p>has been a student of <strong><?php echo esc_html( $school_name ); ?></strong>
                with admission number <strong><?php echo esc_html( $student->admission_no ?? '' ); ?></strong>,
                and has demonstrated outstanding conduct and academic performance.
            </p>
            <p class="date">Issued on: <?php echo esc_html( date( 'd F Y' ) ); ?></p>
            <div class="signs">
                <div>______________________<br><small>Class Teacher</small></div>
                <div>______________________<br><small>Principal</small></div>
            </div>
        </div>
    </div>
</div>
