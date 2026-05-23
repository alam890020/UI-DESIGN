<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Bulk Print Results', 'Print results for an entire class or exam', 'dashicons-printer' ); ?>
    <div class="ssm-card">
        <div class="ssm-form-grid-3">
            <div class="ssm-field"><label>Exam</label><select class="ssm-select"><option>Mid-term 2025</option></select></div>
            <div class="ssm-field"><label>Class</label><select class="ssm-select"><option>Class 5-A</option></select></div>
            <div class="ssm-field"><label>Layout</label><select class="ssm-select"><option>A4 Portrait</option><option>A4 Landscape</option><option>Letter</option></select></div>
        </div>
        <div style="margin-top:16px;display:flex;gap:8px">
            <button class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-printer"></span> Print PDF</button>
            <button class="ssm-btn ssm-btn-ghost"><span class="dashicons dashicons-download"></span> Export CSV</button>
        </div>
    </div>
</div>
