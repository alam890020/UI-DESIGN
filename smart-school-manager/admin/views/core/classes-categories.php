<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$cats = $wpdb->get_results( "SELECT * FROM {$p}categories ORDER BY id DESC" );
$classes = $wpdb->get_results( "SELECT c.*, cat.name AS cat_name FROM {$p}classes c LEFT JOIN {$p}categories cat ON cat.id=c.category_id ORDER BY c.id DESC" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Classes & Categories', 'Configure class structure and class categories', 'dashicons-welcome-write-blog' ); ?>

    <div class="ssm-tabs">
        <div class="ssm-tab active" data-target="ssm-tab-classes">Classes</div>
        <div class="ssm-tab" data-target="ssm-tab-cats">Categories</div>
    </div>

    <div id="ssm-tab-classes" class="ssm-tab-content">
        <div class="ssm-grid-2-1">
            <div class="ssm-card">
                <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> Classes</div></div>
                <div class="ssm-table-wrap">
                    <table class="ssm-table">
                        <thead><tr><th>Class</th><th>Section</th><th>Category</th><th>Capacity</th><th></th></tr></thead>
                        <tbody>
                        <?php if ( $classes ) : foreach ( $classes as $c ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $c->name ); ?></strong></td>
                                <td><?php echo esc_html( $c->section ); ?></td>
                                <td><?php echo esc_html( $c->cat_name ); ?></td>
                                <td><?php echo (int) $c->capacity; ?></td>
                                <td><div class="ssm-row-actions">
                                    <a href="#"><span class="dashicons dashicons-edit"></span></a>
                                    <a href="#" class="del" data-entity="class_item" data-id="<?php echo (int) $c->id; ?>"><span class="dashicons dashicons-trash"></span></a>
                                </div></td>
                            </tr>
                        <?php endforeach; else : ?>
                            <tr><td colspan="5" class="ssm-muted" style="text-align:center;padding:30px">No classes yet.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="ssm-card">
                <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Class</div></div>
                <form class="ssm-ajax-form" data-entity="class_item">
                    <div class="ssm-field"><label>Class Name</label><input class="ssm-input" name="name" placeholder="Class 5" required></div>
                    <div class="ssm-form-grid" style="margin-top:12px">
                        <div class="ssm-field"><label>Section</label><input class="ssm-input" name="section" placeholder="A"></div>
                        <div class="ssm-field"><label>Capacity</label><input class="ssm-input" type="number" name="capacity" value="40"></div>
                    </div>
                    <div class="ssm-field" style="margin-top:12px"><label>Category</label>
                        <select class="ssm-select" name="category_id">
                            <option value="0">— Select —</option>
                            <?php foreach ( $cats as $cat ) : ?>
                                <option value="<?php echo (int) $cat->id; ?>"><?php echo esc_html( $cat->name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="status" value="1">
                    <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit"><span class="dashicons dashicons-saved"></span> Save Class</button>
                </form>
            </div>
        </div>
    </div>

    <div id="ssm-tab-cats" class="ssm-tab-content" style="display:none">
        <div class="ssm-grid-2-1">
            <div class="ssm-card">
                <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-category"></span> Categories</div></div>
                <div class="ssm-table-wrap">
                    <table class="ssm-table">
                        <thead><tr><th>Name</th><th>Description</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ( $cats as $c ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $c->name ); ?></strong></td>
                                <td><?php echo esc_html( $c->description ); ?></td>
                                <td><div class="ssm-row-actions">
                                    <a href="#"><span class="dashicons dashicons-edit"></span></a>
                                    <a href="#" class="del" data-entity="category" data-id="<?php echo (int) $c->id; ?>"><span class="dashicons dashicons-trash"></span></a>
                                </div></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="ssm-card">
                <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Category</div></div>
                <form class="ssm-ajax-form" data-entity="category">
                    <div class="ssm-field"><label>Name</label><input class="ssm-input" name="name" required></div>
                    <div class="ssm-field" style="margin-top:12px"><label>Description</label><textarea class="ssm-textarea" name="description"></textarea></div>
                    <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit"><span class="dashicons dashicons-saved"></span> Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
