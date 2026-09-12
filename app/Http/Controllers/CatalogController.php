<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Services\EnhancedPerformanceService;
use App\Services\PageCacheService;
use App\Support\SeoData;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __construct(
        private EnhancedPerformanceService $performance,
        private PageCacheService $pageCache
    ) {}
    // /products — Optimized product listing with caching
    public function index(Request $request)
    {
        $filters = [
            'search' => trim((string) $request->input('q', '')),
            'brand' => $request->input('brand'),
            'category' => $request->input('category'),
            'sort' => $request->input('sort', 'latest'),
        ];

        $page = $request->input('page', 1);
        $perPage = 12;

        // Use cached products with performance optimization
        $result = $this->performance->getCachedProducts($filters, $perPage, $page);

        // Get cached navigation data for filters
        $navigation = $this->performance->getCachedNavigation();

        // Convert cached arrays to objects for compatibility with views
        $products = collect($result['data'])->map(function ($product) {
            $productObj = (object) $product;
            // Convert nested arrays to objects
            if (isset($product['brand']) && is_array($product['brand'])) {
                $productObj->brand = (object) $product['brand'];
            }
            if (isset($product['category']) && is_array($product['category'])) {
                $productObj->category = (object) $product['category'];
            }
            return $productObj;
        });

        $brands = collect($navigation['brands'])->map(function ($brand) {
            return (object) $brand;
        });

        $categories = collect($navigation['categories'])->map(function ($category) {
            return (object) $category;
        });

        // Create a pagination-like object for the view
        $paginationData = $result['pagination'];
        $productsWithPagination = new class($products, $paginationData) implements \Iterator, \Countable {
            public $data;
            public $total;
            public $per_page;
            public $current_page;
            public $last_page;
            public $from;
            public $to;
            private $position = 0;
            private $queryParams = [];

            public function __construct($products, $paginationData) {
                $this->data = $products;
                $this->total = $paginationData['total'];
                $this->per_page = $paginationData['per_page'];
                $this->current_page = $paginationData['current_page'];
                $this->last_page = $paginationData['last_page'];
                $this->from = $paginationData['from'];
                $this->to = $paginationData['to'];
            }

            public function count(): int {
                return $this->data->count();
            }

            public function total() {
                return $this->total;
            }

            public function hasPages(): bool {
                return $this->last_page > 1;
            }

            public function appends($params) {
                $this->queryParams = array_merge($this->queryParams, $params);
                return $this;
            }

            public function links($view = null, $data = []) {
                // Use Laravel's pagination view instead of raw HTML
                $paginationData = [
                    'current_page' => $this->current_page,
                    'last_page' => $this->last_page,
                    'per_page' => $this->per_page,
                    'total' => $this->total,
                    'from' => ($this->current_page - 1) * $this->per_page + 1,
                    'to' => min($this->current_page * $this->per_page, $this->total),
                    'data' => $this->data,
                ];

                // Build query parameters for pagination links
                $queryParams = $this->queryParams;

                // Generate pagination links using Laravel's pagination view
                $html = '<nav><ul class="pagination">';

                // Previous page
                if ($this->current_page > 1) {
                    $prevPage = $this->current_page - 1;
                    $prevUrl = $this->buildUrl($prevPage, $queryParams);
                    $html .= '<li class="page-item"><a class="page-link" href="' . $prevUrl . '">' . __('pagination.previous') . '</a></li>';
                }

                // Page numbers
                $start = max(1, $this->current_page - 2);
                $end = min($this->last_page, $this->current_page + 2);

                if ($start > 1) {
                    $html .= '<li class="page-item"><a class="page-link" href="' . $this->buildUrl(1, $queryParams) . '">1</a></li>';
                    if ($start > 2) {
                        $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                for ($i = $start; $i <= $end; $i++) {
                    $active = $i == $this->current_page ? ' active' : '';
                    $pageUrl = $this->buildUrl($i, $queryParams);
                    $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $pageUrl . '">' . $i . '</a></li>';
                }

                if ($end < $this->last_page) {
                    if ($end < $this->last_page - 1) {
                        $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    $html .= '<li class="page-item"><a class="page-link" href="' . $this->buildUrl($this->last_page, $queryParams) . '">' . $this->last_page . '</a></li>';
                }

                // Next page
                if ($this->current_page < $this->last_page) {
                    $nextPage = $this->current_page + 1;
                    $nextUrl = $this->buildUrl($nextPage, $queryParams);
                    $html .= '<li class="page-item"><a class="page-link" href="' . $nextUrl . '">' . __('pagination.next') . '</a></li>';
                }

                $html .= '</ul></nav>';
                return $html;
            }

            private function buildUrl($page, $queryParams) {
                $params = array_merge($queryParams, ['page' => $page]);
                return '?' . http_build_query($params);
            }

            // Iterator interface methods
            public function rewind(): void {
                $this->position = 0;
            }

            public function current() {
                return $this->data->get($this->position);
            }

            public function key() {
                return $this->position;
            }

            public function next(): void {
                ++$this->position;
            }

            public function valid(): bool {
                return $this->position < $this->data->count();
            }
        };

        $brandName = $filters['brand'] ? $brands->firstWhere('slug', $filters['brand'])?->name : null;
        $categoryName = $filters['category'] ? $categories->firstWhere('slug', $filters['category'])?->name : null;

        return view('products.index', [
            'products' => $productsWithPagination,
            'pagination' => $result['pagination'],
            'brands' => $brands,
            'categories' => $categories,
            'q' => $filters['search'],
            'brand' => $filters['brand'],
            'category' => $filters['category'],
            'sort' => $filters['sort'],
            'seo' => SeoData::products($filters['search'], $categoryName, $brandName),
        ]);
    }

    // /product/{slug} — Optimized product details with caching
    public function show(string $slug)
    {
        $resolved = $this->resolveLocalized(Product::query()->active()->published(), $slug);
        abort_unless($resolved, 404);
        // Use cached product details
        $product = $this->performance->getCachedProductDetails($resolved->getRawOriginal('slug'));

        if (!$product) {
            abort(404);
        }

        // Convert product array to object with proper nested object conversion
        $productObj = $this->convertArrayToObject($product);

        // Get related products
        $relatedProducts = $this->performance->getCachedRelatedProducts($product['id'], 4);

        // Convert related products arrays to objects
        $relatedProductsObj = collect($relatedProducts)->map(function ($product) {
            return $this->convertArrayToObject($product);
        });

        return view('products.show', [
            'product' => $productObj,
            'related' => $relatedProductsObj,
            'seo' => SeoData::product($productObj),
            'structuredData' => SeoData::productSchemas($productObj),
        ]);
    }

    /**
     * Convert array to object with proper nested object conversion
     */
    private function convertArrayToObject(array $data): object
    {
        $obj = (object) $data;
        
        // Convert nested arrays to objects
        if (isset($data['brand']) && is_array($data['brand'])) {
            $obj->brand = (object) $data['brand'];
        }
        if (isset($data['category']) && is_array($data['category'])) {
            $obj->category = (object) $data['category'];
        }
        if (isset($data['images']) && is_array($data['images'])) {
            $obj->images = collect($data['images'])->map(function ($image) {
                return (object) $image;
            });
        }
        if (isset($data['offers']) && is_array($data['offers'])) {
            $obj->offers = collect($data['offers'])->map(function ($offer) {
                return (object) $offer;
            });
        }
        
        return $obj;
    }

    // /categories — All categories page with caching
    public function categories()
    {
        // Use cached categories with hierarchy
        $categories = $this->performance->getCachedCategories();

        // Convert cached arrays to objects for compatibility with views
        $categories = collect($categories)->map(function ($category) {
            return (object) $category;
        });

        return view('categories.index', compact('categories') + ['seo' => SeoData::listing('Categories - '.config('app.name'), 'Browse Avenue product categories.', route('categories.index'))]);
    }

    // /category/{slug} — Category page with products
    public function category(string $slug, Request $request)
    {
        $category = $this->resolveLocalized(Category::query()->active()
            ->with(['children', 'parent'])
        , $slug);
        abort_unless($category, 404);

        // Include child category IDs (one level) if they exist
        $childIds = $category->children()->pluck('id');
        $catIds   = collect([$category->id])->merge($childIds);

        $sort = $request->input('sort', 'latest');
        $page = $request->input('page', 1);
        $perPage = 12;

        // Use cached products with category filter
        $filters = [
            'category' => $category->getRawOriginal('slug'),
            'sort' => $sort,
        ];

        $result = $this->performance->getCachedProducts($filters, $perPage, $page);

        // Convert cached arrays to objects for compatibility with views
        $products = collect($result['data'])->map(function ($product) {
            $productObj = (object) $product;
            if (isset($product['brand']) && is_array($product['brand'])) {
                $productObj->brand = (object) $product['brand'];
            }
            if (isset($product['category']) && is_array($product['category'])) {
                $productObj->category = (object) $product['category'];
            }
            return $productObj;
        });

        // Create a LengthAwarePaginator manually since we're using cached data
        $pagination = $result['pagination'] ?? [];
        $currentPage = $pagination['current_page'] ?? 1;
        $perPage = $pagination['per_page'] ?? 12;
        $total = $pagination['total'] ?? $products->count();

        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        $products->withQueryString();

        return view('categories.show', compact('category', 'products', 'sort') + ['seo' => SeoData::category($category)]);
    }

    // /brands — All brands page
    public function brands()
    {
        $brands = Brand::where('is_active', 1)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('brands.index', compact('brands') + ['seo' => SeoData::listing('Brands - '.config('app.name'), 'Browse brands available at Avenue.', route('brands.index'))]);
    }

    // /brand/{slug} — سنفصلها بالخطوة القادمة
    public function brand(string $slug, Request $request)
    {
        $brand = $this->resolveLocalized(Brand::query()->active(), $slug);
        abort_unless($brand, 404);

        $products = Product::active()
            ->with(['images','brand','category'])
            ->where('brand_id', $brand->id)
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('products.index', [
            'products'   => $products,
            'brands'     => Brand::where('is_active',1)->orderBy('name')->get(['name','slug']),
            'categories' => Category::active()->orderBy('sort_order')->get(['name','slug']),
            'q'          => '',
            'brand'      => $slug,
            'category'   => null,
            'sort'       => 'latest',
            'pageTitle'  => $brand->name,
            'seo'        => SeoData::brand($brand),
        ]);
    }


    // /wishlist — Wishlist page. Items live in the browser's localStorage
    // (see public/js/enhanced-product-cards.js) and are rendered client-side.
    public function wishlist(Request $request)
    {
        return view('wishlist.index');
    }

    private function resolveLocalized($query, string $slug)
    {
        $field = app()->getLocale() === 'ar' ? 'slug_ar' : 'slug_en';
        $matches = (clone $query)->where($field, $slug)->limit(2)->get();
        if ($matches->count() > 1) abort(404);
        if ($matches->count() === 1) return $matches->first();

        $fallback = (clone $query)->where(function ($q) use ($field): void {
            $q->whereNull($field)->orWhere($field, '');
        });
        if ($field === 'slug_ar') {
            $english = (clone $fallback)->where('slug_en', $slug)->limit(2)->get();
            if ($english->count() > 1) abort(404);
            if ($english->count() === 1) return $english->first();
        }
        $base = $fallback->where('slug', $slug)->limit(2)->get();
        return $base->count() === 1 ? $base->first() : null;
    }
}
