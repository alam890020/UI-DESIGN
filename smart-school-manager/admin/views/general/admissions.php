<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT a.*, c.name AS class_name FROM {$p}admissions a LEFT JOIN {$p}classes c ON c.id=a.class_id ORDER BY a.id DESC LIMIT 100" );
$classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes ORDER BY name" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Admissions', 'New admission inquiries & processing', 'dashicons-welcome-add-page' ); ?>

    <div class="ssm-stats-grid">
        <?php
        $total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}admissions" );
        $pending = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}admissions WHERE status='pending'" );
        $approved = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}admissions WHERE status='approved'" );
        $rejected = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}admissions WHERE status='rejected'" );
        echo SSM_Helper::stat_card( 'Total', number_format( $total ), 'dashicons-welcome-add-page', 'indigo' );
        echo SSM_Helper::stat_card( 'Pending', number_format( $pending ), 'dashicons-clock', 'orange' );
        echo SSM_Helper::stat_card( 'Approved', number_format( $approved ), 'dashicons-yes-alt', 'green' );
        echo SSM_Helper::stat_card( 'Rejected', number_format( $rejected ), 'dashicons-no-alt', 'red' );
        ?>
    </div>

    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Admission Applications</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Applicant</th><th>Class</th><th>Phone</th><th>Email</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><div class="ssm-row-name"><?php echo SSM_Helper::avatar( $r->applicant_name ); ?><?php echo esc_html( $r->applicant_name ); ?></div></td>
                            <td><?php echo esc_html( $r->class_name ); ?></td>
                            <td><?php echo esc_html( $r->phone ); ?></td>
                            <td><?php echo esc_html( $r->email ); ?></td>
                            <td><?php echo SSM_Helper::badge( $r->status ); ?></td>
                            <td><div class="ssm-row-actions">
                                <a href="#"><span class="dashicons dashicons-yes"></span></a>
                                <a href="#"><span class="dashicons dashicons-no"></span></a>
                                <a href="#" class="del" data-entity="admission" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a>
                            </div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="6" class="ssm-muted" style="text-align:center;padding:30px">No admissions yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> New Admission</div></div>
            <form class="ssm-ajax-form" data-entity="admission">
                <div class="ssm-field"><label>Applicant Name</label><input class="ssm-input" name="applicant_name" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Email</label><input class="ssm-input" name="email" type="email"></div>
                    <div class="ssm-field"><label>Phone</label><input class="ssm-input" name="phone"></div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Class</label>
                    <select class="ssm-select" name="class_id">
                        <option value="0">— Select —</option>
                        <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Notes</label><textarea class="ssm-textarea" name="notes"></textarea></div>
                <input type="hidden" name="status" value="pending">
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit"><span class="dashicons dashicons-saved"></span> Submit</button>
            </form>
        </div>
    </div>
</div>
