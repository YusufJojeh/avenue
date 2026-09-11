@extends('layouts.app')

@section('title', $pageTitle ?? 'Products')

@push('styles')
<style>
  /* ===== Modern Products Page Styles ===== */

  /* Page Header */
  .page-header {
    text-align: center;
    padding: 2rem 0;
  }

  .page-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 1rem;
  }

  .page-header p {
    font-size: 1.1rem;
    color: var(--muted);
    margin-bottom: 0;
  }

  /* Advanced Filters Section */
  .filters-section {
    background: var(--glass);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
  }

  .filter-group {
    margin-bottom: 1.5rem;
  }

  .filter-group:last-child {
    margin-bottom: 0;
  }

  .filter-label {
    font-weight: 700;
    color: var(--text);
    margin-bottom: 0.75rem;
    display: block;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .filter-input {
    background: var(--surface);
    border: 2px solid var(--border);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    color: var(--text);
    transition: all 0.3s ease;
    width: 100%;
    font-size: 0.95rem;
  }

  .filter-input:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(240,194,75,0.1);
    background: var(--glass-strong);
  }

  .filter-input::placeholder {
    color: var(--muted);
  }

  .filter-select {
    background: var(--surface);
    border: 2px solid var(--border);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    color: var(--text);
    filter: none !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    transition: all 0.3s ease;
    width: 100%;
    font-size: 0.95rem;
    cursor: pointer;
  }

  .filter-select:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(240,194,75,0.1);
  }

  /* Filter Pills */
  .filter-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1.5rem;
  }

  .filter-pill {
    background: linear-gradient(135deg, var(--gold), #f59e0b);
    color: #111216;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 4px 12px rgba(240,194,75,0.3);
    transition: all 0.3s ease;
  }

  .filter-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(240,194,75,0.4);
  }

  .filter-pill .remove {
    background: rgba(17,18,22,0.2);
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .filter-pill .remove:hover {
    background: rgba(17,18,22,0.3);
    transform: scale(1.1);
  }

  /* Action Buttons */
  .filter-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    flex-wrap: wrap;
  }

  .btn-filter {
    background: linear-gradient(135deg, var(--gold), #f59e0b);
    color: #111216;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(240,194,75,0.3);
  }

  .btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(240,194,75,0.4);
    color: #111216;
  }

  .btn-reset {
    background: transparent;
    color: var(--muted);
    border: 2px solid var(--border);
    padding: 0.75rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
  }

  .btn-reset:hover {
    background: var(--surface);
    border-color: var(--gold);
    color: var(--text);
  }

  /* Results Header */
  .results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .results-count {
    font-size: 1.1rem;
    color: var(--muted);
    font-weight: 600;
  }

  .results-count strong {
    color: var(--gold);
    font-weight: 800;
  }

  .view-toggle {
    display: flex;
    background: var(--surface);
    border-radius: 12px;
    padding: 0.25rem;
    border: 1px solid var(--border);
  }

  .view-btn {
    background: transparent;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    color: var(--muted);
    transition: all 0.3s ease;
    cursor: pointer;
  }

  .view-btn.active {
    background: var(--gold);
    color: #111216;
    box-shadow: 0 2px 8px rgba(240,194,75,0.3);
  }

  /* Products Grid */
  .products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
  }

  .products-grid.list-view {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .products-grid.list-view .product-card {
    display: flex;
    flex-direction: row;
    height: auto;
  }

  .products-grid.list-view .product-card .thumb {
    width: 200px;
    height: 200px;
    flex-shrink: 0;
  }

  .products-grid.list-view .product-card .card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  /* Empty State */
  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--glass);
    border-radius: 20px;
    border: 1px solid var(--border);
  }

  .empty-state-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    opacity: 0.6;
  }

  .empty-state h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 1rem;
  }

  .empty-state p {
    color: var(--muted);
    font-size: 1.1rem;
    margin-bottom: 2rem;
  }

  /* Pagination */
  .pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 3rem;
    padding: 2rem 0;
  }

  .pagination {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .pagination .page-link {
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--text);
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    transition: all 0.3s ease;
    font-weight: 500;
    font-size: 0.9rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
  }

  .pagination .page-link:hover {
    background: var(--gold);
    color: #111216;
    border-color: var(--gold);
  }

  .pagination .page-item.active .page-link {
    background: var(--gold);
    color: #111216;
    border-color: var(--gold);
    font-weight: 600;
  }

  .pagination .page-item.disabled .page-link {
    background: var(--surface);
    color: var(--muted);
    border-color: var(--border);
    cursor: not-allowed;
    opacity: 0.5;
  }

  .pagination .page-item.disabled .page-link:hover {
    background: var(--surface);
    color: var(--muted);
    border-color: var(--border);
  }

  /* Loading States */
  .loading-skeleton {
    background: linear-gradient(90deg, var(--surface) 25%, var(--glass) 50%, var(--surface) 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 12px;
  }

  @keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .page-header {
      padding: 1.5rem 0;
    }

    .page-header h1 {
      font-size: 2rem;
    }

    .page-header p {
      font-size: 1rem;
    }

    .filters-section {
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .products-grid {
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 1.5rem;
    }

    .results-header {
      flex-direction: column;
      align-items: stretch;
    }

    .filter-actions {
      flex-direction: column;
    }

    .btn-filter, .btn-reset {
      width: 100%;
      text-align: center;
    }

    /* Mobile-specific improvements */
    .filter-group {
      margin-bottom: 1rem;
    }

    .filter-label {
      font-size: 0.9rem;
      margin-bottom: 0.5rem;
    }

    .filter-input, .filter-select {
      padding: 0.6rem 0.8rem;
      font-size: 0.9rem;
    }
  }

  @media (max-width: 576px) {
    .products-grid {
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1rem;
    }

    .products-grid.list-view .product-card {
      flex-direction: column;
    }

    .products-grid.list-view .product-card .thumb {
      width: 100%;
      height: 200px;
    }

    .filter-pills {
      gap: 0.5rem;
    }

    .filter-pill {
      font-size: 0.8rem;
      padding: 0.4rem 0.8rem;
    }

    /* Extra small screens */
    .page-header {
      padding: 1rem 0;
    }

    .page-header h1 {
      font-size: 1.75rem;
    }

    .filters-section {
      padding: 1rem;
    }

    .results-count {
      font-size: 1rem;
      text-align: center;
    }

    .empty-state {
      padding: 2rem 1rem;
    }

    .empty-state h3 {
      font-size: 1.25rem;
    }

    .empty-state p {
      font-size: 1rem;
    }

    /* Pagination responsive */
    .pagination {
      gap: 0.4rem;
    }

    .pagination .page-link {
      padding: 0.4rem 0.6rem;
      font-size: 0.8rem;
      min-width: 32px;
    }
  }

  /* Dark Mode Enhancements */
  html[data-theme="dark"] .page-header h1 {
    color: #ffffff;
  }

  html[data-theme="dark"] .filters-section {
    background: rgba(22,26,32,0.8);
    border-color: rgba(255,255,255,0.1);
  }

  html[data-theme="dark"] .empty-state {
    background: rgba(22,26,32,0.8);
    border-color: rgba(255,255,255,0.1);
  }

  html[data-theme="dark"] .pagination {
    background: rgba(22,26,32,0.8);
    border-color: rgba(255,255,255,0.1);
  }

  /* Animation Enhancements */
  .product-card {
    animation: fadeInUp 0.6s ease-out;
  }

  .product-card:nth-child(1) { animation-delay: 0.1s; }
  .product-card:nth-child(2) { animation-delay: 0.2s; }
  .product-card:nth-child(3) { animation-delay: 0.3s; }
  .product-card:nth-child(4) { animation-delay: 0.4s; }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>
@endpush

@section('content')
@php
  $q         = $q         ?? request('q', '');
  $sort      = $sort      ?? request('sort', 'latest');
  $brand     = request('brand');
  $category  = request('category');
  $view      = request('view', 'grid'); // grid or list
@endphp

<div class="container py-4">

  <x-enhanced-breadcrumb :items="[
    ['url' => url('/'), 'label' => __('common.nav.home')],
    ['label' => __('common.nav.products')],
  ]" />

  {{-- ================= Page Title ================= --}}
  <div class="page-header mb-4">
    <h1>{{ $pageTitle ?? 'All Products' }}</h1>
    <p class="text-muted">Discover amazing products and find exactly what you're looking for</p>
  </div>

  {{-- ================= Advanced Filters ================= --}}
  <div class="filters-section">
    <form method="get" id="filtersForm">
      <div class="row g-3">
        {{-- Search Input --}}
        <div class="col-12 col-md-6 col-lg-4">
          <label class="filter-label">Search Products</label>
          <input type="text"
                 name="q"
                 value="{{ $q }}"
                 class="filter-input"
                 placeholder="Search by name or description"
                 id="searchInput">
        </div>

        {{-- Brand Filter --}}
        <div class="col-6 col-md-3 col-lg-2">
          <label class="filter-label">Brand</label>
          <select name="brand" class="filter-select" id="brandSelect">
            <option value="">All Brands</option>
            @foreach($brands as $b)
              <option value="{{ $b->slug }}" @selected($brand===$b->slug)>{{ $b->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Category Filter --}}
        <div class="col-6 col-md-3 col-lg-2">
          <label class="filter-label">Category</label>
          <select name="category" class="filter-select" id="categorySelect">
            <option value="">All Categories</option>
            @foreach($categories as $c)
              <option value="{{ $c->slug }}" @selected($category===$c->slug)>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Sort Filter --}}
        <div class="col-6 col-md-3 col-lg-2">
          <label class="filter-label">Sort By</label>
          <select name="sort" class="filter-select" id="sortSelect">
            <option value="latest" @selected($sort==='latest')>Latest First</option>
            <option value="price_low" @selected($sort==='price_low')>Price: Low to High</option>
            <option value="price_high" @selected($sort==='price_high')>Price: High to Low</option>
            <option value="name" @selected($sort==='name')>Name: A to Z</option>
          </select>
        </div>

        {{-- View Toggle --}}
        <div class="col-6 col-md-3 col-lg-2">
          <label class="filter-label">View</label>
          <div class="view-toggle">
            <button type="button" class="view-btn {{ $view === 'grid' ? 'active' : '' }}" data-view="grid">
              <i class="fas fa-th"></i>
            </button>
            <button type="button" class="view-btn {{ $view === 'list' ? 'active' : '' }}" data-view="list">
              <i class="fas fa-list"></i>
            </button>
          </div>
        </div>
      </div>

      {{-- Active Filters Pills --}}
      @if($q || $brand || $category)
        <div class="filter-pills">
          @if($q)
            <span class="filter-pill">
              Search: <strong>{{ $q }}</strong>
              <span class="remove" onclick="removeFilter('q')">×</span>
            </span>
          @endif
          @if($brand)
            <span class="filter-pill">
              Brand: <strong>{{ $brands->firstWhere('slug',$brand)->name ?? $brand }}</strong>
              <span class="remove" onclick="removeFilter('brand')">×</span>
            </span>
          @endif
          @if($category)
            <span class="filter-pill">
              Category: <strong>{{ $categories->firstWhere('slug',$category)->name ?? $category }}</strong>
              <span class="remove" onclick="removeFilter('category')">×</span>
            </span>
          @endif
        </div>
      @endif

      {{-- Action Buttons --}}
      <div class="filter-actions">
        <button type="submit" class="btn-filter">
          <i class="fas fa-search me-2"></i>Apply Filters
        </button>
        <a href="{{ route('products.index') }}" class="btn-reset">
          <i class="fas fa-refresh me-2"></i>Reset All
        </a>
      </div>
    </form>
  </div>

  {{-- ================= Results Header ================= --}}
  <div class="results-header">
    <div class="results-count">
      Showing <strong>{{ $products->count() }}</strong> of <strong>{{ $products->total() }}</strong> products
    </div>
  </div>

  {{-- ================= Products Grid ================= --}}
  <div class="products-grid {{ $view === 'list' ? 'list-view' : '' }}" id="productsGrid">
    @forelse($products as $p)
      <div class="product-card-wrapper">
        @includeIf('partials.product-card', ['p'=>$p])
      </div>
    @empty
      <div class="col-12">
        <div class="empty-state">
          <div class="empty-state-icon">🔍</div>
          <h3>No Products Found</h3>
          <p>We couldn't find any products matching your criteria</p>
          <a href="{{ route('products.index') }}" class="btn btn-vel-gold">
            <i class="fas fa-refresh me-2"></i>View All Products
          </a>
        </div>
      </div>
    @endforelse
  </div>

  {{-- ================= Pagination ================= --}}
  @if($products->hasPages())
    <div class="pagination-wrapper">
      {!! $products->appends(request()->query())->links() !!}
    </div>
  @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // View Toggle Functionality
  const viewButtons = document.querySelectorAll('.view-btn');
  const productsGrid = document.getElementById('productsGrid');

  viewButtons.forEach(button => {
    button.addEventListener('click', function() {
      const view = this.dataset.view;

      // Update active state
      viewButtons.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');

      // Update grid class
      if (view === 'list') {
        productsGrid.classList.add('list-view');
      } else {
        productsGrid.classList.remove('list-view');
      }

      // Update URL parameter
      const url = new URL(window.location);
      url.searchParams.set('view', view);
      window.history.pushState({}, '', url);
    });
  });

  // Auto-submit form on filter change
  const filterInputs = document.querySelectorAll('#filtersForm select, #filtersForm input[type="text"]');
  filterInputs.forEach(input => {
    input.addEventListener('change', function() {
      // Add loading state
      const form = document.getElementById('filtersForm');
      form.style.opacity = '0.7';
      form.style.pointerEvents = 'none';

      // Submit form
      setTimeout(() => {
        form.submit();
      }, 300);
    });
  });

  // Search input with debounce
  const searchInput = document.getElementById('searchInput');
  let searchTimeout;

  searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      if (this.value.length >= 2 || this.value.length === 0) {
        document.getElementById('filtersForm').submit();
      }
    }, 500);
  });

  // Remove filter function
  window.removeFilter = function(filterName) {
    const url = new URL(window.location);
    url.searchParams.delete(filterName);
    window.location.href = url.toString();
  };

  // Initialize view from URL
  const urlParams = new URLSearchParams(window.location.search);
  const viewParam = urlParams.get('view');
  if (viewParam) {
    const viewBtn = document.querySelector(`[data-view="${viewParam}"]`);
    if (viewBtn) {
      viewBtn.click();
    }
  }

  // Add loading animation to product cards
  const productCards = document.querySelectorAll('.product-card');
  productCards.forEach((card, index) => {
    card.style.animationDelay = `${index * 0.1}s`;
  });

  // Intersection Observer for animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);

  // Observe all product cards
  productCards.forEach(card => {
    observer.observe(card);
  });

  // Add smooth scroll to pagination
  document.querySelectorAll('.pagination .page-link').forEach(link => {
    link.addEventListener('click', function(e) {
      // Add a small delay to allow the page to start loading
      setTimeout(() => {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      }, 100);
    });
  });
});
</script>
@endpush
