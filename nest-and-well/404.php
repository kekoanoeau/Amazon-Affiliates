<?php
/**
 * 404 Not Found Template
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main site-main--404">
    <div class="container">

        <div class="error-404">
            <div class="error-404__content">
                <div class="error-404__number" aria-hidden="true">404</div>
                <h1 class="error-404__title">
                    <?php esc_html_e( 'Page Not Found', 'nest-and-well' ); ?>
                </h1>
                <p class="error-404__message">
                    <?php esc_html_e( 'The page you\'re looking for might have been moved, renamed, or is temporarily unavailable.', 'nest-and-well' ); ?>
                </p>

                <div class="error-404__actions">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--sage">
                        <?php esc_html_e( 'Go to Homepage', 'nest-and-well' ); ?>
                    </a>
                </div>

                <div class="error-404__search">
                    <p><?php esc_html_e( 'Or try searching for what you need:', 'nest-and-well' ); ?></p>
                    <?php get_search_form(); ?>
                </div>
            </div>

            <div class="error-404__categories">
                <h2><?php esc_html_e( 'Browse Our Reviews', 'nest-and-well' ); ?></h2>
                <div class="error-404__stripe-links">
                    <?php
                    $stripes = array(
                        array(
                            'label' => get_theme_mod( 'nest_well_stripe_1_label', 'Smart Home' ),
                            'url'   => get_theme_mod( 'nest_well_stripe_1_url', '/smart-home/' ),
                            'class' => 'stripe--forest',
                        ),
                        array(
                            'label' => get_theme_mod( 'nest_well_stripe_2_label', 'Wellness Tech' ),
                            'url'   => get_theme_mod( 'nest_well_stripe_2_url', '/wellness-tech/' ),
                            'class' => 'stripe--sage',
                        ),
                        array(
                            'label' => get_theme_mod( 'nest_well_stripe_3_label', 'Home Beauty' ),
                            'url'   => get_theme_mod( 'nest_well_stripe_3_url', '/home-beauty/' ),
                            'class' => 'stripe--moss',
                        ),
                        array(
                            'label' => get_theme_mod( 'nest_well_stripe_4_label', 'Gift Guides' ),
                            'url'   => get_theme_mod( 'nest_well_stripe_4_url', '/gift-guides/' ),
                            'class' => 'stripe--amber',
                        ),
                        array(
                            'label' => get_theme_mod( 'nest_well_stripe_5_label', 'Deals' ),
                            'url'   => get_theme_mod( 'nest_well_stripe_5_url', '/deals/' ),
                            'class' => 'stripe--clay',
                        ),
                    );
                    ?>
                    <?php foreach ( $stripes as $stripe ) : ?>
                    <a href="<?php echo esc_url( $stripe['url'] ); ?>"
                       class="error-404__stripe-link <?php echo esc_attr( $stripe['class'] ); ?>">
                        <?php echo esc_html( $stripe['label'] ); ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <h3><?php esc_html_e( 'Recent Articles', 'nest-and-well' ); ?></h3>
                <?php
                $recent = new WP_Query(
                    array(
                        'posts_per_page' => 4,
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                    )
                );

                if ( $recent->have_posts() ) :
                ?>
                <ul class="error-404__recent-list">
                    <?php
                    while ( $recent->have_posts() ) :
                        $recent->the_post();
                    ?>
                    <li class="error-404__recent-item">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <?php
                wp_reset_postdata();
                endif;
                ?>
            </div>
        </div><!-- .error-404 -->

    </div><!-- .container -->
</main><!-- #main -->

<?php
get_footer();
