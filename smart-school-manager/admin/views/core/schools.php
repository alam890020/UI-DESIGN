<?php
/**
 * Schools - multi-school setup.
 *
 * @package SmartSchoolManager
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;
$p = $wpdb->prefix . 'ssm_';
$schools = $wpdb->get_results( "SELECT * FROM {$p}schools ORDER BY id DESC" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Schools', 'Multi-school setup & administrator management', 'dashicons-bank',
        array( array( 'href' => '#', 'label' => 'Add School', 'icon' => 'dashicons-plus', 'class' => 'ssm-btn-primary' ) )
    ); ?>

    <div class="ssm-stats-grid">
        <?php
        echo SSM_Helper::stat_card( 'Total Schools', count( $schools ), 'dashicons-bank', 'indigo' );
        $active = 0; foreach ( $schools as $s ) { if ( $s->status ) $active++; }
        echo SSM_Helper::stat_card( 'Active Schools', $active, 'dashicons-yes-alt', 'green' );
        echo SSM_Helper::stat_card( 'Inactive', count( $schools ) - $active, 'dashicons-warning', 'red' );
        echo SSM_Helper::stat_card( 'Multi-Campus', count( $schools ) > 1 ? 'Enabled' : 'Single', 'dashicons-admin-multisite', 'cyan' );
        ?>
    </div>

    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header">
                <div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> All Schools</div>
                <input type="text" class="ssm-input" placeholder="Search schools…" style="max-width:240px">
            </div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr>
                        <th>School</th><th>Code</th><th>Phone</th><th>Email</th><th>Status</th><th></th>
                    </tr></thead>
                    <tbody>
                    <?php if ( $schools ) : foreach ( $schools as $s ) : ?>
                        <tr>
                            <td><div class="ssm-row-name"><?php echo SSM_Helper::avatar( $s->name ); ?> <?php echo esc_html( $s->name ); ?></div></td>
                            <td><?php echo esc_html( $s->code ); ?></td>
                            <td><?php echo esc_html( $s->phone ); ?></td>
                            <td><?php echo esc_html( $s->email ); ?></td>
                            <td><?php echo SSM_Helper::badge( $s->status ? 'Active' : 'Inactive' ); ?></td>
                            <td><div class="ssm-row-actions">
                                <a href="#" title="Edit"><span class="dashicons dashicons-edit"></span></a>
                                <a href="#" class="del" data-entity="school" data-id="<?php echo (int) $s->id; ?>" title="Delete"><span class="dashicons dashicons-trash"></span></a>
                            </div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="6"><p class="ssm-muted" style="margin:24px;text-align:center">No schools yet — add your first school using the form on the right.</p></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add New School</div></div>
            <form class="ssm-ajax-form" data-entity="school">
                <div class="ssm-field"><label>School Name</label><input class="ssm-input" name="name" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Code</label><input class="ssm-input" name="code" placeholder="e.g. SCH-01"></div>
                    <div class="ssm-field"><label>Phone</label><input class="ssm-input" name="phone"></div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Email</label><input class="ssm-input" name="email" type="email"></div>
                <div class="ssm-field" style="margin-top:12px"><label>Address</label><textarea class="ssm-textarea" name="address"></textarea></div>
                <input type="hidden" name="status" value="1">
                <div style="margin-top:14px;display:flex;gap:8px">
                    <button class="ssm-btn ssm-btn-primary" type="submit"><span class="dashicons dashicons-saved"></span> Save School</button>
                    <button class="ssm-btn ssm-btn-ghost" type="reset">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
