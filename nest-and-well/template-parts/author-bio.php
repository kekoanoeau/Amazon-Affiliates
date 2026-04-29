<?php
/**
 * Author Bio Template Part
 * Displays author box below single post content.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$author_id     = get_post_field( 'post_author', get_the_ID() );
$author_name   = get_the_author_meta( 'display_name', $author_id );
$author_url    = get_author_posts_url( $author_id );
$author_site   = get_the_author_meta( 'user_url', $author_id );
$author_twitter = get_the_author_meta( 'twitter', $author_id );

if ( ! $author_name ) {
    return;
}
?>

<div class="author-bio" itemscope itemtype="https://schema.org/Person">
    <div class="author-bio__inner">

        <!-- Author Avatar -->
        <div class="author-bio__avatar">
            <?php
            echo get_avatar(
                $author_id,
                80,
                '',
                $author_name,
                array(
                    'class'     => 'author-bio__avatar-img',
                    'loading'   => 'lazy',
                    'itemprop'  => 'image',
                )
            ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
        </div>

        <!-- Author Details -->
        <div class="author-bio__content">
            <div class="author-bio__header">
                <p class="author-bio__label"><?php esc_html_e( 'Written by', 'nest-and-well' ); ?></p>
                <h3 class="author-bio__name" itemprop="name">
                    <a href="<?php echo esc_url( $author_url ); ?>" class="author-bio__name-link" itemprop="url">
                        <?php echo esc_html( $author_name ); ?>
                    </a>
                </h3>

                <?php
                $author_title = get_the_author_meta( 'job_title', $author_id );
                if ( ! $author_title ) {
                    $author_title = get_the_author_meta( 'title', $author_id );
                }
                if ( $author_title ) :
                ?>
                <p class="author-bio__title" itemprop="jobTitle"><?php echo esc_html( $author_title ); ?></p>
                <?php endif; ?>
            </div>

            <!-- Author Social Links -->
            <div class="author-bio__links">
                <a href="<?php echo esc_url( $author_url ); ?>" class="author-bio__link author-bio__link--articles">
                    <?php
                    printf(
                        /* translators: %s: author name */
                        esc_html__( 'More by %s', 'nest-and-well' ),
                        esc_html( $author_name )
                    );
                    ?>
                    &rarr;
                </a>

                <?php if ( $author_site ) : ?>
                <a href="<?php echo esc_url( $author_site ); ?>"
                   class="author-bio__link author-bio__link--website"
                   target="_blank"
                   rel="noopener noreferrer"
                   itemprop="url">
                    <?php esc_html_e( 'Website', 'nest-and-well' ); ?>
                </a>
                <?php endif; ?>

                <?php if ( $author_twitter ) : ?>
                <a href="https://twitter.com/<?php echo esc_attr( ltrim( $author_twitter, '@' ) ); ?>"
                   class="author-bio__link author-bio__link--twitter"
                   target="_blank"
                   rel="noopener noreferrer">
                    <?php esc_html_e( 'Twitter/X', 'nest-and-well' ); ?>
                </a>
                <?php endif; ?>
            </div>

        </div><!-- .author-bio__content -->
    </div><!-- .author-bio__inner -->
</div><!-- .author-bio -->
