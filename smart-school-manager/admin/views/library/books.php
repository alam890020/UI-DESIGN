<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'ssm_';
$rows = $wpdb->get_results( "SELECT * FROM {$p}books ORDER BY id DESC LIMIT 100" );
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Books Catalog', 'Library inventory management', 'dashicons-book-alt' ); ?>
    <div class="ssm-grid-2-1">
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-list-view"></span> All Books</div></div>
            <div class="ssm-table-wrap">
                <table class="ssm-table">
                    <thead><tr><th>Title</th><th>Author</th><th>ISBN</th><th>Category</th><th>Available</th><th></th></tr></thead>
                    <tbody>
                    <?php if ( $rows ) : foreach ( $rows as $r ) : ?>
                        <tr>
                            <td><div class="ssm-row-name"><span class="ssm-avatar ssm-grad-purple"><span class="dashicons dashicons-book" style="color:#fff"></span></span><strong><?php echo esc_html( $r->title ); ?></strong></div></td>
                            <td><?php echo esc_html( $r->author ); ?></td>
                            <td><code><?php echo esc_html( $r->isbn ); ?></code></td>
                            <td><?php echo esc_html( $r->category ); ?></td>
                            <td><?php echo (int) $r->available; ?> / <?php echo (int) $r->quantity; ?></td>
                            <td><div class="ssm-row-actions"><a href="#" class="del" data-entity="book" data-id="<?php echo (int) $r->id; ?>"><span class="dashicons dashicons-trash"></span></a></div></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="6" class="ssm-muted" style="text-align:center;padding:30px">No books yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ssm-card">
            <div class="ssm-card-header"><div class="ssm-card-title"><span class="dashicons dashicons-plus-alt"></span> Add Book</div></div>
            <form class="ssm-ajax-form" data-entity="book">
                <div class="ssm-field"><label>Title</label><input class="ssm-input" name="title" required></div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Author</label><input class="ssm-input" name="author"></div>
                    <div class="ssm-field"><label>ISBN</label><input class="ssm-input" name="isbn"></div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Category</label><input class="ssm-input" name="category"></div>
                    <div class="ssm-field"><label>Shelf</label><input class="ssm-input" name="shelf"></div>
                </div>
                <div class="ssm-form-grid" style="margin-top:12px">
                    <div class="ssm-field"><label>Quantity</label><input class="ssm-input" type="number" name="quantity" value="1"></div>
                    <div class="ssm-field"><label>Available</label><input class="ssm-input" type="number" name="available" value="1"></div>
                </div>
                <button class="ssm-btn ssm-btn-primary" style="margin-top:14px" type="submit">Save Book</button>
            </form>
        </div>
    </div>
</div>
