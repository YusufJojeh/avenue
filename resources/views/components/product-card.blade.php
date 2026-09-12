{{-- Avenue V2 ProductCard — one shared card for all discovery surfaces (Phase B). --}}
@props(['product'])

@php
  $productUrl = route('products.show', ['slug' => $product->slug]);
  $image = $product->primary_image_url ?? asset('images/placeholder-product.png');
  $hasSale = !is_null($product->sale_price) && $product->sale_price > 0 && $product->sale_price < $product->price;
  $discountPercent = $hasSale ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0;
@endphp

<div class="av-product-card"
     data-product-id="{{ $product->id }}"
     data-product-name="{{ $product->name }}"
     data-product-price="{{ $product->price }}"
     data-product-sale-price="{{ $product->sale_price ?? '' }}"
     data-product-image="{{ $image }}"
     data-product-url="{{ $productUrl }}"
     data-product-brand="{{ $product->brand->name ?? '' }}">

  <a href="{{ $productUrl }}" class="av-product-card__media">
    <img src="{{ $image }}"
         alt="{{ $product->name }}"
         width="400"
         height="400"
         loading="lazy"
         decoding="async"
         data-fallback="{{ asset('images/placeholder-product.png') }}"
         class="av-product-card__image">
    @if($hasSale)
      <span class="av-product-card__badge">-{{ $discountPercent }}%</span>
    @endif
  </a>

  <button type="button"
          class="av-product-card__wishlist wishlist-btn"
          data-product-id="{{ $product->id }}"
          aria-label="{{ __('common.messages.add_to_wishlist') }}"
          aria-pressed="false"
          title="{{ __('common.messages.add_to_wishlist') }}">
    <i class="fas fa-heart" aria-hidden="true"></i>
  </button>

  <div class="av-product-card__body">
    @if($product->brand)
      <p class="av-product-card__eyebrow">{{ $product->brand->name }}</p>
    @endif

    <a href="{{ $productUrl }}" class="av-product-card__name">{{ $product->name }}</a>

    <p class="av-product-card__price">
      @if($hasSale)
        <span class="av-product-card__price--sale">${{ number_format($product->sale_price, 2) }}</span>
        <span class="av-product-card__price--original">${{ number_format($product->price, 2) }}</span>
      @else
        <span class="av-product-card__price--current">${{ number_format($product->price, 2) }}</span>
      @endif
    </p>
  </div>
</div>
