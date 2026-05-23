<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT s.*, c.name AS class_name FROM {$p}subjects s LEFT JOIN {$p}classes c ON c.id=s.class_id ORDER BY s.id DESC" );
$classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Subjects', 'Per-class subject configuration', 'dashicons-book-alt' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> All Subjects</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Subject</th><th>Code</th><th>Class</th><th>Type</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $r->name ); ?></strong></td>
                            <td><code><?php echo esc_html( $r->code ); ?></code></td>
                            <td><?php echo esc_html( $r->class_name ); ?></td>
                            <td><span class="ssm-badge ssm-badge-info"><?php echo esc_html( ucfirst( $r->type ) ); ?></span></td>
                            <td><div class="ssm-row-actions">
                                <a href="#"><span class="dashicons dashicons-edit"></span></a>
                                <a href="#" class="del" data-entity="subject" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a>
                            </div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="5" class="ssm-muted" style="text-align:center;padding:30px">No subjects yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Subject</div></div>
            <form class="ssm-ajax-form" data-entity="subject">
                <div class="ssm-field"><label>Subject Name</label><input class="ssm-input" name="name" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Code</label><input class="ssm-input" name="code"></div>
                    <div class="ssm-field"><label>Type</label>
                        <select class="ssm-select" name="type"><option value="theory">Theory</option><option value="practical">Practical</option><option value="elective">Elective</option></select>
                    </div>
                </div>
                <div class="ssm-field" style="margin-top:12px"><label>Class</label>
                    <select class="ssm-select" name="class_id" required>
                        <option value="">— Select —</option>
                        <?php foreach ( $classes as $c ) : ?><option value="<?php echo (int) $c->id; ?>"><?php echo esc_html( $c->name . ' ' . $c->section ); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save Subject</button>
            </form>
        </div>
    </div>
</div>
