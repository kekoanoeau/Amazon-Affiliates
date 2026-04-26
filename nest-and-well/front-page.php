<?php
/**
 * Homepage Template (Front Page)
 *
 * Three-column infinite-scroll article feed.
 * Loads the first 12 posts server-side; subsequent pages are fetched
 * automatically via IntersectionObserver as the user scrolls.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();

$home_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 12,
		'ignore_sticky_posts' => true,
		'post_status'         => 'publish',
	)
);

$total_pages = (int) $home_query->max_num_pages;
?>

<main id="main" class="site-main site-main--home">

	<?php get_template_part( 'template-parts/hero' ); ?>

	<div class="hp-feed container">

		<header class="hp-feed__header">
			<h2 class="hp-feed__title"><?php esc_html_e( 'Latest Reviews', 'nest-and-well' ); ?></h2>
			<p class="hp-feed__subtitle"><?php esc_html_e( 'Fresh from our test bench.', 'nest-and-well' ); ?></p>
		</header>

		<!-- Article grid — initial load -->
		<div class="hp-feed__grid" id="hp-feed-grid">
			<?php if ( $home_query->have_posts() ) : ?>
				<?php while ( $home_query->have_posts() ) : $home_query->the_post(); ?>
				<div class="flex-item homepage-style-item">
					<?php get_template_part( 'template-parts/content-article' ); ?>
				</div>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
			<p class="hp-feed__no-posts">
				<?php esc_html_e( 'No articles found. Check back soon!', 'nest-and-well' ); ?>
			</p>
			<?php endif; ?>
		</div><!-- #hp-feed-grid -->

		<!-- Infinite scroll sentinel — observed by IntersectionObserver -->
		<?php if ( $total_pages > 1 ) : ?>
		<div class="hp-feed__sentinel js-infinite-sentinel"
		     aria-hidden="true"
		     data-page="2"
		     data-per-page="12"
		     data-total-pages="<?php echo esc_attr( $total_pages ); ?>">
		</div>
		<?php endif; ?>

		<!-- Loading indicator \u2014 skeleton card grid, kept under hp-feed__loading for aria-live continuity -->
		<div class="hp-feed__loading js-infinite-loading" hidden aria-live="polite" aria-busy="false">
			<span class="screen-reader-text"><?php esc_html_e( 'Loading more articles\u2026', 'nest-and-well' ); ?></span>
			<div class="hp-feed__skeleton" aria-hidden="true">
				<?php for ( $i = 0; $i < 3; $i++ ) : ?>
				<div class="skeleton-card">
					<div class="skeleton-card__img"></div>
					<div class="skeleton-card__body">
						<div class="skeleton-card__line"></div>
						<div class="skeleton-card__line skeleton-card__line--short"></div>
						<div class="skeleton-card__line skeleton-card__line--cta"></div>
					</div>
				</div>
				<?php endfor; ?>
			</div>
		</div>

		<!-- End-of-feed message -->
		<div class="hp-feed__end js-infinite-end" hidden aria-live="polite">
			<p><?php esc_html_e( "You've read everything. Nice work.", 'nest-and-well' ); ?></p>
		</div>

	</div><!-- .hp-feed -->

	<?php get_template_part( 'template-parts/discovery-feed' ); ?>

</main><!-- #main -->

<?php get_footer(); ?>
