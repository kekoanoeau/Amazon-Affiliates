<?php
/**
 * Review Details Meta Box
 *
 * Replaces the default WordPress Custom Fields panel for the review-post
 * meta keys (`_review_score`, `_review_badge`, `_product_name`,
 * `_product_asin`, `_product_price`, `_review_pros`, `_review_cons`,
 * `_review_verdict`, `_last_updated`) so authors get a structured UI
 * with the right input controls instead of free-form text fields.
 *
 * The meta keys themselves are unchanged — anything that already reads
 * them (single.php, review-summary-meta.php, seo-helpers.php product
 * schema, the [review_summary] shortcode, etc.) keeps working without
 * modification.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Review Details meta box.
 */
function nest_well_register_review_meta_box() {
    add_meta_box(
        'nest_well_review_details',
        __( 'Review Details', 'nest-and-well' ),
        'nest_well_render_review_meta_box',
        'post',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes_post', 'nest_well_register_review_meta_box' );

/**
 * Render the Review Details meta box form.
 *
 * @param WP_Post $post Current post.
 */
function nest_well_render_review_meta_box( $post ) {
    wp_nonce_field( 'nest_well_save_review', 'nest_well_review_nonce' );

    $score        = get_post_meta( $post->ID, '_review_score', true );
    $badge        = get_post_meta( $post->ID, '_review_badge', true );
    $product_name = get_post_meta( $post->ID, '_product_name', true );
    $product_asin = get_post_meta( $post->ID, '_product_asin', true );
    $price        = get_post_meta( $post->ID, '_product_price', true );
    $pros         = get_post_meta( $post->ID, '_review_pros', true );
    $cons         = get_post_meta( $post->ID, '_review_cons', true );
    $verdict      = get_post_meta( $post->ID, '_review_verdict', true );
    $last_updated = get_post_meta( $post->ID, '_last_updated', true );

    $badges = array(
        ''                => __( '— No badge —', 'nest-and-well' ),
        'editors-choice'  => __( "Editor's Choice", 'nest-and-well' ),
        'best-value'      => __( 'Best Value', 'nest-and-well' ),
        'budget-pick'     => __( 'Budget Pick', 'nest-and-well' ),
        'premium-pick'    => __( 'Premium Pick', 'nest-and-well' ),
        'staff-favorite'  => __( 'Staff Favorite', 'nest-and-well' ),
    );
    ?>
    <style>
        .nest-well-review-meta p { margin: 0 0 14px; }
        .nest-well-review-meta label { display: block; font-weight: 600; margin-bottom: 4px; }
        .nest-well-review-meta input[type="text"],
        .nest-well-review-meta input[type="number"],
        .nest-well-review-meta input[type="date"],
        .nest-well-review-meta select,
        .nest-well-review-meta textarea { width: 100%; }
        .nest-well-review-meta .description { font-size: 11px; color: #646970; margin-top: 2px; }
    </style>
    <div class="nest-well-review-meta">
        <p class="description" style="margin-bottom: 16px;">
            <?php esc_html_e( 'Fill these in for review posts. Leave blank for editorial / list articles.', 'nest-and-well' ); ?>
        </p>

        <p>
            <label for="nest-well-review-score">
                <?php esc_html_e( 'Review score (0–10)', 'nest-and-well' ); ?>
            </label>
            <input type="number"
                   id="nest-well-review-score"
                   name="nest_well_review[score]"
                   value="<?php echo esc_attr( $score ); ?>"
                   min="0" max="10" step="0.1">
        </p>

        <p>
            <label for="nest-well-review-badge">
                <?php esc_html_e( 'Badge', 'nest-and-well' ); ?>
            </label>
            <select id="nest-well-review-badge" name="nest_well_review[badge]">
                <?php foreach ( $badges as $value => $label ) : ?>
                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $badge, $value ); ?>>
                    <?php echo esc_html( $label ); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="nest-well-product-name">
                <?php esc_html_e( 'Product name', 'nest-and-well' ); ?>
            </label>
            <input type="text"
                   id="nest-well-product-name"
                   name="nest_well_review[product_name]"
                   value="<?php echo esc_attr( $product_name ); ?>"
                   placeholder="<?php esc_attr_e( 'e.g. Echo Dot (5th Gen)', 'nest-and-well' ); ?>">
        </p>

        <p>
            <label for="nest-well-product-asin">
                <?php esc_html_e( 'Amazon ASIN', 'nest-and-well' ); ?>
            </label>
            <input type="text"
                   id="nest-well-product-asin"
                   name="nest_well_review[product_asin]"
                   value="<?php echo esc_attr( $product_asin ); ?>"
                   placeholder="<?php esc_attr_e( 'e.g. B09B8V1LZ3', 'nest-and-well' ); ?>"
                   pattern="[A-Za-z0-9]{8,12}">
            <span class="description">
                <?php esc_html_e( 'Used to build the affiliate Amazon URL automatically.', 'nest-and-well' ); ?>
            </span>
        </p>

        <p>
            <label for="nest-well-product-price">
                <?php esc_html_e( 'Price', 'nest-and-well' ); ?>
            </label>
            <input type="text"
                   id="nest-well-product-price"
                   name="nest_well_review[product_price]"
                   value="<?php echo esc_attr( $price ); ?>"
                   placeholder="<?php esc_attr_e( 'e.g. $49.99', 'nest-and-well' ); ?>">
        </p>

        <p>
            <label for="nest-well-review-pros">
                <?php esc_html_e( 'Pros (one per line)', 'nest-and-well' ); ?>
            </label>
            <textarea id="nest-well-review-pros"
                      name="nest_well_review[pros]"
                      rows="3"><?php echo esc_textarea( $pros ); ?></textarea>
        </p>

        <p>
            <label for="nest-well-review-cons">
                <?php esc_html_e( 'Cons (one per line)', 'nest-and-well' ); ?>
            </label>
            <textarea id="nest-well-review-cons"
                      name="nest_well_review[cons]"
                      rows="3"><?php echo esc_textarea( $cons ); ?></textarea>
        </p>

        <p>
            <label for="nest-well-review-verdict">
                <?php esc_html_e( 'Verdict', 'nest-and-well' ); ?>
            </label>
            <textarea id="nest-well-review-verdict"
                      name="nest_well_review[verdict]"
                      rows="4"><?php echo esc_textarea( $verdict ); ?></textarea>
            <span class="description">
                <?php esc_html_e( 'A 1–3 sentence bottom-line takeaway. Shown in the auto review summary and at the end of the post.', 'nest-and-well' ); ?>
            </span>
        </p>

        <p>
            <label for="nest-well-last-updated">
                <?php esc_html_e( 'Last updated', 'nest-and-well' ); ?>
            </label>
            <input type="date"
                   id="nest-well-last-updated"
                   name="nest_well_review[last_updated]"
                   value="<?php echo esc_attr( nest_well_normalize_date_for_input( $last_updated ) ); ?>">
            <span class="description">
                <?php esc_html_e( 'Drives the visible "Updated" date and the schema dateModified.', 'nest-and-well' ); ?>
            </span>
        </p>
    </div>
    <?php
}

/**
 * Convert any stored last-updated value (could be a free-form string) to a
 * Y-m-d format suitable for an <input type="date">.
 *
 * @param string $value Stored meta value.
 * @return string Y-m-d or empty string.
 */
function nest_well_normalize_date_for_input( $value ) {
    if ( ! $value ) {
        return '';
    }
    $ts = strtotime( $value );
    return $ts ? gmdate( 'Y-m-d', $ts ) : '';
}

/**
 * Persist Review Details on post save.
 *
 * @param int $post_id Post ID.
 */
function nest_well_save_review_meta( $post_id ) {
    if (
        ! isset( $_POST['nest_well_review_nonce'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nest_well_review_nonce'] ) ), 'nest_well_save_review' )
    ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $raw = isset( $_POST['nest_well_review'] ) ? wp_unslash( $_POST['nest_well_review'] ) : array();
    if ( ! is_array( $raw ) ) {
        return;
    }

    // Map of meta key => sanitiser callback.
    $fields = array(
        '_review_score'   => 'nest_well_sanitize_score',
        '_review_badge'   => 'sanitize_key',
        '_product_name'   => 'sanitize_text_field',
        '_product_asin'   => 'sanitize_text_field',
        '_product_price'  => 'sanitize_text_field',
        '_review_pros'    => 'sanitize_textarea_field',
        '_review_cons'    => 'sanitize_textarea_field',
        '_review_verdict' => 'wp_kses_post',
        '_last_updated'   => 'sanitize_text_field',
    );

    $key_map = array(
        '_review_score'   => 'score',
        '_review_badge'   => 'badge',
        '_product_name'   => 'product_name',
        '_product_asin'   => 'product_asin',
        '_product_price'  => 'product_price',
        '_review_pros'    => 'pros',
        '_review_cons'    => 'cons',
        '_review_verdict' => 'verdict',
        '_last_updated'   => 'last_updated',
    );

    foreach ( $fields as $meta_key => $sanitiser ) {
        $form_key = $key_map[ $meta_key ];
        $value    = isset( $raw[ $form_key ] ) ? $raw[ $form_key ] : '';
        $clean    = is_callable( $sanitiser ) ? call_user_func( $sanitiser, $value ) : $value;

        if ( '' === $clean || null === $clean ) {
            delete_post_meta( $post_id, $meta_key );
        } else {
            update_post_meta( $post_id, $meta_key, $clean );
        }
    }
}
add_action( 'save_post_post', 'nest_well_save_review_meta' );

/**
 * Sanitise a 0–10 review score, allowing one decimal place.
 *
 * @param mixed $value Raw value.
 * @return string Cleaned score, or empty string.
 */
function nest_well_sanitize_score( $value ) {
    if ( '' === $value || null === $value ) {
        return '';
    }
    $num = (float) $value;
    if ( $num < 0 ) {
        $num = 0;
    } elseif ( $num > 10 ) {
        $num = 10;
    }
    return rtrim( rtrim( number_format( $num, 1, '.', '' ), '0' ), '.' );
}
