<?php
/**
 * Latest Deals Feed
 * Horizontal 4-card strip shown above the main editorial feed on the homepage.
 * Queries posts from any category whose name contains "deal" (case-insensitive).
 * Gracefully renders nothing when the category is empty or absent.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Find the Deals category by slug first, then by partial name.
$deals_cat = get_category_by_slug( 'deals' );
if ( ! $deals_cat ) {
    foreach ( get_categories( array( 'hide_empty' => false ) ) as $cat ) {
        if ( false !== stripos( $cat->name, 'deal' ) ) {
            $deals_cat = $cat;
            break;
        }
    }
}

if ( ! $deals_cat ) {
    return;
}

$deals_query = new WP_Query(
    array(
        'post_type'           => 'post',
        'posts_per_page'      => 4,
        'cat'                 => $deals_cat->term_id,
        'ignore_sticky_posts' => true,
        'post_status'         => 'publish',
    )
);

if ( ! $deals_query->have_posts() ) {
    return;
}

$cat_url = get_category_link( $deals_cat->term_id );
?>

<section class="deals-feed" aria-labelledby="deals-feed-heading">
    <div class="deals-feed__inner container">

        <header class="deals-feed__header">
            <div class="deals-feed__header-text">
                <h2 class="deals-feed__title" id="deals-feed-heading">
                    <?php esc_html_e( 'Latest Deals', 'nest-and-well' ); ?>
                </h2>
                <p class="deals-feed__subtitle">
                    <?php esc_html_e( 'Top picks at the best prices right now.', 'nest-and-well' ); ?>
                </p>
            </div>
            <a href="<?php echo esc_url( $cat_url ); ?>" class="deals-feed__view-all">
                <?php esc_html_e( 'View all deals', 'nest-and-well' ); ?> &rarr;
            </a>
        </header>

        <div class="deals-feed__grid">
            <?php
            while ( $deals_query->have_posts() ) :
                $deals_query->the_post();
                $post_id    = get_the_ID();
                $asin       = get_post_meta( $post_id, '_product_asin', true );
                $price      = get_post_meta( $post_id, '_product_price', true );
                $badge      = get_post_meta( $post_id, '_review_badge', true );
                $is_amazon  = (bool) $asin;
                $cta_url    = $is_amazon ? nest_well_amazon_url( $asin ) : get_permalink();
                $cta_rel    = $is_amazon ? 'nofollow noopener sponsored' : '';
                $cta_target = $is_amazon ? '_blank' : '';
            ?>
            <article class="deal-card" itemscope itemtype="https://schema.org/Product">

                <a href="<?php echo esc_url( $cta_url ); ?>"
                   class="deal-card__image-link<?php echo $is_amazon ? ' amazon-affiliate-link' : ''; ?>"
                   <?php if ( $cta_target ) : ?>target="<?php echo esc_attr( $cta_target ); ?>"<?php endif; ?>
                   <?php if ( $cta_rel ) : ?>rel="<?php echo esc_attr( $cta_rel ); ?>"<?php endif; ?>
                   <?php if ( $is_amazon ) : ?>data-affiliate="amazon" data-product="<?php echo esc_attr( get_the_title() ); ?>"<?php endif; ?>
                   tabindex="-1" aria-hidden="true">
                    <div class="deal-card__image-wrap">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php
                            the_post_thumbnail(
                                'card-thumbnail',
                                array(
                                    'class'    => 'deal-card__image',
                                    'loading'  => 'lazy',
                                    'itemprop' => 'image',
                                    'alt'      => esc_attr( get_the_title() ),
                                )
                            );
                            ?>
                        <?php else : ?>
                        <div class="deal-card__image-placeholder" aria-hidden="true"></div>
                        <?php endif; ?>

                        <?php if ( $badge ) : ?>
                        <span class="deal-card__badge badge badge--<?php echo esc_attr( $badge ); ?>">
                            <?php echo esc_html( nest_well_get_badge_label( $badge ) ); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </a>

                <div class="deal-card__body">
                    <h3 class="deal-card__title" itemprop="name">
                        <a href="<?php the_permalink(); ?>" class="deal-card__title-link">
                            <?php the_title(); ?>
                        </a>
                    </h3>

                    <div class="deal-card__footer">
                        <?php if ( $price ) : ?>
                        <span class="deal-card__price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                            <span itemprop="price"><?php echo esc_html( $price ); ?></span>
                        </span>
                        <?php endif; ?>

                        <a href="<?php echo esc_url( $cta_url ); ?>"
                           class="deal-card__cta btn btn--deal<?php echo $is_amazon ? ' amazon-affiliate-link' : ''; ?>"
                           <?php if ( $cta_target ) : ?>target="<?php echo esc_attr( $cta_target ); ?>"<?php endif; ?>
                           <?php if ( $cta_rel ) : ?>rel="<?php echo esc_attr( $cta_rel ); ?>"<?php endif; ?>
                           <?php if ( $is_amazon ) : ?>data-affiliate="amazon" data-product="<?php echo esc_attr( get_the_title() ); ?>"<?php endif; ?>>
                            <?php if ( $is_amazon ) : ?>
                                <?php esc_html_e( 'SHOP DEAL', 'nest-and-well' ); ?> &#x2197;
                            <?php else : ?>
                                <?php esc_html_e( 'VIEW DEAL', 'nest-and-well' ); ?>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>

            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div><!-- .deals-feed__grid -->

    </div><!-- .deals-feed__inner -->
</section><!-- .deals-feed -->
