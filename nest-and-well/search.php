<?php
/**
 * Search Results Template
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();

$query        = get_search_query();
$active_cat   = isset( $_GET['cat'] ) ? (int) $_GET['cat'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$has_results  = have_posts();
global $wp_query;
$found_count  = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;

// Categories used for the filter chips — same five Customizer-driven slugs
// as the category strip, in the same accent palette.
$chip_slugs = array( 'smart-home', 'wellness-tech', 'home-beauty', 'gift-guides', 'deals' );
$accents    = array(
    'smart-home'    => 'var(--pine)',
    'wellness-tech' => 'var(--sage)',
    'home-beauty'   => 'var(--moss)',
    'gift-guides'   => 'var(--amber)',
    'deals'         => 'var(--clay)',
);
?>

<main id="main" class="site-main site-main--search">
    <div class="container">

        <header class="search-header">
            <h1 class="search-header__title">
                <?php
                printf(
                    /* translators: %s: search query */
                    esc_html__( 'Search Results for: %s', 'nest-and-well' ),
                    '<span class="search-header__query">' . esc_html( $query ) . '</span>'
                );
                ?>
            </h1>
            <?php if ( $has_results ) : ?>
            <p class="search-header__count">
                <?php
                printf(
                    /* translators: %d: results count */
                    esc_html( _n( '%d result found', '%d results found', $found_count, 'nest-and-well' ) ),
                    $found_count
                );
                ?>
            </p>
            <?php endif; ?>
        </header>

        <!-- Search form -->
        <div class="search-form-wrap">
            <?php get_search_form(); ?>
        </div>

        <!-- Category filter chips -->
        <?php if ( $query ) : ?>
        <div class="search-filters" role="group" aria-label="<?php esc_attr_e( 'Filter by category', 'nest-and-well' ); ?>">
            <a href="<?php echo esc_url( add_query_arg( array( 's' => $query ), home_url( '/' ) ) ); ?>"
               class="search-filter-chip<?php echo $active_cat ? '' : ' is-active'; ?>"
               style="--cat-accent: var(--sage);">
                <?php esc_html_e( 'All', 'nest-and-well' ); ?>
            </a>
            <?php
            foreach ( $chip_slugs as $slug ) :
                $term = get_term_by( 'slug', $slug, 'category' );
                if ( ! $term || is_wp_error( $term ) ) {
                    continue;
                }
                $url = add_query_arg(
                    array( 's' => $query, 'cat' => $term->term_id ),
                    home_url( '/' )
                );
                $is_active = ( $active_cat === (int) $term->term_id );
                ?>
            <a href="<?php echo esc_url( $url ); ?>"
               class="search-filter-chip<?php echo $is_active ? ' is-active' : ''; ?>"
               style="--cat-accent: <?php echo esc_attr( $accents[ $slug ] ); ?>;">
                <?php echo esc_html( $term->name ); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ( $has_results ) : ?>
        <div class="article-grid article-grid--3col">
            <?php
            while ( have_posts() ) :
                the_post();
                get_template_part( 'template-parts/content-article' );
            endwhile;
            ?>
        </div>

        <div class="search-pagination">
            <?php
            the_posts_pagination(
                array(
                    'prev_text' => '&larr; ' . esc_html__( 'Previous', 'nest-and-well' ),
                    'next_text' => esc_html__( 'Next', 'nest-and-well' ) . ' &rarr;',
                )
            );
            ?>
        </div>

        <?php else : ?>
        <div class="no-results">
            <p class="no-results__lede">
                <?php
                if ( $active_cat ) {
                    esc_html_e( "Nothing in that category matched your search. Try removing the filter or searching for another term.", 'nest-and-well' );
                } else {
                    esc_html_e( "No articles matched your search. Try one of these popular topics, or browse a category.", 'nest-and-well' );
                }
                ?>
            </p>

            <!-- Popular searches: most-used tags as suggested queries -->
            <?php
            $popular_tags = get_tags(
                array(
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                    'number'     => 8,
                    'hide_empty' => true,
                )
            );
            if ( ! empty( $popular_tags ) && ! is_wp_error( $popular_tags ) ) :
            ?>
            <div class="no-results__popular">
                <h2 class="no-results__heading"><?php esc_html_e( 'Popular searches', 'nest-and-well' ); ?></h2>
                <div class="no-results__chips">
                    <?php foreach ( $popular_tags as $tag ) :
                        $tag_search_url = add_query_arg( array( 's' => $tag->name ), home_url( '/' ) );
                        ?>
                    <a href="<?php echo esc_url( $tag_search_url ); ?>" class="search-filter-chip">
                        <?php echo esc_html( $tag->name ); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="no-results__categories">
                <h2 class="no-results__heading"><?php esc_html_e( 'Browse by category', 'nest-and-well' ); ?></h2>
                <ul class="no-results__category-list">
                    <?php foreach ( $chip_slugs as $slug ) :
                        $term = get_term_by( 'slug', $slug, 'category' );
                        if ( ! $term || is_wp_error( $term ) ) {
                            continue;
                        }
                        ?>
                    <li style="--cat-accent: <?php echo esc_attr( $accents[ $slug ] ); ?>;">
                        <a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
                            <?php echo esc_html( $term->name ); ?>
                            <span class="no-results__count">
                                <?php
                                printf(
                                    /* translators: %d: post count in category */
                                    esc_html( _n( '%d article', '%d articles', $term->count, 'nest-and-well' ) ),
                                    (int) $term->count
                                );
                                ?>
                            </span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

    </div>
</main>

<?php
get_footer();
