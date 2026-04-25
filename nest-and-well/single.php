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

            <!-- Breadcrumbs -->
            <?php if ( $primary_cat ) : ?>
            <nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'nest-and-well' ); ?>">
                <ol class="breadcrumbs__list">
                    <li class="breadcrumbs__item">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="breadcrumbs__link">
                            <?php esc_html_e( 'Home', 'nest-and-well' ); ?>
                        </a>
                    </li>
                    <li class="breadcrumbs__item">
                        <a href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>" class="breadcrumbs__link">
                            <?php echo esc_html( $primary_cat->name ); ?>
                        </a>
                    </li>
                    <li class="breadcrumbs__item breadcrumbs__item--current" aria-current="page">
                        <?php echo esc_html( wp_trim_words( get_the_title(), 8, '&hellip;' ) ); ?>
                    </li>
                </ol>
            </nav>
            <?php endif; ?>

            <!-- Article Title -->
            <h1 class="article-head__title"><?php the_title(); ?></h1>

            <!-- Byline Row -->
            <div class="byline" aria-label="<?php esc_attr_e( 'Article information', 'nest-and-well' ); ?>">
                <div class="byline__author">
                    <?php if ( $author_avatar ) : ?>
                    <a href="<?php echo esc_url( $author_url ); ?>" class="byline__avatar-link">
                        <?php echo $author_avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </a>
                    <?php endif; ?>
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
                    </div>
                </div>
            </div><!-- .byline -->

            <!-- Inline Affiliate Disclosure (FTC required) -->
            <div class="article-head__disclosure">
                <?php get_template_part( 'template-parts/affiliate-disclosure' ); ?>
            </div>

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
