# Avenue Product CSV Import Contract

CSV files must be UTF-8, comma-separated, correctly quoted, and contain one product per row. Canonical columns, in order:

`name_ar, name_en, slug_ar, slug_en, category, brand, sku, price, sale_price, stock_qty, sizes, short_description_ar, short_description_en, description_ar, description_en, is_active, is_featured, seo_title_ar, seo_title_en, seo_description_ar, seo_description_en, primary_image_url, image_urls`

- Required headers: `name_ar`, `name_en`, `category`, `price`. Each row needs at least one non-blank name, an existing Avenue category, and a non-negative numeric price.
- Optional: all other fields. Blank brand stores `null`; an unknown brand warns and stores `null`.
- Lists: `sizes` and `image_urls` use `|` inside the cell.
- Booleans: `1/0`, `true/false`, or `yes/no`.
- Prices: `price` and `sale_price` are Avenue selling prices. `sale_price`, when present, cannot exceed `price`.
- Slugs: optional; blank slugs are generated from the product name. Supplied slugs are normalized by the importer.
- Images: direct HTTP(S) image URLs only. Image failures warn but do not block product creation.
- Import mode: create-only. Existing SKU or normalized slug matches are skipped and never updated.

## ChatGPT Link-to-CSV Contract

An external AI workflow may receive product-page links, but must output only the canonical Avenue CSV structure above. It must:

- Never invent missing factual specifications; preserve factual product details while optionally improving names and descriptions for clarity.
- Never copy an external-store price into Avenue `price` or `sale_price` unless the user explicitly instructs it. Leave unknown Avenue prices blank.
- Use an existing Avenue category. Leave an unknown brand blank rather than inventing one.
- Use direct HTTP(S) image URLs when available; separate lists with `|`.
- Exclude supplier/source URLs and tracking unless requested outside Avenue.
- Emit valid UTF-8 CSV, quote cells correctly, and write one product per row.
