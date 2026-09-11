@extends('layouts.app')

@section('title', 'Brands - ' . ($siteName ?? 'MyStore'))

@push('styles')
<style>
  .brands-header {
    background: linear-gradient(135deg, rgba(240,194,75,0.1) 0%, rgba(120,119,198,0.1) 100%);
    border-radius: 24px;
    padding: 4rem 2rem;
    margin-bottom: 3rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .brands-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
  }

  .brands-title {
    font-size: 3rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--text) 0%, var(--gold) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
  }

  .brands-subtitle {
    color: var(--muted);
    font-size: 1.2rem;
    max-width: 600px;
    margin: 0 auto;
  }

  .brand-card {
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
    text-decoration: none;
    color: inherit;
  }

  .brand-card::before {
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

  .brand-card:hover::before {
    opacity: 1;
  }

  .brand-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow:
      0 20px 40px rgba(0,0,0,0.15),
      0 0 0 1px rgba(240,194,75,0.4),
      inset 0 1px 0 rgba(240,194,75,0.3);
    text-decoration: none;
    color: inherit;
  }

  .brand-logo {
    width: 100%;
    height: 200px;
    object-fit: contain;
    background: var(--surface);
    border-radius: 24px 24px 0 0;
    padding: 2rem;
    border-bottom: 1px solid var(--border);
  }

  .brand-info {
    padding: 1.5rem;
    text-align: center;
  }

  .brand-name {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text);
  }

  .brand-meta {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 1rem;
  }

  .brand-meta-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
    color: var(--muted);
  }

  .brand-type-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .brand-type-badge.internal {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.2);
  }

  .brand-type-badge.external {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.2);
  }

  .brand-type {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
  }

  .brand-type.internal {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.2);
  }

  .brand-type.external {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.2);
  }

  .brand-count {
    color: var(--muted);
    font-size: 0.875rem;
  }

  .brands-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
  }

  .brand-actions {
    margin-top: 1rem;
  }

  .btn-view-products {
    background: var(--accent-grad);
    color: #111216;
    border: none;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
  }

  .btn-view-products:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(240,194,75,0.3);
    text-decoration: none;
    color: #111216;
  }

  .no-brands {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--muted);
  }

  .no-brands-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
  }

  @media (max-width: 768px) {
    .brands-header {
      padding: 2rem 1rem;
    }

    .brands-title {
      font-size: 2rem;
    }

    .brands-subtitle {
      font-size: 1rem;
    }

    .brands-grid {
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 1.5rem;
    }
  }

  /* Dark mode enhancements */
  html[data-theme="dark"] .brands-header {
    background: linear-gradient(135deg, rgba(240,194,75,0.15) 0%, rgba(120,119,198,0.15) 100%);
  }

  html[data-theme="dark"] .brand-card {
    background: linear-gradient(135deg,
      rgba(240,194,75,0.15) 0%,
      rgba(120,119,198,0.15) 100%);
    box-shadow:
      0 8px 32px rgba(0,0,0,0.3),
      inset 0 1px 0 rgba(240,194,75,0.2);
  }

  html[data-theme="dark"] .brand-card:hover {
    box-shadow:
      0 20px 40px rgba(0,0,0,0.4),
      0 0 0 1px rgba(240,194,75,0.4),
      inset 0 1px 0 rgba(240,194,75,0.3);
  }

  html[data-theme="dark"] .brand-logo {
    background: rgba(26,26,26,0.8);
    border-bottom-color: rgba(255,255,255,0.1);
  }
</style>
@endpush

@section('content')

{{-- Brands Header --}}
<section class="brands-header reveal">
  <div class="container position-relative">
    <h1 class="brands-title">Our Brands</h1>
    <p class="brands-subtitle">
      Discover quality products from trusted brands. From internal collections to premium external partnerships.
    </p>
  </div>
</section>

{{-- Brands Grid --}}
<section class="py-5 reveal">
  <div class="container">
    @if($brands->count() > 0)
      <div class="row g-4">
        @foreach($brands as $brand)
          <div class="col-6 col-md-4 col-lg-3">
            <a href="{{ route('brands.show', ['slug' => $brand->slug]) }}" class="brand-card d-block">
              <div class="brand-logo">
                @if($brand->logo_url)
                  <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="w-100 h-100">
                @else
                  <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                    <span class="fs-1">{{ substr($brand->name, 0, 1) }}</span>
                  </div>
                @endif
              </div>

              <div class="brand-info">
                <h3 class="brand-name">{{ $brand->name }}</h3>

                <div class="brand-meta">
                  <div class="brand-meta-item">
                    <span>📦</span>
                    <span>{{ $brand->products_count ?? 0 }}</span>
                  </div>
                  <div class="brand-meta-item">
                    <span class="brand-type-badge {{ $brand->is_external ? 'external' : 'internal' }}">
                      {{ $brand->is_external ? 'External' : 'Internal' }}
                    </span>
                  </div>
                </div>

                <div class="brand-actions">
                  <span class="btn-view-products">View Products</span>
                </div>
              </div>
            </a>
          </div>
        @endforeach
      </div>
    @else
      <div class="no-brands">
        <div class="no-brands-icon">
          <svg class="no-brands-svg" viewBox="0 0 24 24" fill="currentColor" width="64" height="64">
            <path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/>
          </svg>
        </div>
        <h3>No brands found</h3>
        <p>We don't have any brands yet.</p>
        <a href="{{ route('products.index') }}" class="btn btn-vel-gold">
          Browse All Products
        </a>
      </div>
    @endif
  </div>
</section>

@endsection
