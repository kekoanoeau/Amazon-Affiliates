<?php
/**
 * Archive Template
 * Category, tag, and date archive pages.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">

    <!-- Archive Header -->
    <div class="archive-hero">
        <div class="container">
            <?php nest_well_breadcrumbs(); ?>
            <header class="archive-header">
                <?php
                $category_color_class = '';
                if ( is_category() ) {
                    $cat  = get_queried_object();
                    $slug = $cat->slug;

                    $stripe_categories = array(
                        'smart-home'    => 'stripe--forest',
                        'wellness-tech' => 'stripe--sage',
                        'home-beauty'   => 'stripe--moss',
                        'gift-guides'   => 'stripe--amber',
                        'deals'         => 'stripe--clay',
                    );

                    $category_color_class = isset( $stripe_categories[ $slug ] ) ? $stripe_categories[ $slug ] : '';
                }
                ?>

                <?php if ( $category_color_class ) : ?>
                <span class="archive-header__category-badge <?php echo esc_attr( $category_color_class ); ?>">
                    <?php echo esc_html( single_cat_title( '', false ) ); ?>
                </span>
                <?php else : ?>
                <?php the_archive_title( '<h1 class="archive-header__title">', '</h1>' ); ?>
                <?php endif; ?>

                <?php the_archive_description( '<p class="archive-header__desc">', '</p>' ); ?>

                <?php if ( is_category() ) : ?>
                <p class="archive-header__count">
                    <?php
                    $cat_count = $cat->count ?? 0;
                    printf(
                        /* translators: %d: post count */
                        esc_html( _n( '%d Review', '%d Reviews', $cat_count, 'nest-and-well' ) ),
                        (int) $cat_count
                    );
                    ?>
                </p>
                <?php endif; ?>
            </header>
        </div>
    </div><!-- .archive-hero -->

    <div class="archive-content container">
        <?php if ( have_posts() ) : ?>
        <div class="article-grid article-grid--3col">
            <?php
            while ( have_posts() ) :
                the_post();
                get_template_part( 'template-parts/content-article' );
            endwhile;
            ?>
        </div><!-- .article-grid -->

        <!-- Pagination -->
        <div class="archive-pagination">
            <?php
            the_posts_pagination(
                array(
                    'prev_text' => '&larr; ' . esc_html__( 'Previous', 'nest-and-well' ),
                    'next_text' => esc_html__( 'Next', 'nest-and-well' ) . ' &rarr;',
                    'class'     => 'pagination',
                )
            );
            ?>
        </div>

        <?php else : ?>
        <div class="no-results">
            <h2><?php esc_html_e( 'No articles found', 'nest-and-well' ); ?></h2>
            <p><?php esc_html_e( 'Try browsing another category or use the search to find what you\'re looking for.', 'nest-and-well' ); ?></p>
            <?php get_search_form(); ?>
        </div>
        <?php endif; ?>
    </div><!-- .archive-content -->

</main><!-- #main -->

<?php
get_footer();
