<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$name = trim( ( $student->first_name ?? '' ) . ' ' . ( $student->last_name ?? '' ) );
?>
<div class="sms-doc">
    <header class="sms-doc-head">
        <div><h1><?php echo esc_html( $school_name ); ?></h1><small>ADMIT CARD</small></div>
        <div class="sms-doc-meta">
            <strong><?php echo esc_html( $exam->name ?? 'Examination' ); ?></strong><br>
            <small><?php echo esc_html( $exam->start_date ?? '' ); ?> - <?php echo esc_html( $exam->end_date ?? '' ); ?></small>
        </div>
    </header>
    <section class="sms-doc-grid">
        <div>
            <h3>Candidate</h3>
            <strong><?php echo esc_html( $name ); ?></strong><br>
            <small>Adm No: <?php echo esc_html( $student->admission_no ?? '' ); ?></small><br>
            <small>Roll No: <?php echo esc_html( $student->roll_no ?? '' ); ?></small><br>
            <small>Class: <?php echo esc_html( $student->section ?? '' ); ?></small>
        </div>
        <div class="photo-box"><?php echo esc_html( strtoupper( substr( $name, 0, 1 ) ) ); ?></div>
    </section>
    <table class="sms-doc-table">
        <thead><tr><th>Date</th><th>Subject</th><th>Time</th><th>Room</th></tr></thead>
        <tbody><tr><td>—</td><td>Subject schedule will be issued separately</td><td>—</td><td>—</td></tr></tbody>
    </table>
    <footer class="sms-doc-foot">
        <div class="sign">_________________________<br><small>Candidate's Signature</small></div>
        <div class="sign r">_________________________<br><small>Principal's Signature</small></div>
    </footer>
</div>
