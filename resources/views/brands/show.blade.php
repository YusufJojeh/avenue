@extends('layouts.app')

@section('title', $brand->name . ' - ' . ($siteName ?? 'MyStore'))

@push('styles')
<style>
  .brand-header {
    background: linear-gradient(135deg, rgba(240,194,75,0.1) 0%, rgba(120,119,198,0.1) 100%);
    border-radius: 24px;
    padding: 3rem 2rem;
    margin-bottom: 3rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .brand-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
  }

  .brand-logo {
    width: 120px;
    height: 120px;
    object-fit: contain;
    margin: 0 auto 1.5rem;
    display: block;
    background: var(--surface);
    border-radius: 16px;
    padding: 1rem;
    border: 1px solid var(--border);
  }

  .brand-name {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--text) 0%, var(--gold) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
  }

  .brand-description {
    color: var(--muted);
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
  }

  .brand-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-top: 2rem;
  }

  .brand-stat {
    text-align: center;
  }

  .brand-stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gold);
  }

  .brand-stat-label {
    color: var(--muted);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .section-title {
    font-family: 'Georgia', serif;
    font-weight: 700;
    font-size: 2rem;
    background: linear-gradient(135deg, var(--text) 0%, var(--gold) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    margin-bottom: 2rem;
    text-align: center;
  }

  .section-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, var(--gold), transparent);
    border-radius: 2px;
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

  .no-products {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--muted);
  }

  .no-products-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
  }

  /* Dark mode enhancements */
  html[data-theme="dark"] .brand-header {
    background: linear-gradient(135deg, rgba(240,194,75,0.15) 0%, rgba(120,119,198,0.15) 100%);
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
</style>
@endpush

@section('content')

{{-- Brand Header --}}
<section class="brand-header reveal">
  <div class="container position-relative">
    @if($brand->logo_url)
      <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="brand-logo">
    @endif

    <h1 class="brand-name">{{ $brand->name }}</h1>

    @if($brand->description)
      <p class="brand-description">{{ $brand->description }}</p>
    @endif

    <div class="brand-stats">
      <div class="brand-stat">
        <div class="brand-stat-number">{{ $products->total() }}</div>
        <div class="brand-stat-label">Products</div>
      </div>
      <div class="brand-stat">
        <div class="brand-stat-number">{{ $brand->is_external ? 'External' : 'Internal' }}</div>
        <div class="brand-stat-label">Type</div>
      </div>
    </div>
  </div>
</section>

{{-- Products Section --}}
<section class="py-5 reveal">
  <div class="container">
    <h2 class="section-title">Products by {{ $brand->name }}</h2>

    @if($products->count() > 0)
      <div class="row g-4">
        @foreach($products as $product)
          <div class="col-6 col-md-3">
            @includeIf('partials.product-card', ['p'=>$product])
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
      <div class="no-products">
        <div class="no-products-icon">📦</div>
        <h3>No products found</h3>
        <p>This brand doesn't have any products yet.</p>
        <a href="{{ route('products.index') }}" class="btn btn-vel-gold">
          Browse All Products
        </a>
      </div>
    @endif
  </div>
</section>

@endsection
