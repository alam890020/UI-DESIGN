<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'sms_';
$cats = $wpdb->get_results( "SELECT * FROM {$p}categories ORDER BY id DESC" );
$classes = $wpdb->get_results( "SELECT c.*, cat.name AS cat_name FROM {$p}classes c LEFT JOIN {$p}categories cat ON cat.id=c.category_id ORDER BY c.id DESC" );
?>
<div class="sms-page">
    <?php SMS_Helper::page_header( 'Classes & Categories', 'Configure class structure and categories', 'graduation' ); ?>

    <div class="sms-tabs">
        <button class="sms-tab active" data-target="sms-tab-classes">Classes</button>
        <button class="sms-tab" data-target="sms-tab-cats">Categories</button>
    </div>

    <div id="sms-tab-classes" class="sms-tab-pane">
        <div class="sms-grid-2-1">
            <div class="sms-card">
                <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('list',18); ?> Classes</div></div>
                <div class="sms-table-wrap">
                    <table class="sms-table">
                        <thead><tr><th>Class</th><th>Section</th><th>Category</th><th>Capacity</th><th></th></tr></thead>
                        <tbody>
                        <?php if ( $classes ) : foreach ( $classes as $c ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $c->name ); ?></strong></td>
                                <td><?php echo esc_html( $c->section ); ?></td>
                                <td><?php echo esc_html( $c->cat_name ); ?></td>
                                <td><?php echo (int) $c->capacity; ?></td>
                                <td><div class="sms-row-actions">
                                    <a href="#"><?php echo SMS_Icons::svg('edit',14); ?></a>
                                    <a href="#" class="del" data-entity="class_item" data-id="<?php echo (int) $c->id; ?>"><?php echo SMS_Icons::svg('trash',14); ?></a>
                                </div></td>
                            </tr>
                        <?php endforeach; else : ?>
                            <tr><td colspan="5" class="sms-muted" style="text-align:center;padding:30px">No classes.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="sms-card">
                <div class="sms-card-head"><div class="sms-card-title"><?php echo SMS_Icons::svg('plus',18); ?> Add Class</div></div>
                <form class="sms-ajax-form" data-entity="class_item">
                    <div class="sms-field"><label>Class Name</label><input class="sms-input" name="name" required></div>
                    <div class="sms-grid-2" style="margin-top:12px">
                        <div class="sms-field"><label>Section</label><input class="sms-input" name="section" placeholder="A"></div>
                        <div class="sms-field"><label>Capacity</label><input class="sms-input" type="number" name="capacity" value="40"></div>
                    </div>
                    <div class="sms-field" style="margin-top:12px"><label>Category</label>
                        <select class="sms-select" name="category_id">
                            <option value="0">— Select —</option>
                            <?php foreach ( $cats as $cat ) : ?><option value="<?php echo (int) $cat->id; ?>"><?php echo esc_html( $cat->name ); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="status" value="1">
                    <button class="sms-btn sms-btn-primary" style="margin-top:14px" type="submit"><?php echo SMS_Icons::svg('check',14); ?><span>Save</span></button>
                </form>
            </div>
        </div>
    </div>

    <div id="sms-tab-cats" class="sms-tab-pane" style="display:none">
        <div class="sms-grid-2-1">
            <div class="sms-card">
                <div class="sms-table-wrap">
                    <table class="sms-table">
                        <thead><tr><th>Name</th><th>Description</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ( $cats as $c ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $c->name ); ?></strong></td>
                                <td><?php echo esc_html( $c->description ); ?></td>
                                <td><div class="sms-row-actions"><a href="#" class="del" data-entity="category" data-id="<?php echo (int) $c->id; ?>"><?php echo SMS_Icons::svg('trash',14); ?></a></div></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="sms-card">
                <div class="sms-card-title"><?php echo SMS_Icons::svg('plus',18); ?> Add Category</div>
                <form class="sms-ajax-form" data-entity="category" style="margin-top:12px">
                    <div class="sms-field"><label>Name</label><input class="sms-input" name="name" required></div>
                    <div class="sms-field" style="margin-top:12px"><label>Description</label><textarea class="sms-textarea" name="description"></textarea></div>
                    <button class="sms-btn sms-btn-primary" style="margin-top:14px" type="submit"><?php echo SMS_Icons::svg('check',14); ?><span>Save</span></button>
                </form>
            </div>
        </div>
    </div>
</div>
