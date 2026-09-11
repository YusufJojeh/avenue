<?php

namespace App\Http\Controllers;

use App\Services\EnhancedPerformanceService;
use App\Services\PageCacheService;
use App\Models\Slide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        private EnhancedPerformanceService $performance,
        private PageCacheService $pageCache
    ) {}

    /**
     * Home page with optimized caching
     */
    public function index()
    {
        $visibility = [];

        try {
            $visibility = app(HomeVisibilityController::class)->getVisibilitySettings();

            // Get cached data for home page components
            $featuredProducts = $this->performance->getCachedFeaturedProducts(8);
            $navigation = $this->performance->getCachedNavigation();
            $siteSettings = $this->performance->getCachedSiteSettings();
            
            // Get latest products
            $latestProducts = $this->performance->getCachedProducts(['sort' => 'latest'], 8, 1);
            
            // Get categories for stats and display
            $categories = $this->performance->getCachedCategories();
            
            // Convert cached arrays to objects for compatibility with views
            $specialProducts = collect($featuredProducts)->map(function ($product) {
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
            $categoriesCollection = collect($categories)->map(function ($category) {
                return (object) $category;
            });
            
            // Get slides for hero and slider sections
            try {
                $mainSlide = Slide::position('main')->current()->first();
                $sliderSlides = Slide::position('slider')->current()->orderBy('sort_order')->get();
            } catch (\Exception $e) {
                // If slides table doesn't exist or query fails, use empty collections
                $mainSlide = null;
                $sliderSlides = collect([]);
            }
            
            // Mock data for missing variables
            $offers = collect([]);
            
            return view('home', [
                'specialProducts' => $specialProducts,
                'featuredProducts' => $featuredProducts,
                'latestProducts' => $latestProducts['data'],
                'navigation' => $navigation,
                'settings' => $siteSettings,
                'categories' => $categoriesCollection,
                'offers' => $offers,
                'mainSlide' => $mainSlide,
                'sliderSlides' => $sliderSlides,
                'visibility' => $visibility,
                'siteName' => $siteSettings['site_name'] ?? 'E-Commerce Store',
            ]);
        } catch (\Exception $e) {
            // Fallback to basic data if caching fails
            try {
                $mainSlide = Slide::position('main')->current()->first();
                $sliderSlides = Slide::position('slider')->current()->orderBy('sort_order')->get();
            } catch (\Exception $slideException) {
                $mainSlide = null;
                $sliderSlides = collect([]);
            }
            
            return view('home', [
                'specialProducts' => collect([]),
                'featuredProducts' => [],
                'latestProducts' => ['data' => []],
                'navigation' => ['categories' => [], 'brands' => []],
                'settings' => [],
                'categories' => collect([]),
                'offers' => collect([]),
                'mainSlide' => $mainSlide,
                'sliderSlides' => $sliderSlides,
                'visibility' => $visibility,
                'siteName' => 'E-Commerce Store',
            ]);
        }
    }

    /**
     * Search with optimized caching
     */
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        $page = $request->input('page', 1);
        
        if (empty($query)) {
            return redirect()->route('products.index');
        }

        $filters = [
            'search' => $query,
            'sort' => $request->input('sort', 'relevance'),
        ];

        // Use cached search results
        $result = $this->performance->getCachedProducts($filters, 12, $page);
        
        // Get search suggestions
        $suggestions = $this->performance->getCachedSearchSuggestions($query, 10);
        
        // Get cached navigation for filters
        $navigation = $this->performance->getCachedNavigation();

        return view('search.results', [
            'products' => $result['data'],
            'pagination' => $result['pagination'],
            'q' => $query,
            'suggestions' => $suggestions,
            'brands' => $navigation['brands'],
            'categories' => $navigation['categories'],
            'sort' => $filters['sort'],
            'category' => null,
            'brand' => null,
        ]);
    }

    /**
     * Get search suggestions via AJAX
     */
    public function searchSuggestions(Request $request)
    {
        try {
            $query = trim($request->input('q', ''));
            
            // Use mb_strlen for proper Arabic character counting
            if (mb_strlen($query, 'UTF-8') < 2) {
                return response()->json([]);
            }

            $suggestions = $this->performance->getCachedSearchSuggestions($query, 8);
            
            return response()->json($suggestions, 200, [
                'Content-Type' => 'application/json; charset=utf-8',
                'Cache-Control' => 'public, max-age=300'
            ]);
        } catch (\Exception $e) {
            \Log::error('Search suggestions error: ' . $e->getMessage(), [
                'query' => $request->input('q'),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([], 200);
        }
    }

    /**
     * About page with caching
     */
    public function about()
    {
        $content = $this->pageCache->cacheFragment('about_page', function () {
            $siteSettings = $this->performance->getCachedSiteSettings();
            return view('pages.about', ['settings' => $siteSettings])->render();
        }, 86400); // Cache for 24 hours

        return response($content)
            ->header('Cache-Control', 'public, max-age=86400');
    }

    /**
     * Contact page with caching
     */
    public function contact()
    {
        $content = $this->pageCache->cacheFragment('contact_page', function () {
            $siteSettings = $this->performance->getCachedSiteSettings();
            return view('pages.contact', ['settings' => $siteSettings])->render();
        }, 86400); // Cache for 24 hours

        return response($content)
            ->header('Cache-Control', 'public, max-age=86400');
    }

    /**
     * Privacy policy page with caching
     */
    public function privacy()
    {
        $content = $this->pageCache->cacheFragment('privacy_page', function () {
            $siteSettings = $this->performance->getCachedSiteSettings();
            return view('pages.privacy', ['settings' => $siteSettings])->render();
        }, 86400); // Cache for 24 hours

        return response($content)
            ->header('Cache-Control', 'public, max-age=86400');
    }

    /**
     * Terms of service page with caching
     */
    public function terms()
    {
        $content = $this->pageCache->cacheFragment('terms_page', function () {
            $siteSettings = $this->performance->getCachedSiteSettings();
            return view('pages.terms', ['settings' => $siteSettings])->render();
        }, 86400); // Cache for 24 hours

        return response($content)
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
