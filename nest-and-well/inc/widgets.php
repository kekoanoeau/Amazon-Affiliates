<?php
/**
 * Widget Area Registration for Nest & Well
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register widget areas (sidebars).
 */
function nest_well_widgets_init() {

    // Default sidebar (for sidebar.php)
    register_sidebar(
        array(
            'name'          => esc_html__( 'Sidebar', 'nest-and-well' ),
            'id'            => 'sidebar-1',
            'description'   => esc_html__( 'Main sidebar — appears on single posts.', 'nest-and-well' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        )
    );

    // Email Signup widget area
    register_sidebar(
        array(
            'name'          => esc_html__( 'Email Signup', 'nest-and-well' ),
            'id'            => 'sidebar-email-signup',
            'description'   => esc_html__( 'Compact email signup form for sidebar.', 'nest-and-well' ),
            'before_widget' => '<div id="%1$s" class="widget widget--email-signup %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        )
    );

    // Top Picks widget area
    register_sidebar(
        array(
            'name'          => esc_html__( 'Top Picks', 'nest-and-well' ),
            'id'            => 'sidebar-top-picks',
            'description'   => esc_html__( 'Show top posts from current category.', 'nest-and-well' ),
            'before_widget' => '<div id="%1$s" class="widget widget--top-picks %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        )
    );

    // Deal Alert widget area
    register_sidebar(
        array(
            'name'          => esc_html__( 'Deal Alert', 'nest-and-well' ),
            'id'            => 'sidebar-deal-alert',
            'description'   => esc_html__( 'Custom HTML area for Amazon affiliate deal boxes.', 'nest-and-well' ),
            'before_widget' => '<div id="%1$s" class="widget widget--deal-alert %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        )
    );

    // About blurb widget area
    register_sidebar(
        array(
            'name'          => esc_html__( 'About Nest & Well', 'nest-and-well' ),
            'id'            => 'sidebar-about',
            'description'   => esc_html__( 'Short about blurb for sidebar.', 'nest-and-well' ),
            'before_widget' => '<div id="%1$s" class="widget widget--about %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        )
    );

    // Footer widget area 1 — Smart Home links
    register_sidebar(
        array(
            'name'          => esc_html__( 'Footer — Smart Home', 'nest-and-well' ),
            'id'            => 'footer-smart-home',
            'description'   => esc_html__( 'Footer column: Smart Home category links.', 'nest-and-well' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="footer-widget-title">',
            'after_title'   => '</h4>',
        )
    );

    // Footer widget area 2 — Wellness links
    register_sidebar(
        array(
            'name'          => esc_html__( 'Footer — Wellness', 'nest-and-well' ),
            'id'            => 'footer-wellness',
            'description'   => esc_html__( 'Footer column: Wellness category links.', 'nest-and-well' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="footer-widget-title">',
            'after_title'   => '</h4>',
        )
    );

    // Footer widget area 3 — About & Legal
    register_sidebar(
        array(
            'name'          => esc_html__( 'Footer — About & Legal', 'nest-and-well' ),
            'id'            => 'footer-about-legal',
            'description'   => esc_html__( 'Footer column: About, Contact, Legal links.', 'nest-and-well' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="footer-widget-title">',
            'after_title'   => '</h4>',
        )
    );
}
add_action( 'widgets_init', 'nest_well_widgets_init' );

/**
 * Output default sidebar content when no widget is added.
 *
 * @param string $sidebar_id Sidebar ID.
 * @return bool Whether the sidebar has widgets.
 */
function nest_well_sidebar_has_widgets( $sidebar_id ) {
    return is_active_sidebar( $sidebar_id );
}

/**
 * Output the Top Picks widget content (most recent posts from same category).
 *
 * @param int $post_id Current post ID.
 */
function nest_well_top_picks_default( $post_id ) {
    $categories = get_the_category( $post_id );

    if ( empty( $categories ) ) {
        return;
    }

    $cat_id = $categories[0]->term_id;

    $args = array(
        'post_type'           => 'post',
        'posts_per_page'      => 5,
        'cat'                 => $cat_id,
        'post__not_in'        => array( $post_id ),
        'ignore_sticky_posts' => true,
        'orderby'             => 'date',
        'order'               => 'DESC',
    );

    $top_picks = new WP_Query( $args );

    if ( ! $top_picks->have_posts() ) {
        return;
    }

    echo '<div class="top-picks-list">';

    while ( $top_picks->have_posts() ) {
        $top_picks->the_post();
        $score = get_post_meta( get_the_ID(), '_review_score', true );
        ?>
        <a href="<?php the_permalink(); ?>" class="top-picks-item">
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="top-picks-item__thumb">
                    <?php the_post_thumbnail( 'thumbnail', array( 'loading' => 'lazy' ) ); ?>
                </div>
            <?php endif; ?>
            <div class="top-picks-item__content">
                <span class="top-picks-item__title"><?php the_title(); ?></span>
                <?php if ( $score ) : ?>
                    <span class="top-picks-item__score"><?php echo esc_html( $score ); ?>/10</span>
                <?php endif; ?>
            </div>
        </a>
        <?php
    }

    wp_reset_postdata();
    echo '</div>';
}
