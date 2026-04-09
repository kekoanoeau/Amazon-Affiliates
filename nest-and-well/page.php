<?php
/**
 * Static Page Template
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main site-main--page">
    <div class="container">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class( 'page-content' ); ?>>

            <?php if ( ! is_front_page() ) : ?>
            <header class="page-header">
                <?php nest_well_breadcrumbs(); ?>
                <h1 class="page-header__title"><?php the_title(); ?></h1>
            </header>
            <?php endif; ?>

            <?php if ( has_post_thumbnail() ) : ?>
            <div class="page-featured-image">
                <?php the_post_thumbnail( 'hero-image', array( 'loading' => 'lazy', 'class' => 'page-featured-image__img' ) ); ?>
            </div>
            <?php endif; ?>

            <div class="page-content__body entry-content">
                <?php the_content(); ?>
            </div>

            <?php
            wp_link_pages(
                array(
                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'nest-and-well' ),
                    'after'  => '</div>',
                )
            );
            ?>

        </article>

        <?php if ( comments_open() || get_comments_number() ) : ?>
        <div class="page-comments">
            <?php comments_template(); ?>
        </div>
        <?php endif; ?>

        <?php endwhile; ?>
    </div><!-- .container -->
</main><!-- #main -->

<?php
get_footer();
