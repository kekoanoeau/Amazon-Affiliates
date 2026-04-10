<?php
/**
 * Archive Template
 * Category, tag, and date archive pages.
 * Uses the same hp-feed grid + infinite scroll as the homepage.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();

global $wp_query;

$is_category  = is_category();
$queried_obj  = get_queried_object();
$category_id  = ( $is_category && $queried_obj ) ? (int) $queried_obj->term_id : 0;
$total_pages  = (int) $wp_query->max_num_pages;

// Map category slug → stripe accent color (matches stripe-nav)
$stripe_accent = array(
	'smart-home'    => 'var(--pine)',
	'wellness-tech' => 'var(--sage)',
	'home-beauty'   => 'var(--moss)',
	'gift-guides'   => 'var(--amber)',
	'deals'         => 'var(--clay)',
);

$accent_color = 'var(--forest)';
if ( $is_category && $queried_obj ) {
	$accent_color = isset( $stripe_accent[ $queried_obj->slug ] )
		? $stripe_accent[ $queried_obj->slug ]
		: 'var(--forest)';
}
?>

<main id="main" class="site-main site-main--archive">

	<!-- =============================================
	     ARCHIVE HEADER
	     ============================================= -->
	<div class="archive-hero" style="--archive-accent: <?php echo esc_attr( $accent_color ); ?>;">
		<div class="container">
			<?php nest_well_breadcrumbs(); ?>
			<header class="archive-header">

				<?php if ( $is_category && $queried_obj ) : ?>

				<h1 class="archive-header__title archive-header__title--category">
					<?php echo esc_html( single_cat_title( '', false ) ); ?>
				</h1>

				<?php the_archive_description( '<p class="archive-header__desc">', '</p>' ); ?>

				<p class="archive-header__count">
					<?php
					$cat_count = isset( $queried_obj->count ) ? (int) $queried_obj->count : 0;
					printf(
						/* translators: %d: post count */
						esc_html( _n( '%d Review', '%d Reviews', $cat_count, 'nest-and-well' ) ),
						$cat_count
					);
					?>
				</p>

				<?php else : ?>

				<?php the_archive_title( '<h1 class="archive-header__title">', '</h1>' ); ?>
				<?php the_archive_description( '<p class="archive-header__desc">', '</p>' ); ?>

				<?php endif; ?>

			</header>
		</div>
	</div><!-- .archive-hero -->

	<!-- =============================================
	     ARTICLE FEED — same layout as homepage
	     ============================================= -->
	<div class="hp-feed container">

		<div class="hp-feed__grid" id="hp-feed-grid">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
				<div class="flex-item homepage-style-item">
					<?php get_template_part( 'template-parts/content-article' ); ?>
				</div>
				<?php endwhile; ?>
			<?php else : ?>
			<div class="hp-feed__no-posts" style="grid-column: 1 / -1; text-align: center; padding: var(--space-3xl) 0;">
				<p><?php esc_html_e( 'No articles found in this category yet.', 'nest-and-well' ); ?></p>
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
		     data-per-page="12"
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
				if ( $is_category && $queried_obj ) {
					printf(
						/* translators: %s: category name */
						esc_html__( "You've seen all %s reviews.", 'nest-and-well' ),
						esc_html( single_cat_title( '', false ) )
					);
				} else {
					esc_html_e( "You've seen everything. Nice work.", 'nest-and-well' );
				}
				?>
			</p>
		</div>

	</div><!-- .hp-feed -->

</main><!-- #main -->

<?php get_footer(); ?>
