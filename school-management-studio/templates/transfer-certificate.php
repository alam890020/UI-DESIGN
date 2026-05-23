<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$name = trim( ( $student->first_name ?? '' ) . ' ' . ( $student->last_name ?? '' ) );
?>
<div class="sms-doc">
    <header class="sms-doc-head">
        <div><h1><?php echo esc_html( $school_name ); ?></h1><small>TRANSFER CERTIFICATE</small></div>
        <div class="sms-doc-meta">
            <strong>TC No:</strong> TC-<?php echo esc_html( $student->id ?? '0' ); ?><br>
            <small>Issued: <?php echo esc_html( date( 'd M Y' ) ); ?></small>
        </div>
    </header>
    <table class="sms-doc-table">
        <tbody>
            <tr><th>1.</th><td>Name of Student</td><td><strong><?php echo esc_html( $name ); ?></strong></td></tr>
            <tr><th>2.</th><td>Father's Name</td><td><?php echo esc_html( $student->father_name ?? '' ); ?></td></tr>
            <tr><th>3.</th><td>Mother's Name</td><td><?php echo esc_html( $student->mother_name ?? '' ); ?></td></tr>
            <tr><th>4.</th><td>Date of Birth</td><td><?php echo esc_html( $student->dob ?? '' ); ?></td></tr>
            <tr><th>5.</th><td>Admission No.</td><td><?php echo esc_html( $student->admission_no ?? '' ); ?></td></tr>
            <tr><th>6.</th><td>Date of Admission</td><td><?php echo esc_html( $student->admission_date ?? '' ); ?></td></tr>
            <tr><th>7.</th><td>Class at Time of Leaving</td><td><?php echo esc_html( $student->section ?? '' ); ?></td></tr>
            <tr><th>8.</th><td>Conduct</td><td>Good</td></tr>
            <tr><th>9.</th><td>Reason for Leaving</td><td>On parents' request</td></tr>
            <tr><th>10.</th><td>Date of Issue</td><td><?php echo esc_html( date( 'd F Y' ) ); ?></td></tr>
        </tbody>
    </table>
    <footer class="sms-doc-foot">
        <div class="sign">_________________________<br><small>Class Teacher</small></div>
        <div class="sign">_________________________<br><small>Principal</small></div>
        <div class="sign r">[ Official Seal ]</div>
    </footer>
</div>
