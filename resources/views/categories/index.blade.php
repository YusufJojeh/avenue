@extends('layouts.app')

@section('title', 'Categories - ' . ($siteName ?? 'AVENUE'))

@section('meta_description', 'Browse all product categories at ' . ($siteName ?? 'AVENUE') . '. Discover curated collections and find exactly what you need.')
@section('canonical', route('categories.index'))
@section('og_title', 'Categories - ' . ($siteName ?? 'AVENUE'))
@section('og_description', 'Browse all product categories at ' . ($siteName ?? 'AVENUE') . '.')

@push('styles')
@include('partials.unified-styles')
<style>
  /* Categories Page Specific Styles */

  .category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
  }

  @media (max-width: 576px) {
    .category-grid {
      grid-template-columns: 1fr;
      gap: 1rem;
    }
  }

  @media (min-width: 577px) and (max-width: 768px) {
    .category-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 1.25rem;
    }
  }

  .category-card {
    background: var(--glass);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 2rem;
    text-decoration: none;
    color: var(--text);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    text-align: center;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
  }

  .category-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg,
      transparent,
      rgba(240,194,75,0.1),
      transparent);
    transition: left 0.6s ease;
  }

  .category-card:hover::before {
    left: 100%;
  }

  .category-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    color: var(--text);
    text-decoration: none;
  }

  .category-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 1.5rem;
    border: 3px solid var(--gold);
    box-shadow: 0 8px 24px rgba(240,194,75,0.3);
    transition: all 0.3s ease;
  }

  .category-card:hover .category-image {
    transform: scale(1.1);
    box-shadow: 0 12px 32px rgba(240,194,75,0.4);
  }

  .category-name {
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
    color: var(--text);
  }

  .category-description {
    color: var(--muted);
    font-size: 0.95rem;
    margin-bottom: 1.5rem;
    line-height: 1.6;
  }

  .category-stats {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    font-size: 0.9rem;
    color: var(--muted);
  }

  .category-stats span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--surface);
    border-radius: 20px;
    border: 1px solid var(--border);
  }
</style>
@endpush

@section('content')

<div class="container py-4">
  <x-enhanced-breadcrumb :items="[
    ['url' => url('/'), 'label' => __('common.nav.home')],
    ['label' => __('common.nav.categories')],
  ]" />

  <div class="page-header text-center mb-5">
    <h1 class="page-title">{{ __('common.pages.browse_categories') }}</h1>
    <p class="page-subtitle text-muted">
      {{ __('common.messages.explore_categories') }}
      {{ __('common.messages.categories_curated') }}
    </p>
  </div>

  {{-- Categories Grid --}}
  <div class="category-grid">
    @forelse($categories as $category)
      <a href="{{ route('categories.show', ['slug' => $category->slug]) }}" class="category-card">
        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="category-image" width="100" height="100" loading="lazy" decoding="async" data-fallback-hide="true">
        <div class="category-image" style="background: var(--surface); display: none; align-items: center; justify-content: center; color: var(--muted);">
          📁
        </div>

        <div class="category-name">{{ $category->name }}</div>

        @if($category->description)
          <div class="category-description">{{ Str::limit($category->description, 80) }}</div>
        @endif

        <div class="category-stats">
          <span>
            <span aria-hidden="true">📦</span>
            {{ $category->products_count ?? 0 }} products
          </span>
          @if($category->children_count > 0)
            <span>
              <span aria-hidden="true">📂</span>
              {{ $category->children_count }} subcategories
            </span>
          @endif
        </div>
      </a>
    @empty
      <div class="col-12">
        <div class="alert alert-info text-center">
          <h5>No categories found</h5>
          <p class="mb-0">Categories will appear here once they are added to the system.</p>
        </div>
      </div>
    @endforelse
  </div>

  {{-- Browse All Products Link --}}
  <div class="text-center mt-5">
    <a href="{{ route('products.index') }}" class="btn btn-enhanced">
      {{ __('common.actions.browse_all_products') }}
    </a>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Enhanced reveal animations
  (function(){
    if (window.__velRevealBound) return;
    window.__velRevealBound = true;

    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    // Observe all reveal elements
    document.querySelectorAll('.reveal').forEach(el => {
      observer.observe(el);
    });
  })();
</script>
@endpush
