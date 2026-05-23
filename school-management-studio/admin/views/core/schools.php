<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'sms_';
$schools = $wpdb->get_results( "SELECT * FROM {$p}schools ORDER BY id DESC" );
?>
<div class="sms-page">
    <?php SMS_Helper::page_header( 'Schools', 'Multi-school setup & administration', 'school' ); ?>

    <div class="sms-stats">
        <?php
        $active = 0; foreach ( $schools as $s ) { if ( $s->status ) $active++; }
        echo SMS_Helper::stat( 'Total Schools', count( $schools ), 'school', 'violet' );
        echo SMS_Helper::stat( 'Active', $active, 'check', 'mint' );
        echo SMS_Helper::stat( 'Inactive', count( $schools ) - $active, 'x', 'rose' );
        echo SMS_Helper::stat( 'Mode', count( $schools ) > 1 ? 'Multi' : 'Single', 'grid', 'cyan' );
        ?>
    </div>

    <div class="sms-grid-2-1">
        <div class="sms-card">
            <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('list',18); ?> All Schools</div></div>
            <div class="sms-table-wrap">
                <table class="sms-table">
                    <thead><tr><th>School</th><th>Code</th><th>Phone</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $schools ) : foreach ( $schools as $s ) : ?>
                        <tr>
                            <td><div class="sms-row-name"><?php echo SMS_Helper::avatar( $s->name ); ?> <?php echo esc_html( $s->name ); ?></div></td>
                            <td><code><?php echo esc_html( $s->code ); ?></code></td>
                            <td><?php echo esc_html( $s->phone ); ?></td>
                            <td><?php echo SMS_Helper::badge( $s->status ? 'Active' : 'Inactive' ); ?></td>
                            <td><div class="sms-row-actions">
                                <a href="#"><?php echo SMS_Icons::svg('edit',14); ?></a>
                                <a href="#" class="del" data-entity="school" data-id="<?php echo (int) $s->id; ?>"><?php echo SMS_Icons::svg('trash',14); ?></a>
                            </div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="5" class="sms-muted" style="text-align:center;padding:30px">No schools yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="sms-card">
            <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('plus',18); ?> Add School</div></div>
            <form class="sms-ajax-form" data-entity="school">
                <div class="sms-field"><label>School Name</label><input class="sms-input" name="name" required></div>
                <div class="sms-grid-2" style="margin-top:12px">
                    <div class="sms-field"><label>Code</label><input class="sms-input" name="code"></div>
                    <div class="sms-field"><label>Phone</label><input class="sms-input" name="phone"></div>
                </div>
                <div class="sms-field" style="margin-top:12px"><label>Email</label><input class="sms-input" name="email"></div>
                <div class="sms-field" style="margin-top:12px"><label>Address</label><textarea class="sms-textarea" name="address"></textarea></div>
                <input type="hidden" name="status" value="1">
                <button class="sms-btn sms-btn-primary" style="margin-top:14px" type="submit"><?php echo SMS_Icons::svg('check',14); ?><span>Save</span></button>
            </form>
        </div>
    </div>
</div>
