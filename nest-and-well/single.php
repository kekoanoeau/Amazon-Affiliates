<?php
/**
 * Single Post Template
 * Article/Review template — 2-column layout: 720px content + 300px sticky sidebar.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main site-main--single">

    <?php
    while ( have_posts() ) :
        the_post();

        $post_id      = get_the_ID();
        $review_score  = get_post_meta( $post_id, '_review_score', true );
        $review_badge  = get_post_meta( $post_id, '_review_badge', true );
        $product_name  = get_post_meta( $post_id, '_product_name', true );
        $product_asin  = get_post_meta( $post_id, '_product_asin', true );
        $product_price = get_post_meta( $post_id, '_product_price', true );
        $last_updated  = get_post_meta( $post_id, '_last_updated', true );
        $read_time    = nest_well_get_read_time( $post_id );

        $author_id    = get_post_field( 'post_author', $post_id );
        $author_name  = get_the_author_meta( 'display_name', $author_id );
        $author_url   = get_author_posts_url( $author_id );
        $author_avatar = get_avatar( $author_id, 40, '', $author_name, array( 'class' => 'byline__avatar-img' ) );

        $categories   = get_the_category();
        $primary_cat  = $categories ? $categories[0] : null;
        ?>

    <!-- Article Header (above the fold) -->
    <div class="article-head">
        <div class="container">

            <!-- Article Title -->
            <h1 class="article-head__title"><?php the_title(); ?></h1>

            <!-- Byline Row -->
            <div class="byline" aria-label="<?php esc_attr_e( 'Article information', 'nest-and-well' ); ?>">
                <div class="byline__author">
                    <?php if ( $author_avatar ) : ?>
                    <div class="byline__avatar-link">
                        <?php echo $author_avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                    <?php endif; ?>
                    <div class="byline__meta">
                        <span class="byline__by-line">
                            <?php esc_html_e( 'By', 'nest-and-well' ); ?>
                            <span class="byline__author-name">
                                <?php echo esc_html( $author_name ); ?>
                            </span>
                        </span>
                        <span class="byline__dates">
                            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" class="byline__published">
                                <?php echo esc_html( get_the_date() ); ?>
                            </time>
                            <?php if ( $last_updated ) : ?>
                            &mdash;
                            <span class="byline__updated">
                                <?php esc_html_e( 'Updated', 'nest-and-well' ); ?>
                                <time datetime="<?php echo esc_attr( date( 'c', strtotime( $last_updated ) ) ); ?>">
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

                    <!-- Social Share -->
                    <div class="share-buttons" aria-label="<?php esc_attr_e( 'Share this article', 'nest-and-well' ); ?>">
                        <button type="button"
                                class="share-buttons__item share-buttons__item--native js-native-share"
                                data-share-url="<?php echo esc_attr( get_permalink() ); ?>"
                                data-share-title="<?php echo esc_attr( get_the_title() ); ?>"
                                aria-label="<?php esc_attr_e( 'Share', 'nest-and-well' ); ?>"
                                hidden>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                            </svg>
                        </button>

                        <a href="https://pinterest.com/pin/create/button/?url=<?php echo rawurlencode( get_permalink() ); ?>&description=<?php echo rawurlencode( get_the_title() ); ?>"
                           class="share-buttons__item share-buttons__item--pinterest"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="<?php esc_attr_e( 'Share on Pinterest', 'nest-and-well' ); ?>">P</a>

                        <a href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( get_permalink() ); ?>&text=<?php echo rawurlencode( get_the_title() ); ?>"
                           class="share-buttons__item share-buttons__item--twitter"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="<?php esc_attr_e( 'Share on Twitter', 'nest-and-well' ); ?>">X</a>

                        <button class="share-buttons__item share-buttons__item--copy"
                                data-copy-url="<?php echo esc_attr( get_permalink() ); ?>"
                                aria-label="<?php esc_attr_e( 'Copy link', 'nest-and-well' ); ?>">
                            <?php esc_html_e( 'Copy', 'nest-and-well' ); ?>
                        </button>

                        <?php nest_well_save_button( $post_id, 'inline' ); ?>
                    </div>
                </div>
            </div><!-- .byline -->

            <!-- Inline Affiliate Disclosure (FTC required) -->
            <div class="article-head__disclosure">
                <?php get_template_part( 'template-parts/affiliate-disclosure' ); ?>
            </div>

            <!-- How We Review (review posts only) -->
            <?php if ( $review_score ) : ?>
                <?php get_template_part( 'template-parts/how-we-review' ); ?>
            <?php endif; ?>

        </div><!-- .container -->
    </div><!-- .article-head -->

    <!-- Sticky Buy Bar (review posts with ASIN only) -->
    <?php if ( $product_asin ) : ?>
    <div id="sticky-buy-bar"
         class="sticky-buy-bar"
         aria-hidden="true"
         aria-label="<?php esc_attr_e( 'Buy this product', 'nest-and-well' ); ?>">
        <div class="container">
            <div class="sticky-buy-bar__inner">
                <span class="sticky-buy-bar__title">
                    <?php echo esc_html( $product_name ?: get_the_title() ); ?>
                </span>
                <?php if ( $product_price ) : ?>
                <span class="sticky-buy-bar__price"><?php echo esc_html( $product_price ); ?></span>
                <?php endif; ?>
                <a href="<?php echo esc_url( nest_well_amazon_url( $product_asin ) ); ?>"
                   class="btn sticky-buy-bar__btn btn--sage"
                   target="_blank"
                   rel="nofollow noopener sponsored">
                    <?php esc_html_e( 'Buy on Amazon', 'nest-and-well' ); ?>
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Two-Column Layout: Content + Sidebar -->
    <div class="content-sidebar-wrap">

        <div class="content-area">

            <!-- Featured Image -->
            <?php if ( has_post_thumbnail() ) : ?>
            <div class="article-featured-image">
                <?php the_post_thumbnail( 'hero-image', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'class' => 'article-featured-image__img' ) ); ?>
                <?php
                $caption = get_post( get_post_thumbnail_id() )->post_excerpt;
                if ( $caption ) :
                ?>
                <figcaption class="article-featured-image__caption">
                    <?php echo esc_html( $caption ); ?>
                </figcaption>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Auto Review Summary (review posts only; skipped if shortcode used in content) -->
            <?php if ( $review_score ) : ?>
                <?php get_template_part( 'template-parts/review-summary-meta' ); ?>
            <?php endif; ?>

            <!-- Article Body -->
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'article-body' ); ?>>
                <div class="article-body__content entry-content">
                    <?php the_content(); ?>
                </div>

                <!-- Post Tags -->
                <?php
                $post_tags = get_the_tags();
                if ( $post_tags ) :
                ?>
                <div class="article-tags" aria-label="<?php esc_attr_e( 'Article tags', 'nest-and-well' ); ?>">
                    <?php foreach ( $post_tags as $tag ) : ?>
                    <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>"
                       class="article-tag"
                       rel="tag">
                        <?php echo esc_html( $tag->name ); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Pagination for paginated posts -->
                <?php
                wp_link_pages(
                    array(
                        'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'nest-and-well' ),
                        'after'  => '</div>',
                    )
                );
                ?>
            </article>

            <!-- The Bottom Line / Verdict (review posts only — uses _review_verdict meta) -->
            <?php get_template_part( 'template-parts/verdict' ); ?>

            <!-- Auto FAQ Accordion (from post meta) -->
            <?php get_template_part( 'template-parts/faq-list' ); ?>

            <!-- Prev / next post navigation -->
            <?php
            $prev_post = get_previous_post( true );
            $next_post = get_next_post( true );
            if ( $prev_post || $next_post ) :
            ?>
            <nav class="post-nav" aria-label="<?php esc_attr_e( 'Continue reading', 'nest-and-well' ); ?>">
                <?php if ( $prev_post ) : ?>
                <a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>"
                   class="post-nav__link post-nav__link--prev"
                   rel="prev">
                    <span class="post-nav__direction">&larr; <?php esc_html_e( 'Previous review', 'nest-and-well' ); ?></span>
                    <span class="post-nav__title"><?php echo esc_html( get_the_title( $prev_post ) ); ?></span>
                </a>
                <?php else : ?>
                <span></span>
                <?php endif; ?>

                <?php if ( $next_post ) : ?>
                <a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>"
                   class="post-nav__link post-nav__link--next"
                   rel="next">
                    <span class="post-nav__direction"><?php esc_html_e( 'Next review', 'nest-and-well' ); ?> &rarr;</span>
                    <span class="post-nav__title"><?php echo esc_html( get_the_title( $next_post ) ); ?></span>
                </a>
                <?php else : ?>
                <span></span>
                <?php endif; ?>
            </nav>
            <?php endif; ?>

            <!-- End-of-article newsletter CTA -->
            <?php get_template_part( 'template-parts/newsletter-cta' ); ?>

            <!-- Author Bio -->
            <?php get_template_part( 'template-parts/author-bio' ); ?>

            <!-- Related Posts -->
            <?php get_template_part( 'template-parts/related-posts' ); ?>

            <!-- Comments -->
            <?php if ( comments_open() || get_comments_number() ) : ?>
            <div class="article-comments">
                <?php comments_template(); ?>
            </div>
            <?php endif; ?>

        </div><!-- .content-area -->

        <!-- Sticky Sidebar -->
        <div class="widget-area sidebar-sticky" aria-label="<?php esc_attr_e( 'Article sidebar', 'nest-and-well' ); ?>">
            <?php get_sidebar(); ?>
        </div>

    </div><!-- .content-sidebar-wrap -->

    <?php endwhile; ?>

</main><!-- #main -->

<!-- Back to Top -->
<button id="back-to-top"
        class="back-to-top"
        aria-label="<?php esc_attr_e( 'Back to top', 'nest-and-well' ); ?>">
    &#8593;
</button>

<?php
get_footer();
