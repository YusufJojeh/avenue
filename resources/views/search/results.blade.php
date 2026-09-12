@extends('layouts.app')

@section('title', 'Search Results - ' . ($q ? '"' . $q . '"' : 'All Products') . ' - ' . ($siteName ?? 'MyStore'))
@section('meta_robots', 'noindex, follow')
@section('canonical', route('search'))

@push('styles')
<style>
  .search-header {
    background: linear-gradient(135deg, rgba(240,194,75,0.1) 0%, rgba(120,119,198,0.1) 100%);
    border-radius: 24px;
    padding: 3rem 2rem;
    margin-bottom: 3rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .search-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
  }

  .search-title {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--text) 0%, var(--gold) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
  }

  .search-subtitle {
    color: var(--muted);
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
  }

  .search-form {
    background: var(--glass);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
  }

  .filters-section {
    background: var(--glass);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
  }

  .filter-group {
    margin-bottom: 1rem;
  }

  .filter-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text);
  }

  .filter-select {
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--text);
    border-radius: 8px;
    padding: 0.5rem;
    filter: none !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    width: 100%;
  }

  .filter-select:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 2px rgba(240,194,75,0.2);
  }

  .sort-options {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }

  .sort-option {
    padding: 0.5rem 1rem;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--surface);
    color: var(--text);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.875rem;
  }

  .sort-option:hover,
  .sort-option.active {
    background: var(--gold);
    color: #111216;
    border-color: var(--gold);
    text-decoration: none;
  }

  .results-count {
    color: var(--muted);
    margin-bottom: 1rem;
    text-align: center;
  }

  .product-card {
    background: linear-gradient(135deg,
      rgba(240,194,75,0.1) 0%,
      rgba(120,119,198,0.1) 100%);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(240,194,75,0.3);
    border-radius: 24px;
    box-shadow:
      0 8px 32px rgba(0,0,0,0.1),
      inset 0 1px 0 rgba(240,194,75,0.2);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    height: 100%;
  }

  .product-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg,
      rgba(240,194,75,0.1) 0%,
      transparent 50%,
      rgba(120,119,198,0.1) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .product-card:hover::before {
    opacity: 1;
  }

  .product-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow:
      0 20px 40px rgba(0,0,0,0.15),
      0 0 0 1px rgba(240,194,75,0.4),
      inset 0 1px 0 rgba(240,194,75,0.3);
  }

  .product-card .thumb {
    aspect-ratio: 1/1;
    background: var(--surface);
    border-radius: 24px 24px 0 0;
    overflow: hidden;
    position: relative;
  }

  .product-card .thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .product-card:hover .thumb img {
    transform: scale(1.1);
  }

  .sale-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    z-index: 2;
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }

  .no-results {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--muted);
  }

  .no-results-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
  }

  .search-suggestions {
    margin-top: 2rem;
    padding: 1.5rem;
    background: var(--glass);
    border: 1px solid var(--border);
    border-radius: 16px;
  }

  .suggestion-title {
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--text);
  }

  .suggestion-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .suggestion-item {
    margin-bottom: 0.5rem;
  }

  .suggestion-link {
    color: var(--gold);
    text-decoration: none;
    transition: color 0.3s ease;
  }

  .suggestion-link:hover {
    color: var(--text);
    text-decoration: underline;
  }

  /* Dark mode enhancements */
  html[data-theme="dark"] .search-header {
    background: linear-gradient(135deg, rgba(240,194,75,0.15) 0%, rgba(120,119,198,0.15) 100%);
  }

  html[data-theme="dark"] .search-form,
  html[data-theme="dark"] .filters-section {
    background: rgba(26,26,26,0.8);
    border-color: rgba(255,255,255,0.1);
  }

  html[data-theme="dark"] .filter-select {
    background: rgba(26,26,26,0.9);
    border-color: rgba(255,255,255,0.1);
    color: #f0f0f0;
  }

  html[data-theme="dark"] .sort-option {
    background: rgba(26,26,26,0.9);
    border-color: rgba(255,255,255,0.1);
    color: #f0f0f0;
  }

  html[data-theme="dark"] .product-card {
    background: linear-gradient(135deg,
      rgba(240,194,75,0.15) 0%,
      rgba(120,119,198,0.15) 100%);
    box-shadow:
      0 8px 32px rgba(0,0,0,0.3),
      inset 0 1px 0 rgba(240,194,75,0.2);
  }

  html[data-theme="dark"] .product-card:hover {
    box-shadow:
      0 20px 40px rgba(0,0,0,0.4),
      0 0 0 1px rgba(240,194,75,0.4),
      inset 0 1px 0 rgba(240,194,75,0.3);
  }

  html[data-theme="dark"] .search-suggestions {
    background: rgba(26,26,26,0.8);
    border-color: rgba(255,255,255,0.1);
  }
</style>
@endpush

@section('content')

{{-- Search Header --}}
<section class="search-header reveal">
  <div class="container position-relative">
    <h1 class="search-title">
      @if($q)
        Search Results for "{{ $q }}"
      @else
        All Products
      @endif
    </h1>
    <p class="search-subtitle">
      @if($q)
        Found {{ $products->total() }} product{{ $products->total() != 1 ? 's' : '' }} matching your search.
      @else
        Browse our complete collection of products.
      @endif
    </p>
  </div>
</section>

{{-- Search Form --}}
<section class="py-3 reveal">
  <div class="container">
    <form class="search-form" action="{{ route('products.index') }}" method="get">
      <div class="row g-3">
        <div class="col-md-6">
          <input type="text"
                 class="form-control"
                 name="q"
                 placeholder="Search products..."
                 value="{{ $q }}"
                 required>
        </div>
        <div class="col-md-2">
          <select name="category" class="filter-select">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->slug }}" {{ $category == $cat->slug ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="brand" class="filter-select">
            <option value="">All Brands</option>
            @foreach($brands as $br)
              <option value="{{ $br->slug }}" {{ $brand == $br->slug ? 'selected' : '' }}>
                {{ $br->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-vel-gold w-100">Search</button>
        </div>
      </div>
    </form>
  </div>
</section>

{{-- Filters and Results --}}
<section class="py-3 reveal">
  <div class="container">
    <div class="row">
      {{-- Filters Sidebar --}}
      <div class="col-lg-3">
        <div class="filters-section">
          <h5 class="filter-label">Sort By</h5>
          <div class="sort-options">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}"
               class="sort-option {{ $sort == 'latest' ? 'active' : '' }}">
              Latest
            </a>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}"
               class="sort-option {{ $sort == 'price_low' ? 'active' : '' }}">
              Price Low
            </a>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}"
               class="sort-option {{ $sort == 'price_high' ? 'active' : '' }}">
              Price High
            </a>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name']) }}"
               class="sort-option {{ $sort == 'name' ? 'active' : '' }}">
              Name
            </a>
          </div>

          @if($q)
            <div class="search-suggestions">
              <h6 class="suggestion-title">Search Suggestions</h6>
              <ul class="suggestion-list">
                <li class="suggestion-item">
                  <a href="{{ route('products.index', ['q' => 'featured']) }}" class="suggestion-link">
                    Featured Products
                  </a>
                </li>
                <li class="suggestion-item">
                  <a href="{{ route('products.index', ['q' => 'new']) }}" class="suggestion-link">
                    New Arrivals
                  </a>
                </li>
                <li class="suggestion-item">
                  <a href="{{ route('products.index', ['q' => 'sale']) }}" class="suggestion-link">
                    On Sale
                  </a>
                </li>
              </ul>
            </div>
          @endif
        </div>
      </div>

      {{-- Results --}}
      <div class="col-lg-9">
        <div class="results-count">
          Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results
        </div>

        @if($products->count() > 0)
          <div class="row g-4">
            @foreach($products as $product)
              <div class="col-6 col-md-4">
                <x-product-card :product="$product" />
              </div>
            @endforeach
          </div>

          {{-- Pagination --}}
          @if($products->hasPages())
            <div class="d-flex justify-content-center mt-5">
              {!! $products->links() !!}
            </div>
          @endif
        @else
          <div class="no-results">
            <div class="no-results-icon">🔍</div>
            <h3>No products found</h3>
            <p>
              @if($q)
                We couldn't find any products matching "{{ $q }}".
              @else
                No products available at the moment.
              @endif
            </p>
            <div class="d-flex gap-2 justify-content-center">
              <a href="{{ route('products.index') }}" class="btn btn-vel-gold">
                Browse All Products
              </a>
              <a href="{{ route('categories.index') }}" class="btn btn-vel-outline">
                Browse Categories
              </a>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>

@endsection
