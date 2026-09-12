@extends('layouts.app')

@section('title', ($siteName ?? 'AVENUE') . ' - Premium E-Commerce Store')

@section('meta_description', ($siteName ?? 'AVENUE') . ' - Discover premium products curated for you. Shop the latest collections, exclusive deals, and trending items at the best prices.')
@section('canonical', route('home'))
@section('og_title', ($siteName ?? 'AVENUE') . ' - Premium E-Commerce Store')
@section('og_description', 'Discover premium products curated for you. Shop the latest collections, exclusive deals, and trending items.')

@section('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "WebSite",
  "name": @json($siteName ?? 'AVENUE'),
  "url": @json(route('home')),
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": @json(route('products.index') . '?q={search_term_string}')
    },
    "query-input": "required name=search_term_string"
  }
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "Organization",
  "name": @json($siteName ?? 'AVENUE'),
  "url": @json(route('home')),
  "logo": @json(asset('brand/avenue.svg'))
}
</script>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')

@unless(isset($visibility['hero']) && $visibility['hero'] && isset($mainSlide) && !empty($mainSlide->title))
  <h1 class="visually-hidden">{{ $siteName ?? 'AVENUE' }} - Premium E-Commerce Store</h1>
@endunless

{{-- ====================== HERO SECTION ====================== --}}
@if(isset($visibility['hero']) && $visibility['hero'] && isset($mainSlide))
  <section class="hero-section reveal">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <div class="hero-copy p-4 p-md-5">
            @if(!empty($mainSlide->title))
              <h1 class="display-4 fw-bold mb-3 hero-title">
                {{ $mainSlide->title }}
              </h1>
            @endif
            @if(!empty($mainSlide->subtitle))
              <p class="lead mb-4 hero-subtitle">
                {{ $mainSlide->subtitle }}
              </p>
            @endif
            @if(!empty($mainSlide->cta_url))
              <a href="{{ $mainSlide->cta_url }}" class="btn btn-vel-gold btn-lg px-4 py-3">
                {{ $mainSlide->cta_label ?? __('common.actions.shop_now') }}
                <i class="ms-2">→</i>
              </a>
            @endif
          </div>
        </div>
        <div class="col-lg-6">
          @if($mainSlide->image_url)
            <img
              src="{{ $mainSlide->image_url }}"
              class="w-100 rounded-4 hero-card-img"
              alt="{{ $mainSlide->title ?? 'Hero' }}"
              loading="eager"
              fetchpriority="high"
              decoding="async"
            >
          @endif
        </div>
      </div>
    </div>
  </section>
          @endif

{{-- ====================== SLIDER SECTION ======================
     V2: the campaign hero and this carousel must never both lead —
     only show the carousel when there is no hero to compete with. --}}
@if(isset($visibility['slider']) && $visibility['slider'] && isset($sliderSlides) && $sliderSlides->count() && !(isset($visibility['hero']) && $visibility['hero'] && isset($mainSlide)))
  <section class="py-5 reveal">
    <div class="container">
      <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
          @foreach($sliderSlides as $index => $slide)
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                    class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                    aria-label="Slide {{ $index + 1 }}"></button>
          @endforeach
        </div>
          <div class="carousel-inner">
          @foreach($sliderSlides as $index => $slide)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
              <img src="{{ $slide->image_url }}" class="carousel-img" alt="{{ $slide->title ?? 'Slide' }}" loading="lazy" decoding="async">
              <div class="carousel-overlay"></div>
              @if(!empty($slide->title) || !empty($slide->subtitle))
                <div class="carousel-caption">
                  @if(!empty($slide->title))
                    <h3 class="fw-bold">{{ $slide->title }}</h3>
                  @endif
                  @if(!empty($slide->subtitle))
                    <p class="mb-3">{{ $slide->subtitle }}</p>
                @endif
                  @if(!empty($slide->cta_url))
                    <a href="{{ $slide->cta_url }}" class="btn btn-vel-gold">
                      {{ $slide->cta_label ?? __('common.actions.learn_more') }}
                      </a>
                    @endif
                </div>
              @endif
              </div>
            @endforeach
          </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">{{ __('common.actions.previous') }}</span>
            </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">{{ __('common.actions.next') }}</span>
            </button>
      </div>
        </div>
      </section>
    @endif

{{-- ====================== STATS SECTION ====================== --}}
{{-- stats-section removed --}}

{{-- ====================== FEATURED PRODUCTS ====================== --}}
@if(isset($visibility['special']) && $visibility['special'] && isset($specialProducts) && $specialProducts->count())
  <section class="py-5 reveal">
      <div class="container">
        <h2 class="section-title">{{ __('common.nav.featured_products') }}</h2>
        <div class="row g-4">
          @foreach($specialProducts as $p)
            <div class="col-6 col-md-3">
              <x-product-card :product="$p" />
            </div>
          @endforeach
        </div>
        </div>
      </section>
    @endif

  {{-- ====================== CATEGORIES ====================== --}}
@if(isset($visibility['categories']) && $visibility['categories'] && isset($categories) && $categories->count())
  <section class="py-5 reveal">
      <div class="container">
        <h2 class="section-title">{{ __('common.pages.shop_by_category') }}</h2>
        <div class="row g-4">
          @foreach($categories as $cat)
            <div class="col-6 col-md-3">
              <a href="{{ route('categories.show', ['slug' => $cat->slug]) }}" class="category-card d-block text-decoration-none h-100">
                <img src="{{ $cat->image_url }}" class="w-100 category-card-img" alt="{{ $cat->name }}" loading="lazy" data-fallback-hide="1">
                <div class="w-100 d-flex align-items-center justify-content-center category-card-placeholder">
                  <span class="text-muted">{{ $cat->name }}</span>
                </div>
                <div class="card-body text-center">
                  <h5 class="fw-semibold mb-0">{{ $cat->name }}</h5>
                </div>
              </a>
            </div>
          @endforeach
        </div>
        </div>
      </section>
    @endif

{{-- ====================== OFFERS ====================== --}}
@if(isset($visibility['offers']) && $visibility['offers'] && isset($offers) && $offers->count())
  <section class="py-5 reveal">
      <div class="container">
        <h2 class="section-title">{{ __('common.pages.special_offers') }}</h2>
        <div class="row g-4">
          @foreach($offers as $offer)
            <div class="col-12 col-md-4">
              <div class="offer-card h-100 p-4">
                @if($offer->banner_url)
                  <img src="{{ $offer->banner_url }}" class="w-100 rounded-3 mb-3 offer-card-img" alt="{{ $offer->title ?? __('common.pages.special_offers') }}" loading="lazy">
                @endif
                <div class="text-center">
                  @if(!empty($offer->title))
                    <h4 class="fw-semibold mb-2">{{ $offer->title }}</h4>
                  @endif
                  @if(!empty($offer->description))
                    <p class="text-muted mb-3">{{ $offer->description }}</p>
                  @endif
                  @if(!empty($offer->cta_url))
                    <a href="{{ $offer->cta_url }}" class="btn btn-vel-gold">
                      {{ __('common.actions.shop_now') }}
                    </a>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
        </div>
      </section>
    @endif

{{-- ====================== NEW ARRIVALS ====================== --}}
@if(isset($visibility['latest']) && $visibility['latest'] && isset($latestProducts) && $latestProducts->count())
  <section class="py-5 reveal">
      <div class="container">
        <h2 class="section-title">{{ __('common.nav.new_arrivals') }}</h2>
        <div class="row g-4">
          @foreach($latestProducts as $p)
            <div class="col-6 col-md-3">
              <x-product-card :product="$p" />
            </div>
          @endforeach
        </div>
        </div>
      </section>
    @endif

  {{-- ====================== EXTERNAL BRANDS ====================== --}}
@if(isset($visibility['external']) && $visibility['external'] && isset($externalBrandProducts) && $externalBrandProducts->count())
  <section class="py-5 reveal">
      <div class="container">
        <h2 class="section-title">{{ __('common.nav.premium_brands') }}</h2>
        <div class="row g-4">
          @foreach($externalBrandProducts as $p)
            <div class="col-6 col-md-3">
              <x-product-card :product="$p" />
            </div>
          @endforeach
        </div>
        </div>
      </section>
    @endif

{{-- ====================== CTA SECTION ====================== --}}
<section class="cta-section reveal">
  <div class="container position-relative">
    <h2 class="section-title text-white">{{ __('common.pages.ready_to_shop') }}</h2>
    <p class="lead text-white mb-4">{{ __('common.pages.discover_amazing_products') }}</p>
    <a href="{{ route('products.index') }}" class="btn btn-vel-gold btn-lg px-5 py-3">
      {{ __('common.actions.browse_all_products') }}
      <i class="ms-2">→</i>
    </a>
</div>
</section>

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

    // Add staggered animation to product cards
    document.querySelectorAll('.product-card').forEach((card, index) => {
      card.style.animationDelay = `${index * 0.1}s`;
    });
  })();

  // Parallax effect for hero section (throttled with rAF)
  (function(){
    const hero = document.querySelector('.hero-section');
    if (!hero) return;

    let ticking = false;
    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          const scrolled = window.pageYOffset;
          if (scrolled < window.innerHeight) {
            hero.style.transform = `translateY(${scrolled * -0.3}px)`;
          }
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  })();
</script>
@endpush
