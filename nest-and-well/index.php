<?php
/**
 * Main Template File (Fallback)
 * Used as the main loop fallback when no more specific template exists.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container">
        <div class="archive-header">
            <?php if ( is_home() && ! is_front_page() ) : ?>
            <h1 class="archive-header__title"><?php esc_html_e( 'Latest Articles', 'nest-and-well' ); ?></h1>
            <?php elseif ( is_archive() ) : ?>
            <?php the_archive_title( '<h1 class="archive-header__title">', '</h1>' ); ?>
            <?php the_archive_description( '<div class="archive-header__desc">', '</div>' ); ?>
            <?php endif; ?>
        </div>

        <?php if ( have_posts() ) : ?>
        <div class="article-grid article-grid--3col">
            <?php
            while ( have_posts() ) :
                the_post();
                get_template_part( 'template-parts/content-article' );
            endwhile;
            ?>
        </div>

        <?php the_posts_navigation(); ?>

        <?php else : ?>
        <div class="no-results">
            <h2><?php esc_html_e( 'Nothing found', 'nest-and-well' ); ?></h2>
            <p><?php esc_html_e( 'Try searching for something.', 'nest-and-well' ); ?></p>
            <?php get_search_form(); ?>
        </div>
        <?php endif; ?>
    </div><!-- .container -->
</main>

<?php
get_footer();
