<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Setup Wizard', 'Get your school running in 5 quick steps', 'dashicons-admin-tools' ); ?>

    <div class="ssm-wizard-steps">
        <div class="ssm-wizard-step done"><span class="num">1</span><div><strong>School</strong><div class="ssm-muted" style="font-size:11px">Identity & branding</div></div></div>
        <div class="ssm-wizard-step done"><span class="num">2</span><div><strong>Session</strong><div class="ssm-muted" style="font-size:11px">Academic year</div></div></div>
        <div class="ssm-wizard-step active"><span class="num">3</span><div><strong>Classes</strong><div class="ssm-muted" style="font-size:11px">Class structure</div></div></div>
        <div class="ssm-wizard-step"><span class="num">4</span><div><strong>Staff</strong><div class="ssm-muted" style="font-size:11px">Teachers & roles</div></div></div>
        <div class="ssm-wizard-step"><span class="num">5</span><div><strong>Fees</strong><div class="ssm-muted" style="font-size:11px">Structures</div></div></div>
    </div>

    <div class="ssm-card">
        <h2 style="margin-bottom:8px">Step 3 — Classes & Subjects</h2>
        <p class="ssm-muted">Define the classes you offer and assign subjects. You can always change this later.</p>

        <div class="ssm-form-grid" style="margin-top:14px">
            <div class="ssm-field"><label>Lowest Class</label><input class="ssm-input" placeholder="Nursery"></div>
            <div class="ssm-field"><label>Highest Class</label><input class="ssm-input" placeholder="Class 12"></div>
        </div>

        <div style="margin-top:18px;display:flex;gap:8px">
            <button class="ssm-btn ssm-btn-ghost"><span class="dashicons dashicons-arrow-left-alt"></span> Back</button>
            <div class="ssm-spacer"></div>
            <button class="ssm-btn ssm-btn-primary">Next: Add Staff <span class="dashicons dashicons-arrow-right-alt"></span></button>
        </div>
    </div>
</div>
