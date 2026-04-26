<?php
/**
 * FAQ Post-Meta Repeater
 *
 * Lets authors capture "Frequently asked questions" as a structured list of
 * {question, answer} pairs in a post meta box, instead of pasting multiple
 * [faq] shortcodes inline. The collected items feed both the article's
 * accordion (rendered by template-parts/faq-list.php) and the FAQPage JSON-LD
 * emitted by inc/seo-helpers.php.
 *
 * Storage format: post meta key `_faq_items`, value is a serialised array of
 *   array( 'q' => string, 'a' => string )
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the FAQ meta box on the post editor.
 */
function nest_well_register_faq_meta_box() {
    foreach ( array( 'post', 'page' ) as $type ) {
        add_meta_box(
            'nest_well_faq_items',
            __( 'Frequently Asked Questions', 'nest-and-well' ),
            'nest_well_render_faq_meta_box',
            $type,
            'normal',
            'default'
        );
    }
}
add_action( 'add_meta_boxes', 'nest_well_register_faq_meta_box' );

/**
 * Render the FAQ meta box.
 *
 * @param WP_Post $post Current post.
 */
function nest_well_render_faq_meta_box( $post ) {
    wp_nonce_field( 'nest_well_save_faq', 'nest_well_faq_nonce' );

    $items = nest_well_get_post_faqs( $post->ID );
    if ( empty( $items ) ) {
        $items = array( array( 'q' => '', 'a' => '' ) );
    }
    ?>
    <p class="description">
        <?php esc_html_e( 'Each row becomes one FAQ accordion item on the post and a Question entry in the FAQPage schema. Leave both fields blank to remove a row.', 'nest-and-well' ); ?>
    </p>
    <div class="nest-well-faq-rows" id="nest-well-faq-rows">
        <?php foreach ( $items as $i => $item ) : ?>
        <div class="nest-well-faq-row">
            <p>
                <label>
                    <strong><?php esc_html_e( 'Question', 'nest-and-well' ); ?></strong>
                    <input type="text"
                           name="nest_well_faq[<?php echo (int) $i; ?>][q]"
                           value="<?php echo esc_attr( $item['q'] ); ?>"
                           class="widefat">
                </label>
            </p>
            <p>
                <label>
                    <strong><?php esc_html_e( 'Answer', 'nest-and-well' ); ?></strong>
                    <textarea name="nest_well_faq[<?php echo (int) $i; ?>][a]"
                              rows="3"
                              class="widefat"><?php echo esc_textarea( $item['a'] ); ?></textarea>
                </label>
            </p>
            <p>
                <button type="button" class="button-link nest-well-faq-remove">
                    <?php esc_html_e( 'Remove this FAQ', 'nest-and-well' ); ?>
                </button>
            </p>
            <hr>
        </div>
        <?php endforeach; ?>
    </div>
    <p>
        <button type="button" class="button" id="nest-well-faq-add">
            <?php esc_html_e( '+ Add FAQ', 'nest-and-well' ); ?>
        </button>
    </p>

    <script>
    (function () {
        var rows = document.getElementById('nest-well-faq-rows');
        var addBtn = document.getElementById('nest-well-faq-add');
        if (!rows || !addBtn) return;

        function nextIndex() {
            return rows.querySelectorAll('.nest-well-faq-row').length;
        }

        addBtn.addEventListener('click', function () {
            var i = nextIndex();
            var row = document.createElement('div');
            row.className = 'nest-well-faq-row';
            row.innerHTML =
                '<p><label><strong><?php echo esc_js( __( 'Question', 'nest-and-well' ) ); ?></strong>' +
                '<input type="text" name="nest_well_faq[' + i + '][q]" class="widefat"></label></p>' +
                '<p><label><strong><?php echo esc_js( __( 'Answer', 'nest-and-well' ) ); ?></strong>' +
                '<textarea name="nest_well_faq[' + i + '][a]" rows="3" class="widefat"></textarea></label></p>' +
                '<p><button type="button" class="button-link nest-well-faq-remove"><?php echo esc_js( __( 'Remove this FAQ', 'nest-and-well' ) ); ?></button></p><hr>';
            rows.appendChild(row);
        });

        rows.addEventListener('click', function (e) {
            var btn = e.target.closest('.nest-well-faq-remove');
            if (!btn) return;
            var row = btn.closest('.nest-well-faq-row');
            if (row && rows.querySelectorAll('.nest-well-faq-row').length > 1) {
                row.parentNode.removeChild(row);
            } else if (row) {
                row.querySelectorAll('input, textarea').forEach(function (el) { el.value = ''; });
            }
        });
    })();
    </script>
    <?php
}

/**
 * Save FAQ meta box on post save.
 *
 * @param int $post_id Post ID.
 */
function nest_well_save_faq_meta( $post_id ) {
    if (
        ! isset( $_POST['nest_well_faq_nonce'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nest_well_faq_nonce'] ) ), 'nest_well_save_faq' )
    ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $raw   = isset( $_POST['nest_well_faq'] ) ? wp_unslash( $_POST['nest_well_faq'] ) : array();
    $clean = array();

    if ( is_array( $raw ) ) {
        foreach ( $raw as $row ) {
            $q = isset( $row['q'] ) ? sanitize_text_field( $row['q'] ) : '';
            $a = isset( $row['a'] ) ? wp_kses_post( $row['a'] ) : '';
            if ( $q && $a ) {
                $clean[] = array( 'q' => $q, 'a' => $a );
            }
        }
    }

    if ( empty( $clean ) ) {
        delete_post_meta( $post_id, '_faq_items' );
    } else {
        update_post_meta( $post_id, '_faq_items', $clean );
    }
}
add_action( 'save_post_post', 'nest_well_save_faq_meta' );
add_action( 'save_post_page', 'nest_well_save_faq_meta' );

/**
 * Get the structured FAQ items stored on a post.
 *
 * @param int $post_id Post ID (defaults to current).
 * @return array[] List of ['q' => question, 'a' => answer] pairs.
 */
function nest_well_get_post_faqs( $post_id = 0 ) {
    $post_id = $post_id ? (int) $post_id : (int) get_the_ID();
    if ( ! $post_id ) {
        return array();
    }

    $items = get_post_meta( $post_id, '_faq_items', true );
    if ( ! is_array( $items ) ) {
        return array();
    }

    return array_values(
        array_filter(
            $items,
            function ( $row ) {
                return is_array( $row ) && ! empty( $row['q'] ) && ! empty( $row['a'] );
            }
        )
    );
}

/**
 * Register meta-derived FAQ items with the global FAQ collector so the
 * FAQPage JSON-LD picks them up. Hooked on wp_head before the schema
 * function runs (priority 5; nest_well_seo_meta_tags runs at 5 too,
 * but FAQ schema is a wp_footer action, so wp_head registration is fine).
 */
function nest_well_register_meta_faqs_for_schema() {
    if ( ! is_singular( array( 'post', 'page' ) ) ) {
        return;
    }

    $items = nest_well_get_post_faqs( get_the_ID() );
    foreach ( $items as $item ) {
        nest_well_get_faq_items(
            array(
                'question' => $item['q'],
                'answer'   => $item['a'],
            )
        );
    }
}
add_action( 'wp_head', 'nest_well_register_meta_faqs_for_schema', 6 );
