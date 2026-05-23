<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$name = trim( ( $staff->first_name ?? '' ) . ' ' . ( $staff->last_name ?? '' ) );
?>
<div class="sms-id">
    <header style="background:linear-gradient(135deg,#10b981,#059669)"><?php echo esc_html( $school_name ); ?> · STAFF</header>
    <div class="body">
        <div class="photo"><span><?php echo esc_html( strtoupper( substr( $name, 0, 1 ) ) ); ?></span></div>
        <div class="info">
            <h2><?php echo esc_html( $name ); ?></h2>
            <p><strong>Emp No:</strong> <?php echo esc_html( $staff->employee_no ?? '' ); ?></p>
            <p><strong>Designation:</strong> <?php echo esc_html( $staff->designation ?? '' ); ?></p>
            <p><strong>Phone:</strong> <?php echo esc_html( $staff->phone ?? '' ); ?></p>
            <p><strong>Email:</strong> <?php echo esc_html( $staff->email ?? '' ); ?></p>
        </div>
    </div>
    <footer>STAFF · Valid for current academic session</footer>
</div>
