<?php
/**
 * Homepage Template (Front Page)
 *
 * 6 sections in order:
 *   1. Hero Section
 *   2. Affiliate Disclosure Banner
 *   3. Tab-Filtered Product Grid (Newest | Popular | etc.)
 *   4. Category Showcase (4 alternating sections)
 *   5. Trust Signals Row
 *   6. Email Optin Section
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main site-main--home">

    <!-- =========================================================
         SECTION 1: HERO
         ========================================================= -->
    <section class="home-hero" aria-labelledby="hero-headline">
        <div class="home-hero__inner">
            <div class="home-hero__content">
                <h1 class="home-hero__headline" id="hero-headline">
                    <?php echo esc_html( get_theme_mod( 'nest_well_hero_headline', 'Smart Home & Wellness, Thoughtfully Reviewed' ) ); ?>
                </h1>
                <p class="home-hero__subtext">
                    <?php echo esc_html( get_theme_mod( 'nest_well_hero_subtext', 'We test products in real homes so you can shop with confidence.' ) ); ?>
                </p>
                <div class="home-hero__ctas">
                    <a href="<?php echo esc_url( get_theme_mod( 'nest_well_cta_primary_url', '/smart-home/' ) ); ?>"
                       class="btn btn--primary">
                        <?php echo esc_html( get_theme_mod( 'nest_well_cta_primary_label', 'Browse Reviews' ) ); ?>
                    </a>
                    <a href="<?php echo esc_url( get_theme_mod( 'nest_well_cta_secondary_url', '/wellness-tech/' ) ); ?>"
                       class="btn btn--secondary">
                        <?php echo esc_html( get_theme_mod( 'nest_well_cta_secondary_label', 'Start with Wellness' ) ); ?>
                    </a>
                </div>
            </div>

            <!-- Category Tiles (desktop only) -->
            <?php if ( get_theme_mod( 'nest_well_show_category_grid', true ) ) : ?>
            <div class="home-hero__tiles" aria-label="<?php esc_attr_e( 'Featured categories', 'nest-and-well' ); ?>">
                <?php
                $hero_tiles = array(
                    array(
                        'label' => get_theme_mod( 'nest_well_stripe_1_label', 'Smart Home' ),
                        'url'   => get_theme_mod( 'nest_well_stripe_1_url', '/smart-home/' ),
                    ),
                    array(
                        'label' => get_theme_mod( 'nest_well_stripe_2_label', 'Wellness Tech' ),
                        'url'   => get_theme_mod( 'nest_well_stripe_2_url', '/wellness-tech/' ),
                    ),
                    array(
                        'label' => get_theme_mod( 'nest_well_stripe_3_label', 'Home Beauty' ),
                        'url'   => get_theme_mod( 'nest_well_stripe_3_url', '/home-beauty/' ),
                    ),
                    array(
                        'label' => get_theme_mod( 'nest_well_stripe_4_label', 'Gift Guides' ),
                        'url'   => get_theme_mod( 'nest_well_stripe_4_url', '/gift-guides/' ),
                    ),
                    array(
                        'label' => get_theme_mod( 'nest_well_stripe_5_label', 'Deals' ),
                        'url'   => get_theme_mod( 'nest_well_stripe_5_url', '/deals/' ),
                    ),
                );
                foreach ( $hero_tiles as $tile ) :
                ?>
                <a href="<?php echo esc_url( $tile['url'] ); ?>" class="home-hero__tile">
                    <span class="home-hero__tile-label"><?php echo esc_html( $tile['label'] ); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div><!-- .home-hero__inner -->
    </section><!-- .home-hero -->

    <!-- =========================================================
         SECTION 2: AFFILIATE DISCLOSURE BANNER
         ========================================================= -->
    <?php if ( get_theme_mod( 'nest_well_show_disclosure_homepage', true ) ) : ?>
    <?php get_template_part( 'template-parts/affiliate-disclosure' ); ?>
    <?php endif; ?>

    <!-- =========================================================
         SECTION 3: TAB-FILTERED PRODUCT GRID
         ========================================================= -->
    <section class="home-articles" aria-labelledby="home-articles-heading">
        <div class="container">
            <h2 class="home-articles__heading" id="home-articles-heading">
                <?php esc_html_e( 'Latest Reviews', 'nest-and-well' ); ?>
            </h2>

            <!-- Filter Tabs -->
            <div class="article-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Filter reviews', 'nest-and-well' ); ?>">
                <button class="article-tabs__tab is-active"
                        role="tab"
                        aria-selected="true"
                        data-filter="newest"
                        id="tab-newest"
                        aria-controls="tab-panel-articles">
                    <?php esc_html_e( 'Newest', 'nest-and-well' ); ?>
                </button>
                <button class="article-tabs__tab"
                        role="tab"
                        aria-selected="false"
                        data-filter="popular"
                        id="tab-popular"
                        aria-controls="tab-panel-articles">
                    <?php esc_html_e( 'Popular', 'nest-and-well' ); ?>
                </button>
                <button class="article-tabs__tab"
                        role="tab"
                        aria-selected="false"
                        data-filter="top-rated"
                        id="tab-top-rated"
                        aria-controls="tab-panel-articles">
                    <?php esc_html_e( 'Top Rated', 'nest-and-well' ); ?>
                </button>
                <button class="article-tabs__tab"
                        role="tab"
                        aria-selected="false"
                        data-filter="under-100"
                        id="tab-under-100"
                        aria-controls="tab-panel-articles">
                    <?php esc_html_e( 'Gifts Under $100', 'nest-and-well' ); ?>
                </button>
                <button class="article-tabs__tab"
                        role="tab"
                        aria-selected="false"
                        data-filter="editors-choice"
                        id="tab-editors-choice"
                        aria-controls="tab-panel-articles">
                    <?php esc_html_e( "Editor's Choice", 'nest-and-well' ); ?>
                </button>
            </div><!-- .article-tabs -->

            <!-- Article Grid -->
            <div class="article-grid article-grid--3col" id="tab-panel-articles" role="tabpanel">
                <?php
                $home_query = new WP_Query(
                    array(
                        'post_type'           => 'post',
                        'posts_per_page'      => 9,
                        'ignore_sticky_posts' => true,
                    )
                );

                if ( $home_query->have_posts() ) :
                    while ( $home_query->have_posts() ) :
                        $home_query->the_post();

                        // Build data attributes for JS tab filtering
                        $data_attrs = 'data-filter-newest="true"';

                        $comment_count = get_comments_number();
                        if ( $comment_count > 5 ) {
                            $data_attrs .= ' data-filter-popular="true"';
                        }

                        $post_score = get_post_meta( get_the_ID(), '_review_score', true );
                        if ( $post_score && $post_score >= 8.5 ) {
                            $data_attrs .= ' data-filter-top-rated="true"';
                        }

                        $post_badge = get_post_meta( get_the_ID(), '_review_badge', true );
                        if ( 'editors-choice' === $post_badge ) {
                            $data_attrs .= ' data-filter-editors-choice="true"';
                        }

                        $post_price = get_post_meta( get_the_ID(), '_product_price', true );
                        if ( $post_price && (float) preg_replace( '/[^0-9.]/', '', $post_price ) < 100 ) {
                            $data_attrs .= ' data-filter-under-100="true"';
                        }
                        ?>
                        <div class="article-grid__item" <?php echo $data_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                            <?php get_template_part( 'template-parts/content-article' ); ?>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div><!-- .article-grid -->

            <!-- Load More Button -->
            <div class="home-articles__load-more">
                <button class="btn btn--sage-outline js-load-more"
                        data-page="2"
                        data-per-page="9"
                        aria-label="<?php esc_attr_e( 'Load more reviews', 'nest-and-well' ); ?>">
                    <?php esc_html_e( 'Load More Reviews', 'nest-and-well' ); ?>
                </button>
                <div class="js-load-more-spinner" aria-live="polite" aria-busy="false" hidden>
                    <?php esc_html_e( 'Loading...', 'nest-and-well' ); ?>
                </div>
            </div>

        </div><!-- .container -->
    </section><!-- .home-articles -->

    <!-- =========================================================
         SECTION 4: CATEGORY SHOWCASE
         ========================================================= -->
    <section class="category-showcase" aria-labelledby="category-showcase-heading">
        <h2 class="screen-reader-text" id="category-showcase-heading">
            <?php esc_html_e( 'Featured Categories', 'nest-and-well' ); ?>
        </h2>

        <?php
        $showcase_cats = array(
            array(
                'name'    => get_theme_mod( 'nest_well_stripe_1_label', 'Smart Home' ),
                'url'     => get_theme_mod( 'nest_well_stripe_1_url', '/smart-home/' ),
                'desc'    => __( 'Discover the best smart home devices tested in real homes. From smart speakers to robot vacuums, we review what actually works.', 'nest-and-well' ),
                'subs'    => array( 'Smart Speakers', 'Smart Displays', 'Robot Vacuums', 'Smart Security', 'Smart Lighting', 'Smart Thermostats' ),
                'bg'      => 'var(--white)',
            ),
            array(
                'name'    => get_theme_mod( 'nest_well_stripe_2_label', 'Wellness Tech' ),
                'url'     => get_theme_mod( 'nest_well_stripe_2_url', '/wellness-tech/' ),
                'desc'    => __( 'From fitness trackers to sleep technology, we review the wellness devices that help you live healthier and feel better every day.', 'nest-and-well' ),
                'subs'    => array( 'Fitness Trackers', 'Sleep Tech', 'Air Purifiers', 'Massage Guns', 'Smart Scales', 'Meditation Devices' ),
                'bg'      => 'var(--lt-stone)',
            ),
            array(
                'name'    => get_theme_mod( 'nest_well_stripe_3_label', 'Home Beauty' ),
                'url'     => get_theme_mod( 'nest_well_stripe_3_url', '/home-beauty/' ),
                'desc'    => __( 'Bring spa-quality beauty and personal care into your home. We test the devices that deliver real results.', 'nest-and-well' ),
                'subs'    => array( 'Hair Tools', 'Skin Devices', 'Oral Care', 'Body Care', 'Aromatherapy', 'Light Therapy' ),
                'bg'      => 'var(--white)',
            ),
            array(
                'name'    => get_theme_mod( 'nest_well_stripe_4_label', 'Gift Guides' ),
                'url'     => get_theme_mod( 'nest_well_stripe_4_url', '/gift-guides/' ),
                'desc'    => __( 'The best tech gifts for every budget and every person in your life. Curated by our editors from hands-on testing.', 'nest-and-well' ),
                'subs'    => array( 'Gifts Under $50', 'Gifts Under $100', 'Gifts Under $200', 'Gifts for Him', 'Gifts for Her', 'Gifts for Parents' ),
                'bg'      => 'var(--lt-stone)',
            ),
        );

        foreach ( $showcase_cats as $i => $cat ) :
            $is_odd = ( 0 === $i % 2 );
        ?>
        <div class="category-showcase__section <?php echo $is_odd ? 'category-showcase__section--normal' : 'category-showcase__section--reverse'; ?>"
             style="background-color: <?php echo esc_attr( $cat['bg'] ); ?>;">
            <div class="container">
                <div class="category-showcase__inner">
                    <div class="category-showcase__image">
                        <div class="category-showcase__image-placeholder">
                            <span class="category-showcase__cat-name"><?php echo esc_html( $cat['name'] ); ?></span>
                        </div>
                    </div>
                    <div class="category-showcase__content">
                        <h2 class="category-showcase__title"><?php echo esc_html( $cat['name'] ); ?></h2>
                        <p class="category-showcase__desc"><?php echo esc_html( $cat['desc'] ); ?></p>
                        <?php if ( ! empty( $cat['subs'] ) ) : ?>
                        <ul class="category-showcase__subs">
                            <?php foreach ( $cat['subs'] as $sub ) : ?>
                            <li><?php echo esc_html( $sub ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        <a href="<?php echo esc_url( $cat['url'] ); ?>" class="category-showcase__link">
                            <?php
                            printf(
                                /* translators: %s: category name */
                                esc_html__( 'See all %s reviews', 'nest-and-well' ),
                                esc_html( $cat['name'] )
                            );
                            ?>
                            &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </section><!-- .category-showcase -->

    <!-- =========================================================
         SECTION 5: TRUST SIGNALS
         ========================================================= -->
    <section class="trust-signals" aria-labelledby="trust-signals-heading">
        <div class="container">
            <h2 class="screen-reader-text" id="trust-signals-heading">
                <?php esc_html_e( 'Why Trust Nest & Well', 'nest-and-well' ); ?>
            </h2>
            <div class="trust-signals__grid">

                <div class="trust-signal">
                    <div class="trust-signal__icon" aria-hidden="true">
                        <span class="trust-icon trust-icon--home">&#8962;</span>
                    </div>
                    <h3 class="trust-signal__title"><?php esc_html_e( 'Tested in Real Homes', 'nest-and-well' ); ?></h3>
                    <p class="trust-signal__desc">
                        <?php esc_html_e( 'Every product is tested by our editors in actual home environments — not in labs.', 'nest-and-well' ); ?>
                    </p>
                </div>

                <div class="trust-signal">
                    <div class="trust-signal__icon" aria-hidden="true">
                        <span class="trust-icon trust-icon--shield">&#9632;</span>
                    </div>
                    <h3 class="trust-signal__title"><?php esc_html_e( 'Honest Scores, Always', 'nest-and-well' ); ?></h3>
                    <p class="trust-signal__desc">
                        <?php esc_html_e( 'Our ratings reflect real-world performance. We call out the bad as clearly as the good.', 'nest-and-well' ); ?>
                    </p>
                </div>

                <div class="trust-signal">
                    <div class="trust-signal__icon" aria-hidden="true">
                        <span class="trust-icon trust-icon--no">&#8856;</span>
                    </div>
                    <h3 class="trust-signal__title"><?php esc_html_e( 'No Sponsored Placements', 'nest-and-well' ); ?></h3>
                    <p class="trust-signal__desc">
                        <?php esc_html_e( 'Brands cannot pay for coverage. Every recommendation is editorially independent.', 'nest-and-well' ); ?>
                    </p>
                </div>

            </div><!-- .trust-signals__grid -->
        </div><!-- .container -->
    </section><!-- .trust-signals -->

    <!-- =========================================================
         SECTION 6: EMAIL OPTIN
         ========================================================= -->
    <section class="email-optin" aria-labelledby="email-optin-heading">
        <div class="container">
            <div class="email-optin__inner">
                <h2 class="email-optin__heading" id="email-optin-heading">
                    <?php esc_html_e( "Get the Week's Best Amazon Finds", 'nest-and-well' ); ?>
                </h2>
                <p class="email-optin__subtext">
                    <?php esc_html_e( 'No spam. Just the deals and reviews worth your time.', 'nest-and-well' ); ?>
                </p>

                <!-- MailerLite-compatible form structure -->
                <form class="email-optin__form"
                      action="#"
                      method="post"
                      data-form-type="mailerlite"
                      novalidate>
                    <?php wp_nonce_field( 'nest_well_email_signup', 'email_signup_nonce' ); ?>
                    <div class="email-optin__form-row">
                        <div class="email-optin__field-group">
                            <label for="optin-first-name" class="screen-reader-text">
                                <?php esc_html_e( 'First name', 'nest-and-well' ); ?>
                            </label>
                            <input type="text"
                                   id="optin-first-name"
                                   name="fields[name]"
                                   class="email-optin__input"
                                   placeholder="<?php esc_attr_e( 'First name', 'nest-and-well' ); ?>"
                                   autocomplete="given-name">
                        </div>
                        <div class="email-optin__field-group">
                            <label for="optin-email" class="screen-reader-text">
                                <?php esc_html_e( 'Email address', 'nest-and-well' ); ?>
                            </label>
                            <input type="email"
                                   id="optin-email"
                                   name="fields[email]"
                                   class="email-optin__input"
                                   placeholder="<?php esc_attr_e( 'Your email address', 'nest-and-well' ); ?>"
                                   required
                                   autocomplete="email">
                        </div>
                        <button type="submit" class="email-optin__submit btn btn--sage">
                            <?php esc_html_e( 'Subscribe', 'nest-and-well' ); ?>
                        </button>
                    </div>
                </form>

                <p class="email-optin__privacy">
                    <?php esc_html_e( 'We never share your email. Unsubscribe anytime.', 'nest-and-well' ); ?>
                </p>
            </div><!-- .email-optin__inner -->
        </div><!-- .container -->
    </section><!-- .email-optin -->

</main><!-- #main -->

<?php
get_footer();
