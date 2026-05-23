<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'sms_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}notices ORDER BY id DESC LIMIT 100" );
?>
<div class="sms-page">
    <?php SMS_Helper::page_header( 'Notices', 'Announcements', 'megaphone' ); ?>
    <div class="sms-card">
        <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('list',18); ?> Notices</div></div>
        <div class="sms-table-wrap">
            <table class="sms-table">
                <thead><tr><th>Item</th><th>Details</th><th></th></tr></thead>
                <tbody>
                <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                    <tr>
                        <td><strong><?php echo esc_html( $r->name ?? $r->title ?? $r->subject ?? ('#' . $r->id) ); ?></strong></td>
                        <td class="sms-muted"><?php echo esc_html( wp_trim_words( wp_json_encode( (array) $r ), 12 ) ); ?></td>
                        <td><div class="sms-row-actions">
                            <a href="#"><?php echo SMS_Icons::svg('edit',14); ?></a>
                            <a href="#" class="del" data-entity="notice" data-id="<?php echo (int) $r->id; ?>"><?php echo SMS_Icons::svg('trash',14); ?></a>
                        </div></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="3" class="sms-muted" style="text-align:center;padding:30px">No records yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
