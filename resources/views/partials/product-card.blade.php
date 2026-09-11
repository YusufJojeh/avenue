<div class="card product-card h-100"
     data-product-id="{{ $p->id }}"
     data-product-name="{{ $p->name }}"
     data-product-price="{{ $p->price }}"
     data-product-sale-price="{{ $p->sale_price ?? '' }}"
     data-product-image="{{ $p->primary_image_url }}"
     data-product-description="{{ $p->description ?? $p->short_description ?? '' }}"
     data-product-url="{{ route('products.show', ['slug' => $p->slug]) }}"
     data-product-brand="{{ $p->brand->name ?? '' }}">

  {{-- Sale Badge --}}
  @if(!is_null($p->sale_price) && $p->sale_price > 0 && $p->sale_price < $p->price)
    <div class="sale-badge">
      {{ round((($p->price - $p->sale_price) / $p->price) * 100) }}% OFF
    </div>
  @endif

  {{-- Product Image --}}
  <div class="product-image-wrapper">
    <div class="product-image">
      <img src="{{ $p->primary_image_url }}" alt="{{ $p->name }}" width="400" height="400" loading="lazy" decoding="async" data-fallback="{{ asset('images/placeholder-product.png') }}">
      <div class="image-overlay">
        <div class="quick-actions">
          <button type="button" class="quick-action-btn view-details-btn" data-bs-toggle="modal" data-bs-target="#productDetailsModal" title="{{ __('common.messages.view_details') }}">
            <i class="fas fa-eye"></i>
          </button>
          <button type="button" class="quick-action-btn wishlist-btn" data-product-id="{{ $p->id }}" title="{{ __('common.messages.add_to_wishlist') }}">
            <i class="fas fa-heart"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Card Body --}}
  <div class="card-body d-flex flex-column">
    {{-- Brand --}}
    @if($p->brand)
      <div class="product-brand">{{ $p->brand->name }}</div>
    @endif

    {{-- Product Name --}}
    <h5 class="product-name">{{ $p->name }}</h5>

    {{-- Price Section --}}
    <div class="price-section mt-auto">
      @if(!is_null($p->sale_price) && $p->sale_price > 0 && $p->sale_price < $p->price)
        <div class="price-row">
          <span class="original-price">${{ number_format($p->price, 2) }}</span>
          <span class="sale-price">${{ number_format($p->sale_price, 2) }}</span>
        </div>
      @else
        <div class="price-row">
          <span class="current-price">${{ number_format($p->price, 2) }}</span>
        </div>
      @endif
    </div>

    {{-- Action Buttons --}}
    <div class="product-actions">
      <a href="{{ route('products.show', ['slug' => $p->slug]) }}" class="btn btn-primary btn-sm view-details-btn" title="{{ __('common.messages.view_details') }}">
        <i class="fas fa-eye"></i>
      </a>
      <button type="button" class="btn btn-outline-secondary btn-sm copy-link-btn" data-product-url="{{ route('products.show', ['slug' => $p->slug]) }}" title="{{ __('common.messages.copy_link') }}">
        <i class="fas fa-copy"></i>
      </button>
      <button type="button" class="btn btn-outline-danger btn-sm wishlist-btn" data-product-id="{{ $p->id }}" title="{{ __('common.messages.add_to_wishlist') }}">
        <i class="fas fa-heart"></i>
      </button>
    </div>
  </div>
</div>
