<?php
/**
 * Sidebar — Latest Deals Widget
 *
 * Vertical list of up to 4 deal cards (image left, title + price + CTA right).
 * Reuses deal-card markup with a --sidebar modifier for the compact layout.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

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

$sidebar_deals = new WP_Query(
    array(
        'post_type'           => 'post',
        'posts_per_page'      => 4,
        'cat'                 => $deals_cat->term_id,
        'post__not_in'        => array( get_the_ID() ),
        'ignore_sticky_posts' => true,
        'post_status'         => 'publish',
    )
);

if ( ! $sidebar_deals->have_posts() ) {
    return;
}

$cat_url = get_category_link( $deals_cat->term_id );
?>

<div class="widget widget--deals">

    <div class="widget--deals__header">
        <h4 class="widget-title widget--deals__title">
            <?php esc_html_e( 'Latest Deals', 'nest-and-well' ); ?>
        </h4>
        <a href="<?php echo esc_url( $cat_url ); ?>" class="widget--deals__view-all">
            <?php esc_html_e( 'View all', 'nest-and-well' ); ?> &rarr;
        </a>
    </div>

    <ul class="sidebar-deals-list" role="list">
        <?php
        while ( $sidebar_deals->have_posts() ) :
            $sidebar_deals->the_post();
            $post_id    = get_the_ID();
            $asin       = get_post_meta( $post_id, '_product_asin', true );
            $price      = get_post_meta( $post_id, '_product_price', true );
            $badge      = get_post_meta( $post_id, '_review_badge', true );
            $is_amazon  = (bool) $asin;
            $cta_url    = $is_amazon ? nest_well_amazon_url( $asin ) : get_permalink();
            $cta_rel    = $is_amazon ? 'nofollow noopener sponsored' : '';
            $cta_target = $is_amazon ? '_blank' : '';
        ?>
        <li class="sidebar-deals-list__item">
            <article class="deal-card deal-card--sidebar" itemscope itemtype="https://schema.org/Product">

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
                                'thumbnail',
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
        </li>
        <?php endwhile; wp_reset_postdata(); ?>
    </ul>

</div><!-- .widget--deals -->
