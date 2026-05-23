<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$opts = get_option( 'sms_settings', array() );
$g = function( $k, $d = '' ) use ( $opts ) { return isset( $opts[ $k ] ) ? $opts[ $k ] : $d; };
?>
<div class="sms-page">
    <?php SMS_Helper::page_header( 'Settings', 'Plugin-wide preferences', 'cog' ); ?>

    <form id="sms-settings-form">
        <div class="sms-grid-2">
            <div class="sms-card">
                <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('school',18); ?> School Identity</div></div>
                <div class="sms-field"><label>School Name</label><input class="sms-input" name="school_name" value="<?php echo esc_attr( $g('school_name') ); ?>"></div>
                <div class="sms-field" style="margin-top:12px"><label>Logo URL</label><input class="sms-input" name="logo_url" value="<?php echo esc_attr( $g('logo_url') ); ?>"></div>
                <div class="sms-field" style="margin-top:12px"><label>Address</label><textarea class="sms-textarea" name="address"><?php echo esc_textarea( $g('address') ); ?></textarea></div>
                <div class="sms-grid-2" style="margin-top:12px">
                    <div class="sms-field"><label>Phone</label><input class="sms-input" name="phone" value="<?php echo esc_attr( $g('phone') ); ?>"></div>
                    <div class="sms-field"><label>Email</label><input class="sms-input" name="email" value="<?php echo esc_attr( $g('email') ); ?>"></div>
                </div>
            </div>
            <div class="sms-card">
                <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('star',18); ?> Branding & Locale</div></div>
                <div class="sms-grid-2">
                    <div class="sms-field"><label>Currency</label><input class="sms-input" name="currency" value="<?php echo esc_attr( $g('currency','USD') ); ?>"></div>
                    <div class="sms-field"><label>Currency Sign</label><input class="sms-input" name="currency_sign" value="<?php echo esc_attr( $g('currency_sign','$') ); ?>"></div>
                </div>
                <div class="sms-grid-2" style="margin-top:12px">
                    <div class="sms-field"><label>Date Format</label>
                        <select class="sms-select" name="date_format">
                            <?php foreach ( array('Y-m-d','d/m/Y','m/d/Y','d M Y') as $f ) : ?>
                                <option value="<?php echo esc_attr( $f ); ?>" <?php selected( $g('date_format'), $f ); ?>><?php echo esc_html( $f ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sms-field"><label>Theme Color</label><input class="sms-input" type="color" name="theme_color" value="<?php echo esc_attr( $g('theme_color','#7c3aed') ); ?>"></div>
                </div>
                <div class="sms-grid-2" style="margin-top:12px">
                    <div class="sms-field"><label>Dark Mode (default)</label>
                        <select class="sms-select" name="dark_mode"><option value="0" <?php selected( $g('dark_mode',0), 0 ); ?>>Light</option><option value="1" <?php selected( $g('dark_mode',0), 1 ); ?>>Dark</option></select>
                    </div>
                    <div class="sms-field"><label>Load Charts CDN</label>
                        <select class="sms-select" name="load_cdn_libs"><option value="1" <?php selected( $g('load_cdn_libs',1), 1 ); ?>>Yes</option><option value="0" <?php selected( $g('load_cdn_libs',1), 0 ); ?>>No (offline)</option></select>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top:18px"><button type="submit" class="sms-btn sms-btn-primary"><?php echo SMS_Icons::svg('check',14); ?><span>Save Settings</span></button></div>
    </form>
</div>
