<?php
/**
 * Discovery Card Template Part
 *
 * Thin wrapper that delegates to nest_well_render_discovery_card().
 * The full implementation lives in inc/discovery-feed.php so the same
 * markup is used by the initial server render and the REST endpoint
 * that powers infinite-scroll appends — guarantees affiliate
 * attributes and Schema.org microdata never drift between code paths.
 *
 * Required $args:
 *   post_id (int) — Post ID to render. Falls back to current loop post.
 *
 * @package nest-and-well
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'nest_well_render_discovery_card' ) ) {
    return;
}

$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();

if ( ! $post_id ) {
    return;
}

echo nest_well_render_discovery_card( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — escaped in helper
