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
 * Render the infinite-scroll article feed for a given category slug.
 *
 * Used by the dedicated per-category page templates. Looks up the
 * category by slug, queries 12 posts per page, and outputs the same
 * hp-feed grid structure as the homepage.
 *
 * @param string $category_slug  Category slug to filter by, or '' for all posts.
 */
function nest_well_render_category_feed( $category_slug = '' ) {
    $category_id = 0;

    if ( $category_slug ) {
        $term = get_term_by( 'slug', $category_slug, 'category' );
        if ( $term && ! is_wp_error( $term ) ) {
            $category_id = (int) $term->term_id;
        }
    }

    $query_args = array(
        'post_type'           => 'post',
        'posts_per_page'      => 12,
        'ignore_sticky_posts' => true,
        'post_status'         => 'publish',
    );

    if ( $category_id ) {
        $query_args['cat'] = $category_id;
    }

    $feed_query  = new WP_Query( $query_args );
    $total_pages = (int) $feed_query->max_num_pages;
    ?>
    <main id="main" class="site-main site-main--category-feed">
        <div class="hp-feed container">

            <div class="hp-feed__grid" id="hp-feed-grid">
                <?php if ( $feed_query->have_posts() ) : ?>
                    <?php while ( $feed_query->have_posts() ) : $feed_query->the_post(); ?>
                    <div class="flex-item homepage-style-item">
                        <?php get_template_part( 'template-parts/content-article' ); ?>
                    </div>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                <p class="hp-feed__no-posts">
                    <?php esc_html_e( 'No articles found. Check back soon!', 'nest-and-well' ); ?>
                </p>
                <?php endif; ?>
            </div><!-- #hp-feed-grid -->

            <?php if ( $total_pages > 1 ) : ?>
            <div class="hp-feed__sentinel js-infinite-sentinel"
                 aria-hidden="true"
                 data-page="2"
                 data-per-page="12"
                 data-total-pages="<?php echo esc_attr( $total_pages ); ?>"
                 <?php if ( $category_id ) : ?>data-category-id="<?php echo esc_attr( $category_id ); ?>"<?php endif; ?>>
            </div>
            <?php endif; ?>

            <div class="hp-feed__loading js-infinite-loading" hidden aria-live="polite" aria-busy="false">
                <span class="hp-feed__spinner" aria-hidden="true"></span>
                <?php esc_html_e( 'Good things take a moment\u2026', 'nest-and-well' ); ?>
            </div>

            <div class="hp-feed__end js-infinite-end" hidden aria-live="polite">
                <p><?php esc_html_e( "You've read everything. Nice work.", 'nest-and-well' ); ?></p>
            </div>

        </div><!-- .hp-feed -->
    </main><!-- #main -->
    <?php
}

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
