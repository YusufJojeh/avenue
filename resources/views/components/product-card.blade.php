{{-- Enhanced Product Card Component --}}
@props(['product', 'showActions' => true, 'showRating' => true, 'showBrand' => true])

<div class="product-card h-100"
     data-product-id="{{ $product->id }}"
     data-product-name="{{ $product->name }}"
     data-product-price="{{ $product->price }}"
     data-product-sale-price="{{ $product->sale_price ?? '' }}"
     data-product-image="{{ $product->primary_image_url ?? asset('images/placeholder-product.png') }}"
     data-product-description="{{ $product->description ?? $product->short_description ?? '' }}"
     data-product-url="{{ route('products.show', ['slug' => $product->slug]) }}"
     data-product-brand="{{ $product->brand?->name ?? '' }}">

  {{-- Creative Sale Ribbon --}}
  @if($product->sale_price && $product->sale_price > 0 && $product->sale_price < $product->price)
    @php
      $discountPercent = round((($product->price - $product->sale_price) / $product->price) * 100);
    @endphp
    <div class="sale-ribbon {{ $discountPercent >= 50 ? 'style-2' : ($discountPercent >= 30 ? 'style-3' : '') }}">
      <div class="ribbon-content">
        <span class="ribbon-text">{{ $discountPercent }}%</span>
        <span class="ribbon-off">OFF</span>
      </div>
      <div class="ribbon-tail"></div>
      <div class="ribbon-sparkle"></div>
    </div>
  @endif

  {{-- Product Image --}}
  <div class="product-image-wrapper">
    <div class="product-image">
      <img src="{{ $product->primary_image_url }}"
           alt="{{ $product->name }}"
           width="400"
           height="400"
           loading="lazy"
           decoding="async"
           data-fallback="{{ asset('images/placeholder-product.png') }}">

      @if($showActions)
        <div class="image-overlay">
          <div class="quick-actions">
            <a href="{{ route('products.show', ['slug' => $product->slug]) }}"
               class="quick-action-btn view-details-btn"
               title="View Details">
              <i class="fas fa-eye"></i>
            </a>
            <x-wishlist-button :product="$product" size="small" position="inline" :showText="false" />
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- Card Body --}}
  <div class="card-body d-flex flex-column">
    {{-- Brand --}}
    @if($showBrand && $product->brand)
      <div class="product-brand">{{ $product->brand->name }}</div>
    @endif

    {{-- Product Name --}}
    <h5 class="product-name">
      <a href="{{ route('products.show', ['slug' => $product->slug]) }}"
         class="text-decoration-none text-reset">
        {{ $product->name }}
      </a>
    </h5>

    {{-- Rating --}}
    @if($showRating)
      <div class="product-rating">
        <div class="rating-stars">
          @for($i = 1; $i <= 5; $i++)
            <span class="star {{ $i <= 4 ? 'filled' : 'empty' }}">★</span>
          @endfor
        </div>
        <span class="rating-text">(4.3)</span>
      </div>
    @endif

    {{-- Enhanced Price Section --}}
    <div class="price-section mt-auto">
      @if($product->sale_price && $product->sale_price > 0 && $product->sale_price < $product->price)
        <div class="price-row">
          <span class="original-price">${{ number_format($product->price, 2) }}</span>
          <span class="sale-price premium-price">${{ number_format($product->sale_price, 2) }}</span>
        </div>
      @else
        <div class="price-row">
          <span class="current-price premium-price">${{ number_format($product->price, 2) }}</span>
        </div>
      @endif
    </div>

    {{-- Action Buttons --}}
    @if($showActions)
      <div class="product-actions">
        <a href="{{ route('products.show', ['slug' => $product->slug]) }}"
           class="btn btn-primary btn-sm view-details-btn"
           title="View Details">
          <i class="fas fa-eye"></i>
        </a>
        <button type="button"
                class="btn btn-outline-secondary btn-sm copy-link-btn"
                data-product-url="{{ route('products.show', ['slug' => $product->slug]) }}"
                title="Copy Link">
          <i class="fas fa-copy"></i>
        </button>
        <x-wishlist-button :product="$product" size="small" position="inline" :showText="false" />
      </div>
    @endif
  </div>
</div>
