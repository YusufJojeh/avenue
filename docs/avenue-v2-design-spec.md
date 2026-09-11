# Avenue V2 Design Specification

## 1. Current-state summary

Avenue is a bilingual premium catalog. Its core journey is discovery → product detail → WhatsApp contact, not cart or checkout.

### Confirmed from code

- Shared layout: sticky header, search, category mega-menu, locale/theme controls, footer, quick-view modal, floating WhatsApp action.
- Home: campaign hero, carousel, featured products, categories, offers, new arrivals, brands, closing CTA.
- Catalog: search, category, brand, sort, grid/list mode, active filters, count, pagination, empty state.
- Category: hierarchy, subcategories, sorting, products, pagination, sibling categories.
- Product: gallery, title, price, stock, sizes, description, related products, wishlist/share, WhatsApp ordering.
- Cards use square product imagery; campaigns and categories use separate ratios.
- UI uses Bootstrap, Blade components/partials, Font Awesome, global CSS, and large page-local styles.
- Arabic/English routing, RTL, dark mode, SEO, and pagination already exist.

### Current visual language

- Dynamic background/surface/text tokens with recurring gold `#f0c24b`.
- Cinzel brand display, Montserrat interface text, Georgia section headings.
- Radii span 6–24px plus pills/circles.
- Borders, shadows, inset highlights, blur, gradients, and glows frequently combine.
- Product, category, offer, crystal, glass, luxe, modal, and utility card styles overlap.
- Bootstrap, gold, outline, filter, reset, icon, product-action, and WhatsApp buttons coexist.
- Font Awesome is primary; inline SVG and emoji also appear.
- Hover lift/scale/shine/tilt, reveal, heartbeat, marquee, carousel, and entrance motion coexist.

## 2. Confirmed UX/design problems

### Discovery

1. Hero and carousel can both precede catalog content, creating competing campaign leaders.
2. Crystal layers, floating shapes, gradients, title effects, and hover motion compete with photography.
3. Featured, offers, arrivals, brands, and closing CTA carry similar visual weight.
4. Header search, navigation, mega-menu, theme, locale, and mobile controls create high density.
5. Closing shopping CTA repeats discovery paths already available above.

### Product cards

1. Quick view, wishlist, view, copy, and another wishlist action compete inside one card.
2. The primary behavior—open product detail—is not dominant.
3. Product name is a heading but not the primary link in the shared partial.
4. Multiple colors/buttons add noise around price and imagery.
5. Two product-card implementations overlap and can drift.

### Catalog/search

1. Desktop filters dominate the page before results.
2. Active-filter removal uses clickable spans instead of buttons.
3. Grid/list icon buttons lack explicit accessible names and pressed state.
4. Catalog and search maintain separate large inline style systems.
5. Search duplicates catalog result patterns rather than sharing one shell.
6. Existing pagination is appropriate and should remain.

### Product detail/WhatsApp

1. Broad order is correct: gallery, facts, options, action, details, related products.
2. Placeholder stars and “0 reviews” imply social proof the application does not have.
3. Wishlist/share actions compete with the WhatsApp action.
4. Global and product-specific WhatsApp actions may compete on product pages.
5. Sticky/fixed behavior requires live small-screen verification.
6. Large inline product CSS/JS increases component drift.

### Accessibility

- Strengths: skip link, focus-visible rules, labels, alt text, modal labeling, reduced-motion handling.
- Some icon-only controls rely on `title` without explicit accessible names.
- Filter removal is not keyboard-native.
- Some headings follow visual size (`h5`/`h6`) instead of document hierarchy.
- Decorative emoji are not consistently hidden from assistive technology.
- Contrast is unconfirmed because final colors are dynamic and live measurement was unavailable.

## 3. Needs visual verification

- Header wrapping and collapsed navigation at 320–768px.
- Mega-menu positioning and keyboard flow in LTR/RTL.
- Floating WhatsApp overlap with content or product CTA.
- Sticky gallery behavior on short screens and orientation changes.
- Gold, muted text, glass border, and image-overlay contrast.
- Long Arabic labels at 200% zoom.
- Card density and touch spacing in two-column mobile grids.
- Dark-theme consistency across Bootstrap, inline styles, and dynamic theme values.
- Loading, image-error, and asynchronous search transitions.

## 4. Avenue V2 direction

### Restrained editorial catalog

V2 is a curated fashion lookbook with clear commerce intent. Photography creates atmosphere; type and whitespace create premium character; gold marks priority instead of decorating every surface.

- Calm neutral surfaces with one gold accent.
- Flat surfaces/fine borders; elevation reserved for menus and modals.
- One campaign leader per viewport.
- Short, functional motion only.
- Product card is the discovery target; WhatsApp is the product-page conversion target.
- Preserve dark mode, RTL, routing, pagination, search, and supported filters.

## 5. V2 design tokens

| Token | Light | Dark | Use |
|---|---|---|---|
| Page background | `#F7F5F0` | `#111214` | Canvas |
| Surface | `#FFFFFF` | `#191B1F` | Cards/controls |
| Elevated surface | `#FFFFFF` | `#22252A` | Menus/modals |
| Primary text | `#181818` | `#F5F3EE` | Headings/body |
| Secondary text | `#69665F` | `#B7B3AA` | Supporting copy |
| Accent/gold | `#B88A2A` | `#D4AD55` | Primary emphasis |
| Border | `#E3DED4` | `#34373D` | Structure |
| Success | `#287A4B` | `#55B77A` | Availability/WhatsApp |
| Error | `#B53A3A` | `#E47777` | Errors |

- Content width: `1200px`; page gutters: 16/24/32px.
- Spacing: `4, 8, 12, 16, 24, 32, 48, 64`px.
- Radius: 6px controls, 10px cards, 16px editorial media; pills only for status.
- Shadows: `0 2px 8px rgba(0,0,0,.06)`; elevated `0 12px 32px rgba(0,0,0,.12)`.
- Type: 14px caption, 16px body, 20px subhead, 28–32px section title, 40–56px campaign.
- Buttons: 40px compact, 48px standard, 52px purchase.
- Cinzel only for brand/campaign display; Montserrat for navigation, controls, content.

## 6. Homepage specification

| Order | Section | Purpose/content/CTA | Desktop | Mobile |
|---:|---|---|---|---|
| 1 | Header | Search and catalog destinations | Calm row; flexible search | Logo, search, one menu |
| 2 | Campaign | Image, headline, sentence, “Explore collection” | Split/full-bleed editorial | Image then full-width CTA |
| 3 | Categories | 4–8 key categories; card opens category | Four-column grid | Two-column grid |
| 4 | New arrivals | Current products; card opens detail | Four columns | Two columns, less metadata |
| 5 | Featured | Story plus curated products | Asymmetric editorial block | Story then products |
| 6 | Offers | Real offer and factual destination | One dominant/two balanced | Stacked; no hover dependency |
| 7 | Brands | Active brands, “View all brands” | Compact row/grid | Two-column grid |
| 8 | Trust/service | Truthful service facts; FAQ/contact | Three-item row | Compact stack |
| 9 | Footer | Catalog, support, policies, social | Grouped columns | Concise stacked groups |

If a hero exists, the carousel must not become a second primary hero. Show no newsletter unless its behavior works.

## 7. Catalog/category/search specification

- Header: breadcrumbs, title, one-line context, result count.
- Desktop: search, supported category, brand, and sort in one compact bar.
- Mobile: visible search; Filter opens a disclosure/off-canvas with existing fields.
- Active filters: semantic removable chips plus one reset action.
- Grid: four columns wide, three medium, two mobile; retain pagination.
- Card: image, optional brand eyebrow, linked name, price/sale, one subtle wishlist control.
- Sale price leads; original price is quieter and struck through.
- Show availability only when accurate and decision-relevant.
- Search shares catalog shell, cards, pagination, loading, and empty state.
- Category leads with context, then subcategories, products, and existing sort.
- Empty state explains the result and offers reset/view-all.
- Loading uses one reduced-motion-safe product-grid skeleton.
- Do not invent facets unsupported by current data.

## 8. Product-page specification

### Desktop

1. Breadcrumbs.
2. Left: square gallery and ordered thumbnails.
3. Right: optional brand, title, price/sale, availability, sizes.
4. Dominant “Order on WhatsApp” action.
5. Subordinate wishlist/share actions.
6. Concise decision-critical summary.
7. Below: full details and related shared cards.

### Mobile

- Gallery first with accessible thumbnail controls and no hover dependency.
- Title, price, availability, and sizes follow immediately.
- WhatsApp follows required choices; long details follow later.
- Consider one sticky CTA only if it replaces, not duplicates, the global floating action.
- Remove placeholder ratings/reviews.

### WhatsApp conversion

- Use one label: “Order on WhatsApp.”
- Include product identity, selected size, and current Avenue price before contact.
- Keep WhatsApp more prominent than copy/share/wishlist.
- Never imply checkout, payment, reservation, or stock guarantee.

## 9. Component plan

- `SiteHeader`: logo, nav, search, locale/theme utilities.
- `MobileNav`: same destinations in one accessible disclosure.
- `SectionHeader`: title, optional description and text CTA.
- `ProductCard`: one image-led card for all discovery surfaces.
- `PriceDisplay`: normal/sale hierarchy and currency formatting.
- `CategoryCard`: image, name, optional concise count.
- `ProductGallery`: main image, thumbnails, modal, fallback.
- `WhatsAppCTA`: consistent context, label, and states.
- `FilterBar`: search/category/brand/sort and active filters.
- `PromoSection`: one editorial campaign treatment.
- `EmptyState`: decorative slot, explanation, recovery action.

Use Blade components for stable presentation and partials where input objects vary. Add no frontend framework.

## 10. CSS migration strategy

### KEEP

- Bootstrap utilities/grid, dynamic theme, RTL, skip link, focus rules, reduced motion, verified image ratios.

### MERGE LATER

- Make `public/css/layout.css` canonical, ordered as tokens, base, layout, shared components.
- Merge duplicate cards, buttons, forms, radii, shadows, empty/loading states into primitives.

### PAGE-SPECIFIC

- Keep `home.css` home-only; rewrite against tokens during Phase C.
- Keep gallery/purchase rules product-only.
- Let catalog filters/grid serve category/search where practical.
- Load `advanced-gallery.css/js` only when its gallery renders.

### DEPRECATE AFTER V2

- Retire `avenue-unified.css`, `premium-product-enhancements.css`, crystal/platform public styles only after proven unused.
- Retire replaced inline styles, shine, tilt, marquee, glow, duplicate reveal/action rules.
- Do not add another “V2 final” stylesheet.

## 11. Implementation phases

- **A — Tokens/primitives:** type, spacing, buttons, forms, focus, surfaces. Test themes, locales, keyboard, motion, zoom.
- **B — ProductCard:** one card/price hierarchy. Test listings, Arabic names, sales, missing images, mobile.
- **C — Homepage:** specified hierarchy. Test optional sections, missing content, RTL, responsive order.
- **D — Catalog/category/search:** share filters, grid, pagination, loading, empty state. Test query preservation and keyboard.
- **E — Product detail:** reorder decisions and clarify WhatsApp. Test images, sizes, stock/sales, related items, CTA overlap.
- **F — Header/footer/mobile:** reduce density and reconcile WhatsApp. Test RTL, 320px, landscape, zoom, keyboard.
- **G — Cleanup:** trace/remove only superseded CSS/JS; compare snapshots and assets before removal.

Each phase ships independently with focused HTTP/Blade tests and visual verification when tooling works.

## 12. Explicit non-goals

- No cart, checkout, payment, shipping, accounts, or order management.
- No route, SEO, importer, admin, database, or image-ingestion changes.
- No React/Vue, new CSS framework, dependency upgrade, font addition, or frontend rewrite.
- No unsupported filters or product data.
- No fake reviews, ratings, guarantees, scarcity, or luxury claims.
- No removal of dark mode or RTL.
- This document authorizes no implementation; approve Phase A separately.
