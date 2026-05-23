<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}inquiries ORDER BY id DESC LIMIT 100" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Pre-Admission Inquiries', 'Track potential admissions and follow-ups', 'dashicons-format-chat' ); ?>

    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Inquiries</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Name</th><th>Phone</th><th>Source</th><th>Follow-up</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><div class="ssm-row-name"><?php echo SSM_Helper::avatar( $r->name ); ?><?php echo esc_html( $r->name ); ?></div></td>
                            <td><?php echo esc_html( $r->phone ); ?></td>
                            <td><?php echo esc_html( $r->source ); ?></td>
                            <td><?php echo esc_html( $r->follow_up_date ); ?></td>
                            <td><?php echo SSM_Helper::badge( $r->status ); ?></td>
                            <td><div class="ssm-row-actions">
                                <a href="#"><span class="dashicons dashicons-edit"></span></a>
                                <a href="#" class="del" data-entity="inquiry" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a>
                            </div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="6" class="ssm-muted" style="text-align:center;padding:30px">No inquiries yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Log Inquiry</div></div>
            <form class="ssm-ajax-form" data-entity="inquiry">
                <div class="ssm-field"><label>Name</label><input class="ssm-input" name="name" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Phone</label><input class="ssm-input" name="phone"></div>
                    <div class="ssm-field"><label>Email</label><input class="ssm-input" name="email"></div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Source</label>
                        <select class="ssm-select" name="source"><option>Walk-in</option><option>Phone</option><option>Website</option><option>Referral</option></select>
                    </div>
                    <div class="ssm-field"><label>Follow-up</label><input class="ssm-input" type="date" name="follow_up_date"></div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Message</label><textarea class="ssm-textarea" name="message"></textarea></div>
                <input type="hidden" name="status" value="new">
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit"><span class="dashicons dashicons-saved"></span> Save Inquiry</button>
            </form>
        </div>
    </div>
</div>
