<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $htmlDir ?? 'ltr' }}" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @php
    $seoMeta = array_merge([
      'title' => $siteName ?? 'AVENUE',
      'description' => ($siteName ?? 'AVENUE') . ' - Premium e-commerce store. Discover curated products.',
      'canonical' => url()->current(), 'robots' => 'index, follow',
      'image' => asset('brand/avenue.svg'), 'type' => 'website',
    ], $seo ?? []);
    if (empty($seoMeta['alternates'])) $seoMeta['alternates'] = \App\Support\LocalizedUrl::alternates();
    $seoTitle = View::yieldContent('title') ?: $seoMeta['title'];
    $seoDescription = View::yieldContent('meta_description') ?: $seoMeta['description'];
    $seoCanonical = View::yieldContent('canonical') ?: $seoMeta['canonical'];
    $seoRobots = View::yieldContent('meta_robots') ?: $seoMeta['robots'];
    $seoOgTitle = View::yieldContent('og_title') ?: $seoTitle;
    $seoOgDescription = View::yieldContent('og_description') ?: $seoDescription;
    $seoImage = View::yieldContent('og_image') ?: ($seoMeta['image'] ?: asset('brand/avenue.svg'));
  @endphp
  <title>{{ $seoTitle }}</title>

  {{-- SEO Meta --}}
  <meta name="description" content="{{ $seoDescription }}">
  <meta name="robots" content="{{ $seoRobots }}">
  <link rel="canonical" href="{{ $seoCanonical }}">
  @foreach($seoMeta['alternates'] as $language => $alternateUrl)
  <link rel="alternate" hreflang="{{ $language }}" href="{{ $alternateUrl }}">
  @endforeach

  {{-- Open Graph --}}
  <meta property="og:title" content="{{ $seoOgTitle }}">
  <meta property="og:description" content="{{ $seoOgDescription }}">
  <meta property="og:image" content="{{ $seoImage }}">
  <meta property="og:url" content="{{ $seoCanonical }}">
  <meta property="og:type" content="{{ View::yieldContent('og_type') ?: $seoMeta['type'] }}">
  <meta property="og:site_name" content="{{ $siteName ?? 'AVENUE' }}">
  <meta property="og:locale" content="{{ app()->getLocale() == 'ar' ? 'ar_SA' : 'en_US' }}">

  {{-- Twitter Card --}}
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $seoOgTitle }}">
  <meta name="twitter:description" content="{{ $seoOgDescription }}">
  <meta name="twitter:image" content="{{ $seoImage }}">

  @yield('structured_data')

  {{-- Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="preconnect" href="https://cdn.jsdelivr.net">
  <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  {{-- Brand Fonts (non-blocking) --}}
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Cinzel:wght@600;700&family=Montserrat:wght@500;700&display=swap">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Cinzel:wght@600;700&family=Montserrat:wght@500;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
  <noscript><link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Cinzel:wght@600;700&family=Montserrat:wght@500;700&display=swap" rel="stylesheet"></noscript>

  {{-- Font Awesome (non-blocking, loads solid + brands subsets) --}}
  <link rel="preload" as="image" href="{{ asset('brand/avenue.svg') }}">
  <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/solid.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/fontawesome.min.css" rel="stylesheet" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/solid.min.css" rel="stylesheet" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/brands.min.css" rel="stylesheet" crossorigin="anonymous">

  {{-- No-FOUC: set theme early (light/dark) --}}
  <script>
    (function () {
      const KEY = 'vel-theme';
      const saved = localStorage.getItem(KEY);
      const theme = (saved === 'light' || saved === 'dark')
        ? saved
        : (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      document.documentElement.setAttribute('data-theme', theme);
    })();
  </script>

  {{-- Dynamic theme variables (served from DB settings). See ThemeController@css --}}
  <link rel="stylesheet" href="{{ route('theme.css') }}?v={{ $settings['theme.version'] ?? '1' }}">

  {{-- Component styles (use CSS vars from theme.css) --}}
  <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
  @if (($currentLocale ?? app()->getLocale()) === 'ar')
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
  @endif

  @php
    $siteName = $siteName ?? ($settings['site.name'] ?? 'AVENUE');
    $logoPath = $settings['site.logo_light'] ?? null;
    $favicon  = $settings['site.favicon'] ?? null;
  @endphp

  @if($favicon)
    <link rel="icon" type="image/png" href="{{ asset('storage/' . $favicon) }}">
  @endif

  @stack('styles')
</head>
<body>

{{-- Skip to content link for keyboard accessibility (WCAG 2.1 Level A) --}}
<a href="#main-content" class="skip-to-content">{{ __('common.messages.skip_to_content') }}</a>

@php
  $waNumberRaw = $settings['site.whatsapp'] ?? '15551234567';
  $waNumber = preg_replace('/[^0-9]/', '', $waNumberRaw);
  $waText   = urlencode(__('common.messages.whatsapp_default_message'));
@endphp

<script>window.SITE_WHATSAPP_NUMBER = '{{ $waNumber }}';</script>

{{-- ENHANCED NAVBAR --}}
<nav class="navbar navbar-expand-lg vel-nav sticky-top premium-nav-shell" aria-label="{{ __('common.nav.primary_navigation') }}">
  <div class="container py-2 flex-wrap">
    {{-- Logo Section --}}
    <a class="navbar-brand fw-bold d-flex align-items-center nav-order-logo" href="{{ route('home') }}">
      <span class="d-lg-none navbar-logo-theme navbar-logo-mobile" aria-hidden="true">
  <img src="{{ asset('brand/avenue-mark-light.svg') }}" alt="" class="logo-theme-light navbar-logo-img" width="34" height="34" loading="eager" fetchpriority="high">
  <img src="{{ asset('brand/avenue-mark-dark.svg') }}" alt="" class="logo-theme-dark navbar-logo-img" width="34" height="34" loading="eager" fetchpriority="high">
</span>
      <span class="visually-hidden d-lg-none">{{ $siteName }}</span>
      @if($logoPath && file_exists(storage_path('app/public/' . $logoPath)))
        <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $siteName }}" class="navbar-logo navbar-logo-desktop">
      @else
        <span class="navbar-logo-theme navbar-logo-desktop" aria-hidden="true">
  <img src="{{ asset('brand/avenue-logo-light.svg') }}" alt="" class="logo-theme-light navbar-logo-img" loading="eager" fetchpriority="high">
  <img src="{{ asset('brand/avenue-logo-dark.svg') }}" alt="" class="logo-theme-dark navbar-logo-img" loading="eager" fetchpriority="high">
</span>
      @endif
    </a>

    {{-- Quick Actions (desktop only) --}}
    <div class="nav-quick-actions nav-order-actions d-none d-lg-flex align-items-center gap-2">
      <x-language-switcher compact :alternates="$seoMeta['alternates']" />

      <div class="theme-switcher">
        <button class="btn btn-sm btn-theme-switcher" type="button" data-theme-toggle aria-label="{{ __('common.messages.toggle_theme') }}">
          <i class="fas fa-moon" data-theme-icon aria-hidden="true"></i>
        </button>
      </div>
    </div>

    {{-- Mobile Toggle --}}
    <button class="navbar-toggler nav-order-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false" aria-label="{{ __('common.messages.toggle_navigation') }}">
      <span class="navbar-toggler-icon"></span>
    </button>

    {{-- Mobile actions (next to burger, only when opened) --}}
    <div class="mobile-header-actions nav-order-mobile-actions d-lg-none align-items-center gap-2">
      <x-language-switcher compact :alternates="$seoMeta['alternates']" />
      <button class="btn btn-sm btn-theme-switcher" type="button" data-theme-toggle aria-label="{{ __('common.messages.toggle_theme') }}">
        <i class="fas fa-moon" data-theme-icon aria-hidden="true"></i>
      </button>
    </div>

    {{-- Search Bar - Always Visible (outside collapse) --}}
    <div class="navbar-search-wrapper nav-order-search flex-grow-1 mb-2 mb-lg-0 mx-lg-3">
      <div class="navbar-search-container position-relative w-100">
        <form action="{{ route('products.index') }}" method="get" class="navbar-search-form" id="navbarSearchForm" role="search">
          <div class="amazon-search-wrapper">
            <div class="search-category-dropdown-wrapper d-none d-md-flex">
              <select class="search-category-select" name="category" id="searchCategorySelect">
                <option value="">{{ __('common.nav.all_categories') }}</option>
                @foreach($navSearchCategories ?? [] as $cat)
                  <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                @endforeach
              </select>
              <i class="fas fa-chevron-down category-dropdown-icon"></i>
            </div>
            <div class="amazon-search-input-wrapper">
              <i class="fas fa-search search-icon-left"></i>
              <input type="text"
                     class="amazon-search-input"
                     name="q"
                     id="navbarSearchInput"
                     placeholder="{{ __('common.messages.search_placeholder') }}"
                     autocomplete="off"
                     aria-label="{{ __('common.actions.search') }}"
                     inputmode="search">
            </div>
            <button type="submit" class="amazon-search-submit-btn" aria-label="{{ __('common.actions.search') }}">
              <i class="fas fa-search"></i>
              <span class="visually-hidden search-btn-text">{{ __('common.actions.search') }}</span>
            </button>
          </div>
        </form>
        {{-- Mega Dropdown Search Suggestions --}}
        <div class="mega-search-dropdown" id="navbarSearchSuggestions" style="display: none;"
             data-label-products="{{ __('common.nav.products') }}"
             data-label-categories="{{ __('common.nav.categories') }}"
             data-label-brands="{{ __('common.nav.brands') }}"
             data-label-results="{{ __('common.search.results') }}"
             data-label-popular-searches="{{ __('common.search.popular_searches') }}"
             data-trending-1="{{ __('common.search.trending.featured_products') }}"
             data-trending-2="{{ __('common.search.trending.new_arrivals') }}"
             data-trending-3="{{ __('common.search.trending.on_sale') }}"
             data-trending-4="{{ __('common.search.trending.best_sellers') }}"
             data-trending-5="{{ __('common.search.trending.top_rated') }}">
          <div class="suggestions-loading" style="display: none;">
            <div class="text-center p-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('common.messages.loading') }}</span>
              </div>
            </div>
          </div>
          <div class="mega-dropdown-content" id="suggestionsResults">
            <!-- Suggestions will be inserted here -->
          </div>
           <div class="mega-dropdown-footer">
            <a href="{{ route('products.index') }}" class="view-all-results-btn" id="viewAllResultsBtn">
              {{ __('common.actions.view_all_results') }}
              <i class="fas fa-arrow-right ms-2"></i>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div id="mainNav" class="collapse navbar-collapse nav-order-collapse">
      {{-- Center Navigation Links --}}
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}" @if(request()->routeIs('products.index')) aria-current="page" @endif>
            {{ __('common.nav.products') }}
          </a>
        </li>
        <li class="nav-item premium-categories-item">
          <a class="nav-link premium-categories-trigger {{ request()->routeIs('categories.*') ? 'active' : '' }}"
             href="{{ route('categories.index') }}"
             data-mega-toggle="categories"
             aria-expanded="false"
             @if(request()->routeIs('categories.*')) aria-current="page" @endif>
            {{ __('common.nav.categories') }}
            <i class="fas fa-chevron-down small ms-1" aria-hidden="true"></i>
          </a>
          <div class="premium-mega-menu" id="categoriesMegaMenu" role="region" aria-label="{{ __('common.nav.categories') }}">
            <div class="premium-mega-header d-flex align-items-center justify-content-between mb-3">
              <div>
                <div class="premium-mega-title">{{ __('common.nav.categories') }}</div>
                <div class="premium-mega-subtitle">{{ __('common.nav.categories_megamenu_subtitle') }}</div>
              </div>
              <a href="{{ route('categories.index') }}" class="premium-mega-all-link">
                {{ __('common.actions.view_all_categories') }}
              </a>
            </div>
            <div class="premium-mega-grid">
              @foreach(($navMegaCategories ?? []) as $megaCategory)
                <div class="premium-mega-item">
                  <a href="{{ route('categories.show', ['slug' => $megaCategory->slug]) }}" class="premium-mega-link">
                    <div class="premium-mega-thumb-wrap">
                      <img
                        src="{{ $megaCategory->image_url ?? asset('images/placeholder-category.png') }}"
                        alt="{{ $megaCategory->name }}"
                        class="premium-mega-thumb"
                        loading="lazy"
                        data-fallback="{{ asset('images/placeholder-category.png') }}"
                      >
                    </div>
                    <div class="premium-mega-item-body">
                      <div class="premium-mega-item-title">{{ $megaCategory->name }}</div>
                      <div class="premium-mega-meta">
                        <span>{{ $megaCategory->products_count }} {{ trans_choice('common.units.items', $megaCategory->products_count) }}</span>
                        <span class="premium-mega-view-all">{{ __('common.actions.view_all') }}</span>
                      </div>
                      @if($megaCategory->products->isNotEmpty())
                        <ul class="premium-mega-featured">
                          @foreach($megaCategory->products as $featuredProduct)
                            <li>{{ $featuredProduct->name }}</li>
                          @endforeach
                        </ul>
                      @endif
                    </div>
                  </a>
                </div>
              @endforeach
            </div>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}" href="{{ route('brands.index') }}" @if(request()->routeIs('brands.*')) aria-current="page" @endif>
            {{ __('common.nav.brands') }}
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}" @if(request()->routeIs('about')) aria-current="page" @endif>
            {{ __('common.nav.about') }}
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}" @if(request()->routeIs('contact')) aria-current="page" @endif>
            {{ __('common.nav.contact') }}
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('wishlist.*') ? 'active' : '' }}" href="{{ route('wishlist.index') }}" @if(request()->routeIs('wishlist.*')) aria-current="page" @endif>
            {{ __('common.nav.wishlist') }}
          </a>
        </li>
      </ul>

    </div>
  </div>
</nav>

{{-- PAGE CONTENT --}}
<main id="main-content">
@yield('content')
</main>

{{-- FOOTER --}}
<footer class="vel-footer mt-5 pt-5">
  <div class="container pb-4">
    <div class="row g-4">
      <div class="col-12 col-lg-4">
        <div class="footer-head">{{ $siteName }}</div>
        <p class="footer-description mb-3">
          {{ __('common.pages.refined_storefront') }}
          {{ __('common.pages.discover_premium') }}
        </p>
        <div class="footer-social">
          <div class="social-links">
            @if(isset($settings['social_media']['facebook']) && $settings['social_media']['facebook'])
            <a href="{{ $settings['social_media']['facebook'] }}" target="_blank" class="social-link facebook-link" aria-label="{{ __('common.messages.facebook') }}">
              <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
            @endif

            @if(isset($settings['social_media']['instagram']) && $settings['social_media']['instagram'])
            <a href="{{ $settings['social_media']['instagram'] }}" target="_blank" class="social-link instagram-link" aria-label="{{ __('common.messages.instagram') }}">
              <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M16.98 0a6.9 6.9 0 0 1 5.08 1.98A6.94 6.94 0 0 1 24 7.02v9.96c0 2.08-.68 3.87-1.98 5.13A7.14 7.14 0 0 1 16.94 24H7.06a7.06 7.06 0 0 1-5.03-1.89A6.96 6.96 0 0 1 0 16.94V7.02C0 2.8 2.8 0 7.02 0h9.96zm.05 2.23H7.06c-1.45 0-2.7.43-3.53 1.25a4.82 4.82 0 0 0-1.02 1.08A4.9 4.9 0 0 0 2.1 7.02v9.92a4.9 4.9 0 0 0 1.44 3.53 4.9 4.9 0 0 0 3.53 1.44h9.88a4.9 4.9 0 0 0 3.53-1.44 4.9 4.9 0 0 0 1.44-3.53V7.02a4.9 4.9 0 0 0-1.44-3.53 4.9 4.9 0 0 0-3.53-1.44zM12 5.76c3.39 0 6.13 2.74 6.13 6.13a6.13 6.13 0 0 1-12.26 0c0-3.39 2.74-6.13 6.13-6.13zm0 2.22a3.91 3.91 0 0 0-3.9 3.9 3.91 3.91 0 0 0 3.9 3.9 3.91 3.91 0 0 0 3.9-3.9 3.91 3.91 0 0 0-3.9-3.9zm6.44-3.53a1.68 1.68 0 0 1 0 3.36 1.68 1.68 0 0 1-3.36 0 1.68 1.68 0 0 1 3.36 0z"/>
              </svg>
            </a>
            @endif
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3 col-lg-2">
        <div class="footer-head">{{ __('common.nav.products') }}</div>
        <ul class="footer-links">
          <li><a class="footer-link" href="{{ route('products.index') }}">{{ __('common.pages.all_products') }}</a></li>
          <li><a class="footer-link" href="{{ route('categories.index') }}">{{ __('common.nav.categories') }}</a></li>
          <li><a class="footer-link" href="{{ route('brands.index') }}">{{ __('common.nav.brands') }}</a></li>
          <li><a class="footer-link" href="{{ route('wishlist.index') }}">{{ __('common.nav.wishlist') }}</a></li>
        </ul>
      </div>

      <div class="col-6 col-md-3 col-lg-2">
        <div class="footer-head">{{ __('common.pages.company') }}</div>
        <ul class="footer-links">
          <li><a class="footer-link" href="{{ route('about') }}">{{ __('common.nav.about') }}</a></li>
          <li><a class="footer-link" href="{{ route('contact') }}">{{ __('common.nav.contact') }}</a></li>
          <li><a class="footer-link" href="{{ route('faq') }}">{{ __('common.pages.faq') }}</a></li>
        </ul>
      </div>

      <div class="col-6 col-md-3 col-lg-2">
        <div class="footer-head">{{ __('common.pages.legal') }}</div>
        <ul class="footer-links">
          <li><a class="footer-link" href="{{ route('privacy-policy') }}">{{ __('common.pages.privacy_policy') }}</a></li>
          <li><a class="footer-link" href="{{ route('terms-of-service') }}">{{ __('common.pages.terms_of_service') }}</a></li>
        </ul>
      </div>

      <div class="col-6 col-md-3 col-lg-2">
        <div class="footer-head">{{ __('common.pages.sitemap') }}</div>
        <ul class="footer-links">
          <li><a class="footer-link" href="/">{{ __('common.nav.home') }}</a></li>
          <li><a class="footer-link" href="{{ route('products.index') }}">{{ __('common.nav.products') }}</a></li>
          <li><a class="footer-link" href="{{ route('categories.index') }}">{{ __('common.nav.categories') }}</a></li>
          <li><a class="footer-link" href="{{ route('brands.index') }}">{{ __('common.nav.brands') }}</a></li>
          <li><a class="footer-link" href="{{ route('about') }}">{{ __('common.nav.about') }}</a></li>
          <li><a class="footer-link" href="{{ route('contact') }}">{{ __('common.nav.contact') }}</a></li>
        </ul>
      </div>

    </div>

    <hr class="footer-divider my-4">

    <div class="footer-bottom">
      <div class="row align-items-center">
        <div class="col-12 text-center">
          <div class="copyright-text">
            {{ __('common.footer.copyright', ['year' => date('Y'), 'site' => $siteName]) }}

          </div>
        </div>
      </div>
    </div>
  </div>
</footer>

{{-- Product Details Modal --}}
<div class="modal fade" id="productDetailsModal" tabindex="-1" aria-labelledby="productDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="productDetailsModalLabel">{{ __('common.fields.product_details') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('common.actions.close') }}"></button>
      </div>
      <div class="modal-body">
        <div class="row g-4">
          <!-- Product Image -->
          <div class="col-md-6">
            <div class="product-modal-image">
              <img id="modalProductImage" src="" alt="" class="img-fluid rounded">
            </div>
          </div>

          <!-- Product Details -->
          <div class="col-md-6">
            <div class="product-modal-details">
              <h4 id="modalProductName" class="product-modal-name"></h4>
              <p id="modalProductBrand" class="product-modal-brand"></p>

              <div class="product-modal-price-section">
                <div id="modalProductPrice" class="product-modal-price"></div>
              </div>

              <div id="modalProductDescription" class="product-modal-description"></div>

              <div class="product-modal-actions mt-4">
                <a id="modalProductLink" href="{{ route('products.index') }}" class="btn btn-primary btn-lg w-100 mb-3" target="_blank">
                  <i class="fas fa-external-link-alt me-2"></i>
                  {{ __('common.actions.view') }} {{ __('common.nav.products') }}
                </a>

                <div class="row g-2">
                  <div class="col-6">
                    <button type="button" class="btn btn-outline-secondary w-100" id="modalCopyLinkBtn">
                      <i class="fas fa-copy me-2"></i>
                      {{ __('common.actions.copy') }}
                    </button>
                  </div>
                  <div class="col-6">
                    <button type="button" class="btn btn-outline-danger w-100" id="modalWishlistBtn">
                      <i class="fas fa-heart me-2"></i>
                      {{ __('common.nav.wishlist') }}
                    </button>
                  </div>
                </div>

                <div class="row g-2 mt-2">
                  <div class="col-12">
                    <a id="modalWhatsAppBtn" href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" class="btn btn-success w-100" target="_blank" rel="noopener">
                      <i class="fab fa-whatsapp me-2"></i>
                      {{ __('common.messages.whatsapp') }}
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Toast Container for Notifications --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer">
  <!-- Toast notifications will be dynamically inserted here -->
</div>

{{-- Floating WhatsApp FAB --}}
<a class="wa-fab" href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" rel="noopener" aria-label="{{ __('common.messages.chat_on_whatsapp') }}">
  <span class="wa-icon" aria-hidden="true"></span>
  {{ __('common.messages.whatsapp') }}
</a>

{{-- Bootstrap JS with CDN Fallback --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
  // CDN Fallback for Bootstrap
  if (typeof bootstrap === 'undefined') {
    document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"><\/script>');
  }
</script>

{{-- Theme Manager + FX --}}
<script>
(function () {
  const KEY  = 'vel-theme';
  const root = document.documentElement;

  function current() {
    const saved = localStorage.getItem(KEY);
    if (saved === 'light' || saved === 'dark') return saved;
    return matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function syncIcons(theme) {
    const isDark = theme === 'dark';
    const navIcon = document.getElementById('velIconNav');
    if (navIcon) navIcon.textContent = isDark ? '🌞' : '🌙';
  }

  function apply(theme) {
    root.setAttribute('data-theme', theme);
    try { localStorage.setItem(KEY, theme); } catch (_) {}
    syncIcons(theme);
    document.dispatchEvent(new CustomEvent('vel:theme', { detail: { theme } }));
  }

  // init
  apply(current());

  // follow OS if no explicit choice
  try {
    matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
      const saved = localStorage.getItem(KEY);
      if (!saved) apply(e.matches ? 'dark' : 'light');
    });
  } catch (_) {}

  // theme toggles
  const toggles = [
    ...document.querySelectorAll('[data-theme-toggle]')
  ].filter(Boolean);

  toggles.forEach(btn => btn.addEventListener('click', () => {
    const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    apply(next);
  }));

  // pressed effect for gold buttons
  document.addEventListener('mousedown', e => {
    const btn = e.target.closest('.btn-vel-gold');
    if (!btn) return;
    btn.classList.add('pressed');
  });

  document.addEventListener('mouseup', () => {
    document.querySelectorAll('.btn-vel-gold.pressed').forEach(b => b.classList.remove('pressed'));
  });

  // subtle tilt for luxe-card
  document.addEventListener('mousemove', e => {
    document.querySelectorAll('.luxe-card').forEach(card => {
      const r = card.getBoundingClientRect();
      if (e.clientX < r.left - 20 || e.clientX > r.right + 20 || e.clientY < r.top - 20 || e.clientY > r.bottom + 20) {
        card.style.transform = ''; // reset (keep hover translate via :hover)
        return;
      }
      const rx = ((e.clientY - r.top) / r.height - 0.5) * 2; // -1..1
      const ry = ((e.clientX - r.left) / r.width - 0.5) * 2;
      card.style.transform = `rotateX(${(-rx * 2)}deg) rotateY(${(ry * 2)}deg) translateY(-6px)`;
    });
  });

  // reveal on scroll
  const io = new IntersectionObserver(entries => {
    entries.forEach(x => {
      if (x.isIntersecting) {
        x.target.classList.add('visible');
        io.unobserve(x.target);
      }
    });
  }, { threshold: 0.12 });

  document.querySelectorAll('.reveal').forEach(el => io.observe(el));
})();

// Enhanced Navigation Features
document.addEventListener('DOMContentLoaded', function() {
  const root = document.documentElement;

  // Update theme icons based on current theme
  function updateThemeIcon() {
    const currentTheme = root.getAttribute('data-theme');
    const isDark = currentTheme === 'dark';

    document.querySelectorAll('[data-theme-icon]').forEach((icon) => {
      icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
    });

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
      btn.title = isDark
        ? '{{ __("common.messages.switch_to_light_mode") }}'
        : '{{ __("common.messages.switch_to_dark_mode") }}';
    });
  }

  // Initialize theme icon
  updateThemeIcon();

  // Update icon when theme changes
  const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
        updateThemeIcon();
      }
    });
  });
  observer.observe(root, { attributes: true });

  // Language switcher enhancement
  document.querySelectorAll('.language-switcher').forEach(function(languageDropdown) {
    languageDropdown.addEventListener('click', function() {
      this.style.transform = 'scale(0.98)';
      setTimeout(() => {
        this.style.transform = '';
      }, 120);
    });
  });

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

  // Navbar scroll effect with dark mode support and rAF throttle
  const navbar = document.querySelector('.vel-nav');
  if (navbar) {
    let ticking = false;
    window.addEventListener('scroll', function() {
      if (!ticking) {
        requestAnimationFrame(function() {
          const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
          const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
          if (scrollTop > 100) {
            navbar.style.background = isDark
              ? 'rgba(17, 18, 22, 0.98)'
              : 'rgba(255, 255, 255, 0.95)';
            navbar.style.backdropFilter = 'blur(10px)';
            navbar.style.boxShadow = isDark
              ? '0 4px 20px rgba(0,0,0,0.3)'
              : '0 4px 20px rgba(0,0,0,0.1)';
          } else {
            navbar.style.background = '';
            navbar.style.backdropFilter = '';
            navbar.style.boxShadow = '';
          }
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }
});
</script>

{{-- Image Fallback Handler (replaces inline onerror for CSP compliance) --}}
<script>
document.addEventListener('error', function(e) {
  var img = e.target;
  if (img.tagName !== 'IMG') return;
  if (img.dataset.fallback && !img.dataset.fallen) {
    img.dataset.fallen = '1';
    img.src = img.dataset.fallback;
  } else if (img.dataset.fallbackHide) {
    img.style.display = 'none';
    var next = img.nextElementSibling;
    if (next) next.style.display = 'flex';
  }
}, true);
</script>

{{-- Enhanced Product Cards JavaScript --}}
<script src="{{ asset('js/enhanced-product-cards.js') }}" defer></script>

{{-- Navbar Search JavaScript --}}
<script src="{{ asset('js/navbar-search.js') }}" defer></script>

@stack('scripts')
</body>
</html>
