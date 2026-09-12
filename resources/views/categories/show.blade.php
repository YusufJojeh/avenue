@extends('layouts.app')

@section('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": @json($category->name),
  "url": @json(route('categories.show', ['slug' => $category->slug])),
  "description": @json(Str::limit(strip_tags($category->description ?? ''), 300))
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    { "@type": "ListItem", "position": 1, "name": "Home", "item": @json(url('/')) },
    { "@type": "ListItem", "position": 2, "name": "Categories", "item": @json(route('categories.index')) },
    { "@type": "ListItem", "position": 3, "name": @json($category->name) }
  ]
}
</script>
@endsection

@push('styles')
<style>
  .category-header {
    background: var(--glass);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 2rem;
    margin-bottom: 2rem;
    text-align: center;
  }

  .category-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 1rem;
    border: 3px solid var(--gold);
    box-shadow: 0 8px 24px rgba(240,194,75,.2);
  }

  .category-description {
    color: var(--muted);
    max-width: 600px;
    margin: 0 auto;
  }

  .filters .form-label{font-weight:600}
  .pill{background:var(--glass); border:1px solid var(--border); padding:.35rem .7rem; border-radius:999px; display:inline-flex; gap:.4rem; align-items:center}
  .pill .x{cursor:pointer; opacity:.6}
  .product-card .thumb{aspect-ratio:1/1; background:var(--surface); border-radius:14px; overflow:hidden}
  .product-card .thumb img{width:100%; height:100%; object-fit:cover; transform:scale(1.01); transition:transform .25s ease}
  .product-card:hover .thumb img{transform:scale(1.04)}
  .price-cut{ text-decoration: line-through; opacity:.6 }

  .breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 1rem;
  }

  .breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: var(--muted);
  }

  .breadcrumb-link {
    color: var(--text);
    text-decoration: none;
    transition: color 0.2s ease;
  }

  .breadcrumb-link:hover {
    color: var(--gold);
  }

  .breadcrumb-item.active .breadcrumb-link {
    color: var(--muted);
    pointer-events: none;
  }
</style>
@endpush

@section('content')
<div class="container py-4">
  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{ route('home') }}" class="breadcrumb-link">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{ route('products.index') }}" class="breadcrumb-link">Products</a>
      </li>
      @if($category->parent)
        <li class="breadcrumb-item">
          <a href="{{ route('categories.show', ['slug' => $category->parent->slug]) }}" class="breadcrumb-link">{{ $category->parent->name }}</a>
        </li>
      @endif
      <li class="breadcrumb-item active" aria-current="page">
        <span class="breadcrumb-link">{{ $category->name }}</span>
      </li>
    </ol>
  </nav>

  {{-- Category Header --}}
  <div class="category-header">
    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="category-image" width="120" height="120" decoding="async" data-fallback-hide="true">

    <h1 class="h2 mb-2">{{ $category->name }}</h1>

    @if($category->description)
      <p class="category-description">{{ $category->description }}</p>
    @endif

    @if($category->children->count() > 0)
      <div class="mt-3">
        <h6 class="text-muted mb-2">Subcategories:</h6>
        <div class="d-flex gap-2 flex-wrap justify-content-center">
          @foreach($category->children as $child)
            <a href="{{ route('categories.show', ['slug' => $child->slug]) }}" class="btn btn-sm btn-vel-outline">
              {{ $child->name }}
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>

  {{-- Products Count --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h5 mb-0">{{ $products->total() }} products found</h2>

    {{-- Sort Options --}}
    <div class="d-flex align-items-center gap-2">
      <label class="form-label mb-0">Sort:</label>
      <select class="form-select form-select-sm" onchange="window.location.href=this.value" style="width: auto;">
        <option value="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}" @selected(request('sort', 'latest') === 'latest')>Latest</option>
        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" @selected(request('sort') === 'price_low')>Price: Low → High</option>
        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" @selected(request('sort') === 'price_high')>Price: High → Low</option>
        <option value="{{ request()->fullUrlWithQuery(['sort' => 'name']) }}" @selected(request('sort') === 'name')>Name (A–Z)</option>
      </select>
    </div>
  </div>

  {{-- Products Grid --}}
  <div class="row g-3">
    @forelse($products as $product)
      <div class="col-6 col-md-3">
        <x-product-card :product="$product" />
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info text-center">
          <h5>No products found in this category</h5>
          <p class="mb-0">Check back later for new products or browse our other categories.</p>
          <a href="{{ route('products.index') }}" class="btn btn-vel-gold mt-2">Browse All Products</a>
        </div>
      </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  @if($products->hasPages())
    <div class="mt-4">
      {!! $products->appends(request()->query())->links() !!}
    </div>
  @endif

  {{-- Related Categories --}}
  @if($category->parent && $category->parent->children->count() > 1)
    <div class="mt-5 pt-4 border-top">
      <h3 class="h5 mb-3">Other categories in {{ $category->parent->name }}</h3>
      <div class="row g-2">
        @foreach($category->parent->children->where('id', '!=', $category->id) as $sibling)
          <div class="col-6 col-md-3">
            <a href="{{ route('categories.show', ['slug' => $sibling->slug]) }}" class="card text-decoration-none">
              <div class="card-body text-center">
                @if($sibling->image_path)
                  <img src="{{ $sibling->image_url }}" alt="{{ $sibling->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; margin-bottom: 0.5rem;">
                @endif
                <div class="fw-semibold">{{ $sibling->name }}</div>
                <div class="small text-muted">{{ $sibling->products_count ?? 0 }} products</div>
              </div>
            </a>
          </div>
        @endforeach
      </div>
    </div>
  @endif
</div>
@endsection
