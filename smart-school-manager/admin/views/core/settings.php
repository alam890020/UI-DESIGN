<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$opts = get_option( 'ssm_settings', array() );
$g = function( $k, $d='' ) use ( $opts ) { return isset( $opts[ $k ] ) ? $opts[ $k ] : $d; };
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Global Settings', 'Configure plugin-wide preferences', 'dashicons-admin-generic' ); ?>

    <form id="ssm-settings-form">
        <div class="ssm-grid-2">
            <div class="ssm-card">
                <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-bank"></span> School Identity</div></div>
                <div class="ssm-field"><label>School Name</label><input class="ssm-input" name="school_name" value="<?php echo esc_attr( $g('school_name') ); ?>"></div>
                <div class="ssm-field" style="margin-top:12px"><label>Logo URL</label><input class="ssm-input" name="logo_url" value="<?php echo esc_attr( $g('logo_url') ); ?>"></div>
                <div class="ssm-field" style="margin-top:12px"><label>Address</label><textarea class="ssm-textarea" name="address"><?php echo esc_textarea( $g('address') ); ?></textarea></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Phone</label><input class="ssm-input" name="phone" value="<?php echo esc_attr( $g('phone') ); ?>"></div>
                    <div class="ssm-field"><label>Email</label><input class="ssm-input" name="email" value="<?php echo esc_attr( $g('email') ); ?>"></div>
                </div>
            </div>

            <div class="ssm-card">
                <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-art"></span> Branding & Locale</div></div>
                <div class="ssm-form-grid">
                    <div class="ssm-field"><label>Currency</label><input class="ssm-input" name="currency" value="<?php echo esc_attr( $g('currency','USD') ); ?>"></div>
                    <div class="ssm-field"><label>Currency Sign</label><input class="ssm-input" name="currency_sign" value="<?php echo esc_attr( $g('currency_sign','$') ); ?>"></div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Date Format</label>
                        <select class="ssm-select" name="date_format">
                            <?php foreach ( array('Y-m-d','d/m/Y','m/d/Y','d M Y') as $f ) : ?>
                                <option value="<?php echo esc_attr( $f ); ?>" <?php selected( $g('date_format'), $f ); ?>><?php echo esc_html( $f ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="ssm-field"><label>Theme Color</label><input class="ssm-input" type="color" name="theme_color" value="<?php echo esc_attr( $g('theme_color','#6366f1') ); ?>"></div>
                </div>

                <div class="ssm-divider"></div>

                <div class="ssm-card-title"><span class="dashicons dashicons-shield"></span> Preview</div>
                <div style="margin-top:12px;display:flex;gap:12px;align-items:center">
                    <div class="ssm-brand-mark" style="background:linear-gradient(135deg,<?php echo esc_attr( $g('theme_color','#6366f1') ); ?>,#06b6d4)"><?php echo esc_html( strtoupper( substr( $g('school_name','SS'), 0, 2 ) ) ); ?></div>
                    <div>
                        <strong style="display:block"><?php echo esc_html( $g('school_name','Your School') ); ?></strong>
                        <span class="ssm-muted">Smart School Manager</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top:18px">
            <button type="submit" class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-saved"></span> Save Settings</button>
        </div>
    </form>
</div>
