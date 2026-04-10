<?php
/**
 * Template Name: Category Feed
 * Template Post Type: page
 *
 * Assign this template to any Page whose slug matches a WordPress category slug
 * (e.g. a page with slug "smart-home" will automatically surface the Smart Home category).
 *
 * Renders the identical hp-feed grid + IntersectionObserver infinite scroll
 * used on the homepage, filtered to the matching category.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ----------------------------------------------------------------
// Resolve category from page slug (primary) or page title (fallback)
// ----------------------------------------------------------------
$page_slug   = get_post_field( 'post_name', get_the_ID() );
$category    = get_category_by_slug( $page_slug );

if ( ! $category || is_wp_error( $category ) ) {
	$category = get_term_by( 'name', get_the_title(), 'category' );
}

$category_id   = ( $category && ! is_wp_error( $category ) ) ? (int) $category->term_id : 0;
$category_name = ( $category && ! is_wp_error( $category ) ) ? $category->name : get_the_title();
$category_desc = ( $category && ! is_wp_error( $category ) ) ? $category->description : '';
$category_cnt  = ( $category && ! is_wp_error( $category ) ) ? (int) $category->count : 0;

$per_page = 12;

// ----------------------------------------------------------------
// Query: first page of posts for this category
// ----------------------------------------------------------------
$query_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => $per_page,
	'ignore_sticky_posts' => true,
	'paged'               => 1,
);

if ( $category_id ) {
	$query_args['cat'] = $category_id;
}

$feed_query  = new WP_Query( $query_args );
$total_pages = (int) $feed_query->max_num_pages;

// ----------------------------------------------------------------
// Stripe accent color map — matches stripe-nav colors
// ----------------------------------------------------------------
$stripe_accent = array(
	'smart-home'    => 'var(--pine)',
	'wellness-tech' => 'var(--sage)',
	'home-beauty'   => 'var(--moss)',
	'gift-guides'   => 'var(--amber)',
	'deals'         => 'var(--clay)',
);

$accent_color = isset( $stripe_accent[ $page_slug ] ) ? $stripe_accent[ $page_slug ] : 'var(--forest)';
?>

<main id="main" class="site-main site-main--archive">

	<!-- =============================================
	     CATEGORY HEADER
	     ============================================= -->
	<div class="archive-hero" style="--archive-accent: <?php echo esc_attr( $accent_color ); ?>;">
		<div class="container">
			<?php nest_well_breadcrumbs(); ?>
			<header class="archive-header">

				<h1 class="archive-header__title archive-header__title--category">
					<?php echo esc_html( $category_name ); ?>
				</h1>

				<?php if ( $category_desc ) : ?>
				<p class="archive-header__desc"><?php echo esc_html( $category_desc ); ?></p>
				<?php endif; ?>

				<?php if ( $category_cnt ) : ?>
				<p class="archive-header__count">
					<?php
					printf(
						/* translators: %d: review count */
						esc_html( _n( '%d Review', '%d Reviews', $category_cnt, 'nest-and-well' ) ),
						$category_cnt
					);
					?>
				</p>
				<?php endif; ?>

			</header>
		</div>
	</div><!-- .archive-hero -->

	<!-- =============================================
	     ARTICLE FEED — identical to homepage
	     ============================================= -->
	<div class="hp-feed container">

		<div class="hp-feed__grid" id="hp-feed-grid">
			<?php if ( $feed_query->have_posts() ) : ?>
				<?php while ( $feed_query->have_posts() ) : $feed_query->the_post(); ?>
				<div class="flex-item homepage-style-item">
					<?php get_template_part( 'template-parts/content-article' ); ?>
				</div>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
			<div class="hp-feed__no-posts" style="grid-column: 1 / -1; text-align: center; padding: var(--space-3xl) 0;">
				<p>
					<?php
					printf(
						/* translators: %s: category name */
						esc_html__( 'No %s reviews yet — check back soon!', 'nest-and-well' ),
						esc_html( $category_name )
					);
					?>
				</p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">
					<?php esc_html_e( 'Browse All Reviews', 'nest-and-well' ); ?>
				</a>
			</div>
			<?php endif; ?>
		</div><!-- #hp-feed-grid -->

		<!-- Infinite scroll sentinel -->
		<?php if ( $total_pages > 1 ) : ?>
		<div class="hp-feed__sentinel js-infinite-sentinel"
		     aria-hidden="true"
		     data-page="2"
		     data-per-page="<?php echo esc_attr( $per_page ); ?>"
		     data-total-pages="<?php echo esc_attr( $total_pages ); ?>"
		     <?php if ( $category_id ) : ?>data-category-id="<?php echo esc_attr( $category_id ); ?>"<?php endif; ?>>
		</div>
		<?php endif; ?>

		<!-- Loading indicator -->
		<div class="hp-feed__loading js-infinite-loading" hidden aria-live="polite" aria-busy="false">
			<span class="hp-feed__spinner" aria-hidden="true"></span>
			<?php esc_html_e( 'Loading more reviews\u2026', 'nest-and-well' ); ?>
		</div>

		<!-- End-of-feed message -->
		<div class="hp-feed__end js-infinite-end" hidden aria-live="polite">
			<p>
				<?php
				printf(
					/* translators: %s: category name */
					esc_html__( "You've seen all %s reviews.", 'nest-and-well' ),
					esc_html( $category_name )
				);
				?>
			</p>
		</div>

	</div><!-- .hp-feed -->

</main><!-- #main -->

<?php get_footer(); ?>
