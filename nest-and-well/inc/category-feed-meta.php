<?php
/**
 * Category Feed Meta Box
 *
 * Adds a "Category Feed Settings" side meta box to all pages.
 * Only active when the "Category Feed" template is selected.
 * Editors choose which category's posts to display in the feed.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the meta box on page post type.
 */
function nest_well_register_category_feed_meta_box() {
    add_meta_box(
        'nest_well_category_feed',
        esc_html__( 'Category Feed Settings', 'nest-and-well' ),
        'nest_well_category_feed_meta_box_html',
        'page',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'nest_well_register_category_feed_meta_box' );

/**
 * Render the meta box.
 *
 * @param WP_Post $post Current page object.
 */
function nest_well_category_feed_meta_box_html( $post ) {
    $template = get_post_meta( $post->ID, '_wp_page_template', true );

    // Show a hint when the template isn't active.
    if ( 'page-category-feed.php' !== $template ) {
        echo '<p style="color:#888;font-style:italic;font-size:12px;margin:0">' .
             esc_html__( 'Switch the page template to "Category Feed" to enable this setting.', 'nest-and-well' ) .
             '</p>';
        return;
    }

    wp_nonce_field( 'nest_well_category_feed_save', 'nest_well_category_feed_nonce' );

    $saved_id   = (int) get_post_meta( $post->ID, '_feed_category', true );
    $categories = get_categories(
        array(
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        )
    );
    ?>
    <p style="margin-top:0">
        <label for="nest-well-feed-category" style="display:block;margin-bottom:5px;font-weight:600;font-size:12px">
            <?php esc_html_e( 'Show posts from category:', 'nest-and-well' ); ?>
        </label>
        <select id="nest-well-feed-category" name="nest_well_feed_category" style="width:100%">
            <option value=""><?php esc_html_e( '— All categories —', 'nest-and-well' ); ?></option>
            <?php foreach ( $categories as $cat ) : ?>
            <option value="<?php echo esc_attr( $cat->term_id ); ?>"<?php selected( $saved_id, $cat->term_id ); ?>>
                <?php echo esc_html( $cat->name ); ?> (<?php echo (int) $cat->count; ?>)
            </option>
            <?php endforeach; ?>
        </select>
    </p>
    <?php
}

/**
 * Save the selected category when the page is saved.
 *
 * @param int $post_id Page ID.
 */
function nest_well_save_category_feed_meta( $post_id ) {
    if (
        ! isset( $_POST['nest_well_category_feed_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['nest_well_category_feed_nonce'] ) ),
            'nest_well_category_feed_save'
        ) ||
        ! current_user_can( 'edit_post', $post_id ) ||
        ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    ) {
        return;
    }

    $category_id = isset( $_POST['nest_well_feed_category'] ) ? (int) $_POST['nest_well_feed_category'] : 0;

    if ( $category_id > 0 ) {
        update_post_meta( $post_id, '_feed_category', $category_id );
    } else {
        delete_post_meta( $post_id, '_feed_category' );
    }
}
add_action( 'save_post_page', 'nest_well_save_category_feed_meta' );
