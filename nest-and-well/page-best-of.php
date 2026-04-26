<?php
/**
 * Template Name: Best Of (Buying Guide)
 *
 * Editorial buying-guide layout for evergreen "Best X for Y" pages.
 * Authors compose the body using the standard editor + the [quick_picks]
 * and [comparison_table] shortcodes; this template wraps the content in a
 * 2-column layout with the existing sticky sidebar and emits ItemList
 * schema for SERP carousels.
 *
 * Optional post meta (set in Custom Fields):
 *   _guide_intro_eyebrow  Short kicker above the title (e.g. "Smart Home Buying Guide")
 *   _guide_updated        ISO date string used as visible "Updated" label + dateModified
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();

while ( have_posts() ) :
    the_post();
    $post_id      = get_the_ID();
    $eyebrow      = get_post_meta( $post_id, '_guide_intro_eyebrow', true );
    $last_updated = get_post_meta( $post_id, '_guide_updated', true );
    if ( ! $last_updated ) {
        $last_updated = get_post_meta( $post_id, '_last_updated', true );
    }
    $read_time = nest_well_get_read_time( $post_id );

    $author_id     = get_post_field( 'post_author', $post_id );
    $author_name   = get_the_author_meta( 'display_name', $author_id );
    $author_url    = get_author_posts_url( $author_id );
    ?>

    <main id="main" class="site-main site-main--single">

        <!-- Guide Header -->
        <div class="article-head guide-head">
            <div class="container">

                <?php nest_well_breadcrumbs(); ?>

                <?php if ( $eyebrow ) : ?>
                <p class="guide-head__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
                <?php endif; ?>

                <h1 class="article-head__title"><?php the_title(); ?></h1>

                <div class="byline" aria-label="<?php esc_attr_e( 'Article information', 'nest-and-well' ); ?>">
                    <div class="byline__author">
                        <div class="byline__meta">
                            <span class="byline__by-line">
                                <?php esc_html_e( 'By', 'nest-and-well' ); ?>
                                <a href="<?php echo esc_url( $author_url ); ?>" class="byline__author-name">
                                    <?php echo esc_html( $author_name ); ?>
                                </a>
                            </span>
                            <span class="byline__dates">
                                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" class="byline__published">
                                    <?php echo esc_html( get_the_date() ); ?>
                                </time>
                                <?php if ( $last_updated ) : ?>
                                &mdash;
                                <span class="byline__updated">
                                    <?php esc_html_e( 'Updated', 'nest-and-well' ); ?>
                                    <time datetime="<?php echo esc_attr( gmdate( 'c', strtotime( $last_updated ) ) ); ?>">
                                        <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $last_updated ) ) ); ?>
                                    </time>
                                </span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <div class="byline__secondary">
                        <span class="byline__read-time">
                            <?php
                            printf(
                                /* translators: %d: minutes */
                                esc_html( _n( '%d min read', '%d min read', $read_time, 'nest-and-well' ) ),
                                (int) $read_time
                            );
                            ?>
                        </span>

                        <?php nest_well_save_button( $post_id, 'inline' ); ?>
                    </div>
                </div>

                <div class="article-head__disclosure">
                    <?php get_template_part( 'template-parts/affiliate-disclosure' ); ?>
                </div>

                <?php get_template_part( 'template-parts/how-we-review' ); ?>

            </div>
        </div>

        <!-- 2-column body -->
        <div class="content-sidebar-wrap">

            <div class="content-area">

                <?php if ( has_post_thumbnail() ) : ?>
                <div class="article-featured-image">
                    <?php the_post_thumbnail( 'hero-image', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'class' => 'article-featured-image__img' ) ); ?>
                </div>
                <?php endif; ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class( 'article-body article-body--guide' ); ?>>
                    <div class="article-body__content entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>

                <?php get_template_part( 'template-parts/faq-list' ); ?>

                <?php get_template_part( 'template-parts/author-bio' ); ?>

                <?php get_template_part( 'template-parts/related-posts' ); ?>

            </div>

            <div class="widget-area sidebar-sticky" aria-label="<?php esc_attr_e( 'Guide sidebar', 'nest-and-well' ); ?>">
                <?php get_sidebar(); ?>
            </div>

        </div>

    </main>

    <?php
endwhile;

get_footer();
