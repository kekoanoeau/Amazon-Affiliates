<?php
/**
 * Discovery Feed Template Part
 *
 * Thin wrapper that delegates to nest_well_render_discovery_feed().
 * The full implementation lives in inc/discovery-feed.php so it can
 * be reused by the [discovery_feed] shortcode and block pattern.
 *
 * Optional $args overrides (passed via get_template_part()):
 *   enabled, headline, subtitle, source, density, per_page
 *
 * @package nest-and-well
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'nest_well_render_discovery_feed' ) ) {
    return;
}

$overrides = isset( $args ) && is_array( $args ) ? $args : array();

echo nest_well_render_discovery_feed( $overrides ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — escaped in helper
