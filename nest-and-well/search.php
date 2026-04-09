<?php
/**
 * Search Results Template
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main site-main--search">
    <div class="container">

        <header class="search-header">
            <h1 class="search-header__title">
                <?php
                printf(
                    /* translators: %s: search query */
                    esc_html__( 'Search Results for: %s', 'nest-and-well' ),
                    '<span class="search-header__query">' . esc_html( get_search_query() ) . '</span>'
                );
                ?>
            </h1>
            <?php if ( have_posts() ) : ?>
            <p class="search-header__count">
                <?php
                global $wp_query;
                printf(
                    /* translators: %d: results count */
                    esc_html( _n( '%d result found', '%d results found', (int) $wp_query->found_posts, 'nest-and-well' ) ),
                    (int) $wp_query->found_posts
                );
                ?>
            </p>
            <?php endif; ?>
        </header>

        <!-- New Search Form -->
        <div class="search-form-wrap">
            <?php get_search_form(); ?>
        </div>

        <?php if ( have_posts() ) : ?>
        <div class="article-grid article-grid--3col">
            <?php
            while ( have_posts() ) :
                the_post();
                get_template_part( 'template-parts/content-article' );
            endwhile;
            ?>
        </div><!-- .article-grid -->

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
            <p><?php esc_html_e( 'No articles matched your search. Try a different term or browse our categories below.', 'nest-and-well' ); ?></p>

            <div class="no-results__categories">
                <h2><?php esc_html_e( 'Browse Categories', 'nest-and-well' ); ?></h2>
                <ul>
                    <?php
                    wp_list_categories(
                        array(
                            'title_li' => '',
                            'depth'    => 1,
                            'orderby'  => 'count',
                            'order'    => 'DESC',
                            'number'   => 8,
                        )
                    );
                    ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- .container -->
</main><!-- #main -->

<?php
get_footer();
