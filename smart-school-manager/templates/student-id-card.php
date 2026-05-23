<?php
/**
 * Print template: Student ID Card.
 *
 * @package SmartSchoolManager
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$name = trim( ( $student->first_name ?? '' ) . ' ' . ( $student->last_name ?? '' ) );
?>
<div class="ssm-card-print">
    <header style="background:<?php echo esc_attr( $theme_color ); ?>"><?php echo esc_html( $school_name ); ?></header>
    <div class="body">
        <div class="photo">
            <?php if ( ! empty( $student->photo ) ) : ?>
                <img src="<?php echo esc_url( $student->photo ); ?>" alt="">
            <?php else : ?>
                <span><?php echo esc_html( strtoupper( substr( $name, 0, 1 ) ) ); ?></span>
            <?php endif; ?>
        </div>
        <div class="info">
            <h2><?php echo esc_html( $name ); ?></h2>
            <p><strong>Adm No:</strong> <?php echo esc_html( $student->admission_no ?? '' ); ?></p>
            <p><strong>Class:</strong> <?php echo esc_html( $student->section ?? '' ); ?></p>
            <p><strong>DOB:</strong> <?php echo esc_html( $student->dob ?? '' ); ?></p>
            <p><strong>Blood:</strong> <?php echo esc_html( $student->blood_group ?? '' ); ?></p>
            <p><strong>Phone:</strong> <?php echo esc_html( $student->phone ?? '' ); ?></p>
        </div>
    </div>
    <footer>STUDENT &middot; Valid for current academic session</footer>
</div>
