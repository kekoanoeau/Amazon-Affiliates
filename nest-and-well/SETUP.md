# Nest & Well — Theme Setup Guide

## 1. Installation

1. Download the `nest-and-well.zip` file.
2. In your WordPress admin, go to **Appearance → Themes → Add New → Upload Theme**.
3. Choose `nest-and-well.zip` and click **Install Now**.
4. Click **Activate** once installation completes.

---

## 2. Recommended Plugins

Install these plugins for full functionality:

| Plugin | Purpose |
|--------|---------|
| **Rank Math SEO** | SEO meta, sitemap, schema supplement |
| **WP Rocket** | Page caching and performance optimization |
| **AAWP** (Amazon Affiliate WordPress Plugin) | Advanced Amazon product widgets |
| **Pretty Links Pro** | Affiliate link cloaking and click tracking |
| **MailerLite** | Email list management and forms |

---

## 3. Initial WordPress Configuration

### Set Homepage
1. Create a static page titled **"Home"** (leave content blank — the theme handles it).
2. Go to **Settings → Reading** and set:
   - "Your homepage displays" → **A static page**
   - Homepage → **Home**

### Set Permalinks
Go to **Settings → Permalinks** and select **Post name** (`/%postname%/`).

---

## 4. Create Required Categories

Create these categories (slugs must match exactly for the 5-stripe nav):

| Category Name | Slug |
|--------------|------|
| Smart Home | `smart-home` |
| Wellness Tech | `wellness-tech` |
| Home Beauty | `home-beauty` |
| Gift Guides | `gift-guides` |
| Deals | `deals` |

In **Posts → Categories**, create each with its exact slug.

---

## 5. Customizer Configuration

Go to **Appearance → Customize** to find the **"Nest & Well Settings"** panel.

### Brand Colors
- **Primary Color (Forest)**: `#1A3C34` — headlines, logo, nav background
- **Accent Color (Amber)**: `#E8A23A` — badges, ratings, CTAs
- **CTA Color (Sage)**: `#4A7C59` — buttons, links, highlights

### Header & Navigation
Configure each of the 5 category stripe links:

| Stripe | Default Label | Default URL |
|--------|--------------|-------------|
| Stripe 1 | Smart Home | `/smart-home/` |
| Stripe 2 | Wellness Tech | `/wellness-tech/` |
| Stripe 3 | Home Beauty | `/home-beauty/` |
| Stripe 4 | Gift Guides | `/gift-guides/` |
| Stripe 5 | Deals | `/deals/` |

Update each URL to match your actual category permalinks.

### Affiliate Settings
- **Amazon Associates Tracking ID**: Enter your tag (e.g., `nestandwell-20`)
- **Affiliate Disclosure Text**: Customize the FTC disclosure message
- **Show Disclosure on Homepage**: Toggle on/off

### Homepage
- Edit hero headline, subtext, and CTA button labels/URLs
- Toggle the featured category grid

### Footer
- Set footer tagline, copyright text, about blurb
- Enter social media profile URLs (Pinterest, Instagram, YouTube, Twitter/X)

---

## 6. Navigation Menus

Go to **Appearance → Menus** and create:

1. **Primary Navigation** — Assign to "Primary Navigation" location
   - Add your main category pages
   
2. **Footer Navigation** — Assign to "Footer Navigation" location

---

## 7. Sidebar Widgets

Go to **Appearance → Widgets** to populate widget areas:

| Widget Area | Recommended Content |
|-------------|-------------------|
| **Email Signup** | MailerLite embedded form |
| **Top Picks** | Auto-populated from same category |
| **Deal Alert** | Custom HTML with Amazon affiliate box |
| **About Nest & Well** | Text widget with 2-sentence description |
| **Footer — Smart Home** | Links to Smart Home sub-categories |
| **Footer — Wellness** | Links to Wellness sub-categories |
| **Footer — About & Legal** | Navigation links (About, Contact, Privacy, etc.) |

---

## 8. Shortcode Reference

### Product Box
```
[product_box 
  title="Echo Dot (5th Gen)" 
  image="https://example.com/image.jpg"
  price="$49.99"
  rating="9.2"
  prime="yes"
  badge="editors-choice"
  pros="Great sound,Easy setup,Affordable"
  cons="No 3.5mm jack,Alexa-only ecosystem"
  asin="B09B8V1LZ3"
]
```

### Rating Badge
```
[rating score="9.4" label="Editor's Choice"]
```

### Badge Only
```
[badge type="editors-choice"]
[badge type="best-value"]
[badge type="budget-pick"]
[badge type="premium-pick"]
[badge type="staff-favorite"]
```

### Pros & Cons
```
[pros_cons 
  pros="Great build quality,Long battery life,Accurate sensors" 
  cons="Expensive,App can be buggy"
]
```

### Affiliate Disclosure (manual)
```
[affiliate_disclosure]
```

### Comparison Table
```
[comparison_table title="Best Smart Speakers Compared"]
  [comparison_row name="Echo Dot 5th Gen" score="9.2" price="$49.99" feature="Best value" link="https://amzn.to/xxx"]
  [comparison_row name="Google Nest Mini" score="8.8" price="$49.99" feature="Google Assistant" link="https://amzn.to/xxx"]
[/comparison_table]
```

### Quick Picks
```
[quick_picks title="At a Glance"]
  [quick_pick type="Best Overall" name="Echo Show 10" desc="Best smart display overall" price="$249.99" link="https://amzn.to/xxx"]
  [quick_pick type="Best Budget" name="Echo Dot 5th Gen" desc="Best value Alexa speaker" price="$49.99" link="https://amzn.to/xxx"]
  [quick_pick type="Best Premium" name="Echo Studio" desc="Best audio quality" price="$199.99" link="https://amzn.to/xxx"]
[/quick_picks]
```

### FAQ Item (use multiple per article)
```
[faq question="Does the Echo Dot work without Wi-Fi?" answer="No, the Echo Dot requires a Wi-Fi connection to function. It cannot play music or respond to Alexa commands without internet access."]
```

### Buy Button
```
[buy_button url="https://www.amazon.com/dp/B09B8V1LZ3?tag=nestandwell-20" text="Check Price on Amazon"]
```

### Review Summary (place at top of review)
```
[review_summary 
  score="9.4" 
  title="Echo Dot 5th Generation"
  pros="Excellent sound,Smart home hub,Affordable"
  cons="Alexa-only,No 3.5mm jack"
  verdict="The best budget smart speaker you can buy in 2026. Improved audio and built-in hub functionality make this the go-to recommendation."
  price="$49.99"
  badge="editors-choice"
]
```

---

## 9. Writing Product Reviews

### Custom Fields (Post Meta)
When writing review posts, fill in these custom fields:

| Field | Value |
|-------|-------|
| `_review_score` | Score 0–10 (e.g., `9.4`) |
| `_review_badge` | `editors-choice`, `best-value`, `budget-pick`, `premium-pick`, or `staff-favorite` |
| `_product_price` | Price string (e.g., `$49.99`) |
| `_product_asin` | Amazon ASIN (e.g., `B09B8V1LZ3`) |
| `_product_name` | Full product name |
| `_last_updated` | Date (e.g., `2026-04-09`) |

Use a plugin like **Advanced Custom Fields** or **Meta Box** to add a UI for these fields, or use the default WordPress Custom Fields panel (enable via Screen Options).

---

## 10. Recommended Featured Image Sizes

| Use Case | Recommended Size |
|----------|-----------------|
| Article card thumbnail | 1200×800px (16:9 or 3:2) |
| Hero image | 1200×675px (16:9) |
| Product image | 800×800px (1:1 square) |
| Author avatar | 160×160px (square) |

All images are automatically cropped to theme-defined sizes on upload.

---

## 11. Creating Required Pages

Create these static pages:

| Page Title | Slug | Content |
|-----------|------|---------|
| Affiliate Disclosure | `affiliate-disclosure` | Your FTC disclosure |
| Privacy Policy | `privacy-policy` | Privacy policy |
| Terms of Use | `terms-of-use` | Terms and conditions |
| About | `about` | About your site |
| Contact | `contact` | Contact form |
| How We Review | `how-we-review` | Review methodology |

---

## 12. FTC Compliance Notes

- The theme **automatically prepends** the affiliate disclosure to every single post.
- The disclosure text is controlled via **Appearance → Customize → Affiliate Settings**.
- Use `[affiliate_disclosure]` shortcode if you need to place it manually.
- All affiliate links include `rel="nofollow noopener sponsored"` automatically.
- The homepage displays an affiliate disclosure banner (toggleable in Customizer).

---

## 13. Performance Tips

1. **WP Rocket**: Enable page caching, CSS/JS minification, and lazy loading.
2. **Images**: Use WebP format when possible; the theme handles lazy loading natively.
3. **CDN**: Use a CDN for images (Cloudflare or AWS CloudFront recommended).
4. **Google Fonts**: Inter is loaded from Google Fonts. For better performance, self-host using the **OMGF** plugin.

---

## Support

Theme Version: 1.0.0  
Requires WordPress: 6.0+  
Requires PHP: 8.0+

For issues or customization help, refer to the theme's template files — all code is documented inline.
