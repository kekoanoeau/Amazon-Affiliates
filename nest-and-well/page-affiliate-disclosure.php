<?php
/**
 * Affiliate Disclosure Page Template
 *
 * Auto-loads for the page with slug "affiliate-disclosure".
 * Contains FTC-required disclosures for the Amazon Associates program,
 * editorial-independence statement, and commission explanation.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main site-main--page site-main--disclosure">

    <!-- =============================================
         DISCLOSURE HERO BANNER
         ============================================= -->
    <div class="disclosure-hero">
        <div class="container disclosure-hero__inner">
            <div class="disclosure-hero__icon" aria-hidden="true">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </div>
            <div class="disclosure-hero__text">
                <h1 class="disclosure-hero__title"><?php esc_html_e( 'Affiliate Disclosure', 'nest-and-well' ); ?></h1>
                <p class="disclosure-hero__sub"><?php esc_html_e( 'Transparency you deserve — how we earn and how it affects our recommendations.', 'nest-and-well' ); ?></p>
            </div>
        </div>
    </div>

    <div class="container">

        <!-- Breadcrumbs -->
        <?php nest_well_breadcrumbs(); ?>

        <!-- FTC Quick-Summary Callout -->
        <div class="disclosure-summary" role="note" aria-label="<?php esc_attr_e( 'Disclosure summary', 'nest-and-well' ); ?>">
            <div class="disclosure-summary__badge">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <?php esc_html_e( 'FTC Disclosure', 'nest-and-well' ); ?>
            </div>
            <p class="disclosure-summary__text">
                <?php
                esc_html_e(
                    'Nest & Well participates in the Amazon Services LLC Associates Program, an affiliate advertising program that lets us earn commissions by linking to Amazon.com — at no extra cost to you. Our reviews and recommendations are always our own honest opinion, never influenced by affiliate relationships.',
                    'nest-and-well'
                );
                ?>
            </p>
            <p class="disclosure-summary__updated">
                <?php
                printf(
                    /* translators: %s: date */
                    esc_html__( 'Last updated: %s', 'nest-and-well' ),
                    '<time datetime="2026-01-01">January 1, 2026</time>'
                );
                ?>
            </p>
        </div>

        <!-- Disclosure Sections -->
        <div class="disclosure-body entry-content">

            <!-- Table of Contents -->
            <nav class="disclosure-toc" aria-label="<?php esc_attr_e( 'Page contents', 'nest-and-well' ); ?>">
                <p class="disclosure-toc__label"><?php esc_html_e( 'On this page', 'nest-and-well' ); ?></p>
                <ol class="disclosure-toc__list">
                    <li><a href="#what-are-affiliate-links"><?php esc_html_e( 'What Are Affiliate Links?', 'nest-and-well' ); ?></a></li>
                    <li><a href="#amazon-associates"><?php esc_html_e( 'Amazon Associates Program', 'nest-and-well' ); ?></a></li>
                    <li><a href="#how-commissions-work"><?php esc_html_e( 'How Commissions Work', 'nest-and-well' ); ?></a></li>
                    <li><a href="#editorial-independence"><?php esc_html_e( 'Editorial Independence', 'nest-and-well' ); ?></a></li>
                    <li><a href="#where-disclosures-appear"><?php esc_html_e( 'Where Disclosures Appear', 'nest-and-well' ); ?></a></li>
                    <li><a href="#your-choices"><?php esc_html_e( 'Your Choices', 'nest-and-well' ); ?></a></li>
                    <li><a href="#contact-us"><?php esc_html_e( 'Contact Us', 'nest-and-well' ); ?></a></li>
                </ol>
            </nav>

            <!-- Section 1: What Are Affiliate Links? -->
            <section id="what-are-affiliate-links" class="disclosure-section">
                <div class="disclosure-section__header">
                    <span class="disclosure-section__icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                    </span>
                    <h2><?php esc_html_e( 'What Are Affiliate Links?', 'nest-and-well' ); ?></h2>
                </div>
                <p><?php esc_html_e( 'An affiliate link is a special URL that contains a tracking code tied to our account. When you click one of these links and make a qualifying purchase, the retailer records that we referred you and credits our account with a small commission.', 'nest-and-well' ); ?></p>
                <p><?php esc_html_e( 'The price you pay is exactly the same whether you use our link or navigate to the product directly — the commission comes out of the retailer\'s marketing budget, not from a surcharge added to your order.', 'nest-and-well' ); ?></p>
                <div class="disclosure-highlight">
                    <strong><?php esc_html_e( 'In plain English:', 'nest-and-well' ); ?></strong>
                    <?php esc_html_e( ' You pay nothing extra. We earn a small referral fee. The retailer gains a sale. Everyone benefits.', 'nest-and-well' ); ?>
                </div>
            </section>

            <!-- Section 2: Amazon Associates -->
            <section id="amazon-associates" class="disclosure-section">
                <div class="disclosure-section__header">
                    <span class="disclosure-section__icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                            <line x1="8" y1="21" x2="16" y2="21"/>
                            <line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                    </span>
                    <h2><?php esc_html_e( 'Amazon Associates Program', 'nest-and-well' ); ?></h2>
                </div>
                <p>
                    <?php
                    esc_html_e(
                        'Nest & Well is a participant in the Amazon Services LLC Associates Program, an affiliate advertising program designed to provide a means for sites to earn advertising fees by advertising and linking to Amazon.com.',
                        'nest-and-well'
                    );
                    ?>
                </p>
                <p>
                    <?php
                    esc_html_e(
                        'Amazon, the Amazon logo, AmazonSupply, and the AmazonSupply logo are trademarks of Amazon.com, Inc. or its affiliates.',
                        'nest-and-well'
                    );
                    ?>
                </p>
                <div class="disclosure-amazon-badge">
                    <div class="disclosure-amazon-badge__inner">
                        <div class="disclosure-amazon-badge__logo" aria-hidden="true">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="disclosure-amazon-badge__title"><?php esc_html_e( 'Verified Amazon Associate', 'nest-and-well' ); ?></p>
                            <p class="disclosure-amazon-badge__sub"><?php esc_html_e( 'We link only to products we have independently evaluated.', 'nest-and-well' ); ?></p>
                        </div>
                    </div>
                </div>
                <p>
                    <?php
                    esc_html_e(
                        'Our participation in the Amazon Associates program means that some links on this site lead to Amazon product pages. These links are identified by standard affiliate link attributes (rel="sponsored") and the fact that they point to amazon.com.',
                        'nest-and-well'
                    );
                    ?>
                </p>
            </section>

            <!-- Section 3: How Commissions Work -->
            <section id="how-commissions-work" class="disclosure-section">
                <div class="disclosure-section__header">
                    <span class="disclosure-section__icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </span>
                    <h2><?php esc_html_e( 'How Commissions Work', 'nest-and-well' ); ?></h2>
                </div>
                <p><?php esc_html_e( 'Commission rates vary by product category. Amazon pays a percentage of the sale price when a qualifying purchase is made within 24 hours of clicking our link (or 90 days if the item was added to the cart during that window).', 'nest-and-well' ); ?></p>

                <div class="disclosure-commission-table">
                    <table>
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Product Category', 'nest-and-well' ); ?></th>
                                <th><?php esc_html_e( 'Typical Commission Rate', 'nest-and-well' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php esc_html_e( 'Smart Home Devices', 'nest-and-well' ); ?></td>
                                <td>4%</td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Home & Garden', 'nest-and-well' ); ?></td>
                                <td>3%</td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Health & Beauty', 'nest-and-well' ); ?></td>
                                <td>3%</td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Wellness & Fitness', 'nest-and-well' ); ?></td>
                                <td>3%</td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Electronics', 'nest-and-well' ); ?></td>
                                <td>2.5%</td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Amazon Devices (Echo, Fire, etc.)', 'nest-and-well' ); ?></td>
                                <td>4%</td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="disclosure-commission-table__note">
                        <em><?php esc_html_e( 'Rates reflect Amazon\'s standard Associates fee schedule and are subject to change. See Amazon\'s Associates Program Operating Agreement for current rates.', 'nest-and-well' ); ?></em>
                    </p>
                </div>

                <p><?php esc_html_e( 'We do not receive commissions for products that are returned, refunded, or that do not meet Amazon\'s qualifying purchase criteria.', 'nest-and-well' ); ?></p>
            </section>

            <!-- Section 4: Editorial Independence -->
            <section id="editorial-independence" class="disclosure-section">
                <div class="disclosure-section__header">
                    <span class="disclosure-section__icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </span>
                    <h2><?php esc_html_e( 'Editorial Independence', 'nest-and-well' ); ?></h2>
                </div>
                <p><?php esc_html_e( 'Our editorial opinions are never influenced by affiliate relationships. We recommend products because we genuinely believe they offer value to our readers — not because they pay a higher commission.', 'nest-and-well' ); ?></p>

                <div class="disclosure-principles">
                    <div class="disclosure-principle">
                        <div class="disclosure-principle__icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <div>
                            <strong><?php esc_html_e( 'We research products independently.', 'nest-and-well' ); ?></strong>
                            <p><?php esc_html_e( 'Our buying guides and reviews are based on hands-on testing, expert interviews, and extensive research — not on what pays us the most.', 'nest-and-well' ); ?></p>
                        </div>
                    </div>
                    <div class="disclosure-principle">
                        <div class="disclosure-principle__icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <div>
                            <strong><?php esc_html_e( 'We include negative findings.', 'nest-and-well' ); ?></strong>
                            <p><?php esc_html_e( 'If a product has significant drawbacks, we say so — even if it means we earn a lower commission or none at all.', 'nest-and-well' ); ?></p>
                        </div>
                    </div>
                    <div class="disclosure-principle">
                        <div class="disclosure-principle__icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <div>
                            <strong><?php esc_html_e( 'We do not accept payment for positive reviews.', 'nest-and-well' ); ?></strong>
                            <p><?php esc_html_e( 'No brand, manufacturer, or retailer can purchase editorial coverage on Nest & Well. Our rankings are earned, not bought.', 'nest-and-well' ); ?></p>
                        </div>
                    </div>
                    <div class="disclosure-principle">
                        <div class="disclosure-principle__icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <div>
                            <strong><?php esc_html_e( 'Commission rates do not influence placement.', 'nest-and-well' ); ?></strong>
                            <p><?php esc_html_e( 'A product with a 2% commission rate has the same chance of earning "Best Pick" as one with a 4% rate. Our #1 recommendation is always the product we believe is best for most people.', 'nest-and-well' ); ?></p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 5: Where Disclosures Appear -->
            <section id="where-disclosures-appear" class="disclosure-section">
                <div class="disclosure-section__header">
                    <span class="disclosure-section__icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </span>
                    <h2><?php esc_html_e( 'Where Disclosures Appear on Our Site', 'nest-and-well' ); ?></h2>
                </div>
                <p><?php esc_html_e( 'In accordance with FTC guidelines, we disclose our affiliate relationships clearly and conspicuously in the following locations:', 'nest-and-well' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'At the top of every article that contains affiliate links, before any product recommendations.', 'nest-and-well' ); ?></li>
                    <li><?php esc_html_e( 'In a site-wide notice in the page footer on every page.', 'nest-and-well' ); ?></li>
                    <li><?php esc_html_e( 'Adjacent to affiliate links in product boxes and buying guides.', 'nest-and-well' ); ?></li>
                    <li><?php esc_html_e( 'On this dedicated Affiliate Disclosure page, linked from the site footer.', 'nest-and-well' ); ?></li>
                </ul>
                <p><?php esc_html_e( 'We follow the FTC\'s guidance on endorsements and testimonials in advertising (16 C.F.R. Part 255) and the Amazon Associates Program Operating Agreement.', 'nest-and-well' ); ?></p>
            </section>

            <!-- Section 6: Your Choices -->
            <section id="your-choices" class="disclosure-section">
                <div class="disclosure-section__header">
                    <span class="disclosure-section__icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span>
                    <h2><?php esc_html_e( 'Your Choices', 'nest-and-well' ); ?></h2>
                </div>
                <p><?php esc_html_e( 'You are never required to purchase products through our links. If you prefer not to support us through affiliate commissions, you can:', 'nest-and-well' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'Navigate directly to Amazon.com or another retailer and search for the product by name.', 'nest-and-well' ); ?></li>
                    <li><?php esc_html_e( 'Use a browser extension that strips affiliate tags from URLs.', 'nest-and-well' ); ?></li>
                    <li><?php esc_html_e( 'Add items to your cart from our link, then clear the cart and search for the product directly.', 'nest-and-well' ); ?></li>
                </ul>
                <p><?php esc_html_e( 'Our content is free to read regardless of whether you use our affiliate links. Affiliate commissions help us keep the site running, hire independent testers, and produce in-depth reviews — but we understand if you\'d prefer to opt out.', 'nest-and-well' ); ?></p>
            </section>

            <!-- Section 7: Contact Us -->
            <section id="contact-us" class="disclosure-section disclosure-section--last">
                <div class="disclosure-section__header">
                    <span class="disclosure-section__icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </span>
                    <h2><?php esc_html_e( 'Questions? Contact Us', 'nest-and-well' ); ?></h2>
                </div>
                <p><?php esc_html_e( 'If you have questions about our affiliate relationships, how we test products, or how commissions affect our recommendations, we\'re happy to discuss them.', 'nest-and-well' ); ?></p>
                <div class="disclosure-contact">
                    <div class="disclosure-contact__item">
                        <strong><?php esc_html_e( 'Website:', 'nest-and-well' ); ?></strong>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
                    </div>
                    <div class="disclosure-contact__item">
                        <strong><?php esc_html_e( 'Email:', 'nest-and-well' ); ?></strong>
                        <?php
                        $contact_email = get_theme_mod( 'nest_well_contact_email', 'hello@nestandwell.com' );
                        echo '<a href="mailto:' . esc_attr( antispambot( $contact_email ) ) . '">' . esc_html( antispambot( $contact_email ) ) . '</a>';
                        ?>
                    </div>
                </div>
                <p class="disclosure-contact__note">
                    <em>
                        <?php
                        printf(
                            /* translators: %s: date */
                            esc_html__( 'This disclosure was last updated on %s. We reserve the right to update it at any time as our affiliate partnerships or applicable laws change.', 'nest-and-well' ),
                            '<time datetime="2026-01-01">January 1, 2026</time>'
                        );
                        ?>
                    </em>
                </p>
            </section>

        </div><!-- .disclosure-body -->

    </div><!-- .container -->

</main><!-- #main -->

<?php
get_footer();
