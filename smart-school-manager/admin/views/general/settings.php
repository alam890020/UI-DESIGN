<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'School Settings', 'School-level preferences and policies', 'dashicons-admin-settings' ); ?>

    <div class="ssm-grid-2">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-clock"></span> Working Hours</div></div>
            <div class="ssm-form-grid">
                <div class="ssm-field"><label>School Open</label><input class="ssm-input" type="time" value="08:00"></div>
                <div class="ssm-field"><label>School Close</label><input class="ssm-input" type="time" value="14:30"></div>
            </div>
            <div class="ssm-form-grid" style="margin-top:12px">
                <div class="ssm-field"><label>Late Threshold (min)</label><input class="ssm-input" type="number" value="10"></div>
                <div class="ssm-field"><label>Half-day Threshold (hr)</label><input class="ssm-input" type="number" value="4"></div>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-money-alt"></span> Fee Policy</div></div>
            <div class="ssm-form-grid">
                <div class="ssm-field"><label>Late Fee per day</label><input class="ssm-input" type="number" value="10"></div>
                <div class="ssm-field"><label>Grace Days</label><input class="ssm-input" type="number" value="5"></div>
            </div>
            <div class="ssm-form-grid" style="margin-top:12px">
                <div class="ssm-field"><label>Send Reminder</label>
                    <select class="ssm-select"><option>Email</option><option>SMS</option><option>Both</option></select>
                </div>
                <div class="ssm-field"><label>Auto-generate Invoices</label>
                    <select class="ssm-select"><option>Monthly</option><option>Quarterly</option><option>Annually</option></select>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top:14px"><button class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-saved"></span> Save School Settings</button></div>
</div>
