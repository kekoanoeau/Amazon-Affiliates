<?php
/**
 * Author Bio Template Part
 * Displays author box below single post content.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$author_id   = get_post_field( 'post_author', get_the_ID() );
$author_name = get_the_author_meta( 'display_name', $author_id );

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
                    'class'    => 'author-bio__avatar-img',
                    'loading'  => 'lazy',
                    'itemprop' => 'image',
                )
            ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
        </div>

        <!-- Author Details -->
        <div class="author-bio__content">
            <div class="author-bio__header">
                <p class="author-bio__label"><?php esc_html_e( 'Written by', 'nest-and-well' ); ?></p>
                <h3 class="author-bio__name" itemprop="name">
                    <?php echo esc_html( $author_name ); ?>
                </h3>
            </div>
        </div><!-- .author-bio__content -->

    </div><!-- .author-bio__inner -->
</div><!-- .author-bio -->
