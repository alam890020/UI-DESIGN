<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$name = trim( ( $student->first_name ?? '' ) . ' ' . ( $student->last_name ?? '' ) );
?>
<div class="sms-cert">
    <div class="sms-cert-border" style="--sms-print:<?php echo esc_attr( $theme_color ); ?>">
        <div class="sms-cert-inner">
            <h1>CERTIFICATE</h1>
            <p style="color:#5b6175;font-style:italic">This is to certify that</p>
            <h2><?php echo esc_html( $name ); ?></h2>
            <p>has been a student of <strong><?php echo esc_html( $school_name ); ?></strong>
                with admission number <strong><?php echo esc_html( $student->admission_no ?? '' ); ?></strong>,
                and has demonstrated outstanding conduct and academic performance.
            </p>
            <p style="margin-top:24px;font-size:12px;color:#5b6175">Issued on: <?php echo esc_html( date( 'd F Y' ) ); ?></p>
            <div class="signs">
                <div>______________________<br><small>Class Teacher</small></div>
                <div>______________________<br><small>Principal</small></div>
            </div>
        </div>
    </div>
</div>
