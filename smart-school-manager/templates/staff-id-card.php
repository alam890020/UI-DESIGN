<?php
/**
 * Print template: Staff ID Card.
 *
 * @package SmartSchoolManager
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$name = trim( ( $staff->first_name ?? '' ) . ' ' . ( $staff->last_name ?? '' ) );
?>
<div class="ssm-card-print">
    <header style="background:<?php echo esc_attr( $theme_color ); ?>"><?php echo esc_html( $school_name ); ?> · STAFF</header>
    <div class="body">
        <div class="photo">
            <?php if ( ! empty( $staff->photo ) ) : ?>
                <img src="<?php echo esc_url( $staff->photo ); ?>" alt="">
            <?php else : ?>
                <span><?php echo esc_html( strtoupper( substr( $name, 0, 1 ) ) ); ?></span>
            <?php endif; ?>
        </div>
        <div class="info">
            <h2><?php echo esc_html( $name ); ?></h2>
            <p><strong>Emp No:</strong> <?php echo esc_html( $staff->employee_no ?? '' ); ?></p>
            <p><strong>Designation:</strong> <?php echo esc_html( $staff->designation ?? '' ); ?></p>
            <p><strong>Phone:</strong> <?php echo esc_html( $staff->phone ?? '' ); ?></p>
            <p><strong>Email:</strong> <?php echo esc_html( $staff->email ?? '' ); ?></p>
        </div>
    </div>
    <footer>STAFF &middot; Valid for current academic session</footer>
</div>
