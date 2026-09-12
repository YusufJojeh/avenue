@extends('layouts.app')

@section('structured_data')
@foreach($structuredData as $schema)
<script type="application/ld+json">@json($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
@endforeach
@endsection

@push('styles')
<style>
  /* Modern Product Page Design - Global Standards */

  .product-page {
    padding: 2rem 0;
  }

  /* Breadcrumb */
  .breadcrumb-nav {
    margin-bottom: 2rem;
    font-size: 0.875rem;
  }

  .breadcrumb-nav a {
    color: #6c757d;
    text-decoration: none;
    transition: color 0.2s;
  }

  .breadcrumb-nav a:hover {
    color: #495057;
  }

  /* Product Gallery */
  .product-gallery {
    position: sticky;
    top: 2rem;
  }

  .main-image-wrapper {
    position: relative;
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 1rem;
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .main-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    cursor: zoom-in;
    transition: transform 0.3s, opacity 0.3s;
  }

  .main-image:hover {
    transform: scale(1.05);
  }

  .thumbnail-list {
    display: flex;
    gap: 0.75rem;
    overflow-x: auto;
    padding: 0.5rem 0;
  }

  .thumbnail-item {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s;
    background: #fff;
  }

  .thumbnail-item:hover,
  .thumbnail-item.active {
    border-color: #007bff;
  }

  .thumbnail-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* Product Info */
  .product-info {
    padding-left: 2rem;
  }

  .product-title {
    font-size: 2rem;
    font-weight: 600;
    line-height: 1.3;
    margin-bottom: 1rem;
    color: #212529;
  }

  .product-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: #6c757d;
  }

  .product-price-section {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e9ecef;
  }

  .price-wrapper {
    display: flex;
    align-items: baseline;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .current-price {
    font-size: 2rem;
    font-weight: 700;
    color: #212529;
  }

  .original-price {
    font-size: 1.25rem;
    color: #6c757d;
    text-decoration: line-through;
  }

  .discount-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: #dc3545;
    color: #fff;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 600;
  }

  .product-meta {
    margin-bottom: 1.5rem;
  }

  .meta-item {
    display: flex;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f8f9fa;
  }

  .meta-label {
    font-weight: 600;
    color: #495057;
    min-width: 120px;
  }

  .meta-value {
    color: #6c757d;
  }

  .meta-value a {
    color: #007bff;
    text-decoration: none;
  }

  .meta-value a:hover {
    text-decoration: underline;
  }

  /* Size Selection */
  .size-selection {
    margin-bottom: 1.5rem;
  }

  .size-label {
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: #212529;
  }

  .size-options {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .size-option {
    min-width: 60px;
    padding: 0.75rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    background: #fff;
    cursor: pointer;
    text-align: center;
    font-weight: 500;
    transition: all 0.2s;
  }

  .size-option:hover {
    border-color: #007bff;
  }

  .size-option.selected {
    border-color: #007bff;
    background: #e7f3ff;
    color: #007bff;
  }

  .size-option.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    text-decoration: line-through;
  }

  /* Stock Status */
  .stock-status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    margin-bottom: 1.5rem;
  }

  .stock-status.in-stock {
    background: #d4edda;
    color: #155724;
  }

  .stock-status.out-of-stock {
    background: #f8d7da;
    color: #721c24;
  }

  .stock-status::before {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
  }

  /* Action Buttons */
  .action-buttons {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
  }

  .btn-primary-action {
    flex: 1;
    min-width: 200px;
    padding: 0.875rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
  }

  .btn-copy-link {
    background: #6c757d;
    color: #fff;
  }

  .btn-copy-link:hover {
    background: #5a6268;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(108,117,125,0.3);
  }

  .btn-copy-link.copied {
    background: #28a745;
  }

  .btn-copy-link.copied:hover {
    background: #218838;
  }

  .btn-whatsapp-order {
    background: #25d366;
    color: #fff;
  }

  .btn-whatsapp-order:hover {
    background: #1da851;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(37,211,102,0.4);
  }

  .btn-wishlist {
    background: #fff;
    color: #dc3545;
    border: 2px solid #dc3545;
  }

  .btn-wishlist:hover {
    background: #dc3545;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(220,53,69,0.3);
  }

  .btn-wishlist.in-wishlist {
    background: #dc3545;
    color: #fff;
    border-color: #dc3545;
  }

  .btn-wishlist.in-wishlist:hover {
    background: #c82333;
    border-color: #c82333;
  }

  .btn-secondary-action {
    padding: 0.875rem 1.5rem;
    background: #fff;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  .btn-secondary-action:hover {
    border-color: #007bff;
    color: #007bff;
  }

  /* Description Section */
  .product-description-section {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #e9ecef;
  }

  .section-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #212529;
  }

  .description-content {
    line-height: 1.8;
    color: #495057;
  }

  /* Related Products */
  .related-products-section {
    margin-top: 4rem;
    padding-top: 2rem;
    border-top: 1px solid #e9ecef;
  }

  /* Image Modal */
  .image-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    align-items: center;
    justify-content: center;
  }

  .image-modal.active {
    display: flex;
  }

  .modal-image {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
  }

  .modal-close {
    position: absolute;
    top: 2rem;
    right: 2rem;
    color: #fff;
    font-size: 2rem;
    cursor: pointer;
    background: none;
    border: none;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.2s;
  }

  .modal-close:hover {
    background: rgba(255,255,255,0.1);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .product-page {
      padding: 1rem 0;
    }

    .product-info {
      padding-left: 0;
      margin-top: 1.5rem;
    }

    .product-gallery {
      position: static;
    }

    .product-title {
      font-size: 1.5rem;
    }

    .current-price {
      font-size: 1.5rem;
    }

    .action-buttons {
      flex-direction: column;
    }

    .btn-primary-action {
      width: 100%;
      min-height: 48px;
    }

    .thumbnail-item {
      width: 64px;
      height: 64px;
    }

    .meta-label {
      min-width: 90px;
    }

    .size-option {
      min-width: 50px;
      padding: 0.6rem 0.75rem;
      min-height: 44px;
    }

    .section-title {
      font-size: 1.25rem;
    }
  }

  @media (max-width: 576px) {
    .product-title {
      font-size: 1.25rem;
    }

    .current-price {
      font-size: 1.35rem;
    }

    .original-price {
      font-size: 1rem;
    }

    .breadcrumb-nav {
      font-size: 0.8rem;
      margin-bottom: 1rem;
    }

    .product-description-section {
      margin-top: 2rem;
      padding-top: 1.5rem;
    }

    .related-products-section {
      margin-top: 2.5rem;
      padding-top: 1.5rem;
    }
  }
</style>
@endpush

@section('content')

<div class="product-page">
  <div class="container">
    {{-- Breadcrumb --}}
    <nav class="breadcrumb-nav" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('common.nav.home') }}</a></li>
      <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('common.nav.products') }}</a></li>
      @if($product->category)
        <li class="breadcrumb-item">
          <a href="{{ route('categories.show', ['slug' => $product->category->slug]) }}">{{ $product->category->name }}</a>
        </li>
      @endif
      <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
    </ol>
  </nav>

    <div class="row">
      {{-- Product Gallery --}}
      <div class="col-12 col-lg-6">
        <div class="product-gallery">
          {{-- Main Image --}}
          <div class="main-image-wrapper">
            <img
              id="mainProductImage"
              src="{{ $product->primary_image_url }}"
              alt="{{ $product->name }}"
              class="main-image"
              width="800"
              height="800"
              loading="eager"
              fetchpriority="high"
              decoding="async"
              data-fallback="{{ asset('images/placeholder-product.png') }}"
              onclick="openImageModal(this.src)"
            >
      </div>

          {{-- Thumbnails --}}
          @php
            // Get all product images
            $productImages = collect($product->images ?? []);
            if ($productImages->isEmpty() && isset($product->id)) {
              $productImages = \App\Models\ProductImage::where('product_id', $product->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
            }
          @endphp
          @if($productImages->count() > 1)
            <div class="thumbnail-list">
              @foreach($productImages as $index => $img)
                @php
                  $imgUrl = is_object($img) ? $img->url : ($img['url'] ?? '');
                  $imgAlt = is_object($img) ? ($img->alt ?? $product->name) : ($img['alt'] ?? $product->name);
                @endphp
                <div
                  class="thumbnail-item {{ $index === 0 ? 'active' : '' }}"
                  data-image-url="{{ $imgUrl }}"
                  onclick="changeMainImage('{{ $imgUrl }}', '{{ $imgAlt }}', this)"
                >
                  <img
                    src="{{ $imgUrl }}"
                    alt="{{ $imgAlt }}"
                    width="80"
                    height="80"
                    loading="lazy"
                    decoding="async"
                    data-fallback="{{ asset('images/placeholder-product.png') }}"
                  >
            </div>
          @endforeach
        </div>
      @endif
        </div>
      </div>

      {{-- Product Info --}}
      <div class="col-12 col-lg-6">
        <div class="product-info">
          {{-- Title --}}
          <h1 class="product-title">{{ $product->name }}</h1>

          {{-- Price --}}
          <div class="product-price-section">
            <div class="price-wrapper">
              @if($product->sale_price && $product->sale_price < $product->price)
                <span class="current-price">${{ number_format($product->sale_price, 2) }}</span>
                <span class="original-price">${{ number_format($product->price, 2) }}</span>
                @php
                  $discount = round((($product->price - $product->sale_price) / $product->price) * 100);
                @endphp
                <span class="discount-badge">-{{ $discount }}%</span>
              @else
                <span class="current-price">${{ number_format($product->price, 2) }}</span>
              @endif
            </div>
    </div>

          {{-- Short Description --}}
        @if($product->short_description)
            <p class="mb-4" style="color: #495057; line-height: 1.6;">{{ $product->short_description }}</p>
        @endif

          {{-- Product Meta --}}
          <div class="product-meta">
            @if($product->brand)
              <div class="meta-item">
                <span class="meta-label">Brand:</span>
                <span class="meta-value">
                  <a href="{{ route('brands.show', ['slug' => $product->brand->slug]) }}">{{ $product->brand->name }}</a>
                </span>
              </div>
            @endif
            <div class="meta-item">
              <span class="meta-label">SKU:</span>
              <span class="meta-value">{{ $product->sku }}</span>
            </div>
            @if($product->category)
              <div class="meta-item">
                <span class="meta-label">Category:</span>
                <span class="meta-value">
                  <a href="{{ route('categories.show', ['slug' => $product->category->slug]) }}">{{ $product->category->name }}</a>
          </span>
              </div>
            @endif
          </div>

          {{-- Size Selection --}}
          @php
            // Get sizes from database since $product is stdClass from cache
            $activeSizes = \App\Models\ProductSize::where('product_id', $product->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('size')
                ->get();
          @endphp
          @if($activeSizes && $activeSizes->count() > 0)
            <div class="size-selection">
              <div class="size-label">Select Size:</div>
              <div class="size-options" id="sizeOptions">
                @foreach($activeSizes as $size)
                  <div
                    class="size-option {{ !$size->isInStock() ? 'disabled' : '' }}"
                    data-size-id="{{ $size->id }}"
                    data-size="{{ $size->size }}"
                    data-price="{{ $size->effective_price }}"
                    data-stock="{{ $size->stock_qty }}"
                    onclick="selectSize(this)"
                  >
                    {{ $size->size }}
                  </div>
                @endforeach
              </div>
              <div id="selectedSizeInfo" class="mt-2" style="font-size: 0.875rem; color: #6c757d; display: none;"></div>
            </div>
          @endif

          {{-- Stock Status --}}
          <div class="stock-status {{ $product->stock_qty > 0 ? 'in-stock' : 'out-of-stock' }}">
            {{ $product->stock_qty > 0 ? 'In Stock' : 'Out of Stock' }}
            @if($product->stock_qty > 0 && $product->stock_qty < 10)
              <span style="font-size: 0.875rem;">(Only {{ $product->stock_qty }} left)</span>
            @endif
        </div>

        {{-- Action Buttons --}}
          <div class="action-buttons">
            <button
              class="btn-primary-action btn-copy-link"
              id="copyLinkBtn"
              onclick="copyProductLink()"
              title="Copy product link"
            >
              <i class="fas fa-link me-2"></i>Copy Link
            </button>
            <button
              type="button"
              class="btn-primary-action btn-wishlist"
              id="wishlistBtn"
              data-product-id="{{ $product->id }}"
              data-product-name="{{ $product->name }}"
              data-product-url="{{ route('products.show', ['slug' => $product->slug]) }}"
              data-product-price="{{ $product->price }}"
              data-product-sale-price="{{ $product->sale_price ?? '' }}"
              data-product-image="{{ $product->primary_image_url }}"
              onclick="toggleWishlist(this)"
            >
              <i class="fas fa-heart me-2"></i>
              <span class="wishlist-text">Add to Wishlist</span>
            </button>
            <button
              type="button"
              class="btn-primary-action btn-whatsapp-order"
              id="whatsappOrderBtn"
              onclick="orderViaWhatsApp()"
            >
              <i class="fab fa-whatsapp me-2"></i>Order via WhatsApp
            </button>
        </div>
      </div>
    </div>
  </div>

    {{-- Description --}}
  @if(!empty($product->description))
      <div class="product-description-section">
        <h2 class="section-title">Product Description</h2>
        <div class="description-content">
          {!! nl2br(e($product->description)) !!}
      </div>
    </div>
  @endif

    {{-- Related Products --}}
    @if(isset($related) && $related->count() > 0)
      <div class="related-products-section">
        <h2 class="section-title">You Might Also Like</h2>
        <div class="row g-4">
          @foreach($related as $p)
            <div class="col-6 col-md-4 col-lg-3">
              <x-product-card :product="$p" />
            </div>
          @endforeach
        </div>
      </div>
    @endif
      </div>
    </div>

{{-- Image Modal --}}
<div class="image-modal" id="imageModal" onclick="closeImageModal()">
  <button class="modal-close" onclick="event.stopPropagation(); closeImageModal()">&times;</button>
  <img class="modal-image" id="modalImage" src="" alt="">
</div>

@endsection

@push('scripts')
<script>
  // Change main image when thumbnail is clicked
  function changeMainImage(imageUrl, imageAlt, thumbnailElement) {
    const mainImage = document.getElementById('mainProductImage');

    // Add fade effect
    mainImage.style.opacity = '0.5';
    mainImage.style.transition = 'opacity 0.3s ease';

    setTimeout(() => {
      delete mainImage.dataset.fallen;
      mainImage.src = imageUrl;
      mainImage.alt = imageAlt || '{{ $product->name }}';
      mainImage.style.opacity = '1';
    }, 150);

    // Update active thumbnail
    document.querySelectorAll('.thumbnail-item').forEach(item => {
      item.classList.remove('active');
    });
    if (thumbnailElement) {
      thumbnailElement.classList.add('active');
    }
  }

  // Auto-rotate images (optional - can be disabled)
  let imageRotationInterval = null;
  let currentImageIndex = 0;

  function startImageRotation() {
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    if (thumbnails.length <= 1) return;

    imageRotationInterval = setInterval(() => {
      currentImageIndex = (currentImageIndex + 1) % thumbnails.length;
      const nextThumbnail = thumbnails[currentImageIndex];
      const imageUrl = nextThumbnail.dataset.imageUrl;
      const imageAlt = nextThumbnail.querySelector('img').alt;
      changeMainImage(imageUrl, imageAlt, nextThumbnail);
    }, 5000); // Change image every 5 seconds
  }

  function stopImageRotation() {
    if (imageRotationInterval) {
      clearInterval(imageRotationInterval);
      imageRotationInterval = null;
    }
  }

  // Start rotation when page loads (optional)
  // Uncomment the line below to enable auto-rotation
  // document.addEventListener('DOMContentLoaded', startImageRotation);

  // Stop rotation when user hovers over gallery
  document.addEventListener('DOMContentLoaded', function() {
    const gallery = document.querySelector('.product-gallery');
    if (gallery) {
      gallery.addEventListener('mouseenter', stopImageRotation);
      gallery.addEventListener('mouseleave', function() {
        // Optionally restart rotation when mouse leaves
        // startImageRotation();
      });
    }
  });

  // Open image modal
  function openImageModal(imageUrl) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageUrl;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  // Close image modal
  function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }

  // Close modal on ESC key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeImageModal();
    }
  });

  // Size selection
  let selectedSize = null;

  function selectSize(element) {
    if (element.classList.contains('disabled')) {
      return;
    }

    // Remove selected class from all sizes
    document.querySelectorAll('.size-option').forEach(opt => {
      opt.classList.remove('selected');
    });

    // Add selected class to clicked size
    element.classList.add('selected');
    selectedSize = {
      id: element.dataset.sizeId,
      size: element.dataset.size,
      price: element.dataset.price,
      stock: element.dataset.stock
    };

    // Update selected size info
    const infoDiv = document.getElementById('selectedSizeInfo');
    if (infoDiv) {
      infoDiv.style.display = 'block';
      infoDiv.innerHTML = `Selected: <strong>${selectedSize.size}</strong>`;
      if (selectedSize.price) {
        infoDiv.innerHTML += ` - $${parseFloat(selectedSize.price).toFixed(2)}`;
      }
    }
  }

  // Copy product link function
  function copyProductLink() {
    const productUrl = window.location.href;
    const copyBtn = document.getElementById('copyLinkBtn');

    // Use modern Clipboard API
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(productUrl).then(() => {
        // Show success feedback
        const originalText = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
        copyBtn.classList.add('copied');

        setTimeout(() => {
          copyBtn.innerHTML = originalText;
          copyBtn.classList.remove('copied');
        }, 2000);
      }).catch(err => {
        console.error('Failed to copy:', err);
        fallbackCopyTextToClipboard(productUrl, copyBtn);
      });
    } else {
      // Fallback for older browsers
      fallbackCopyTextToClipboard(productUrl, copyBtn);
    }
  }

  // Fallback copy function for older browsers
  function fallbackCopyTextToClipboard(text, button) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
      const successful = document.execCommand('copy');
      if (successful) {
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
        button.classList.add('copied');

        setTimeout(() => {
          button.innerHTML = originalText;
          button.classList.remove('copied');
        }, 2000);
      }
    } catch (err) {
      console.error('Fallback copy failed:', err);
      alert('Failed to copy link. Please copy manually: ' + text);
    }

    document.body.removeChild(textArea);
  }

  // Wishlist functions
  const WISHLIST_STORAGE_KEY = 'wishlist_items_v1';

  function getWishlistItems() {
    const items = localStorage.getItem(WISHLIST_STORAGE_KEY);
    return items ? JSON.parse(items) : [];
  }

  function saveWishlistItems(items) {
    localStorage.setItem(WISHLIST_STORAGE_KEY, JSON.stringify(items));
  }

  function isInWishlist(productId) {
    const items = getWishlistItems();
    return items.some(item => item.id === productId);
  }

  function toggleWishlist(button) {
    const productId = parseInt(button.dataset.productId);
    const productName = button.dataset.productName;
    const productUrl = button.dataset.productUrl;
    const productPrice = parseFloat(button.dataset.productPrice);
    const productSalePrice = button.dataset.productSalePrice ? parseFloat(button.dataset.productSalePrice) : null;
    const productImage = button.dataset.productImage;

    let items = getWishlistItems();
    const isInList = isInWishlist(productId);

    if (isInList) {
      // Remove from wishlist
      items = items.filter(item => item.id !== productId);
      button.classList.remove('in-wishlist');
      button.querySelector('.wishlist-text').textContent = 'Add to Wishlist';
      showToast('Removed from wishlist', 'info');
    } else {
      // Add to wishlist
      items.push({
        id: productId,
        name: productName,
        url: productUrl,
        price: productPrice,
        sale_price: productSalePrice,
        image: productImage
      });
      button.classList.add('in-wishlist');
      button.querySelector('.wishlist-text').textContent = 'In Wishlist';
      showToast('Added to wishlist', 'success');
    }

    saveWishlistItems(items);
    updateWishlistCount();
  }

  function updateWishlistCount() {
    // Update wishlist count in navigation if exists
    const count = getWishlistItems().length;
    const countElements = document.querySelectorAll('.wishlist-count');
    countElements.forEach(el => {
      el.textContent = count;
      el.style.display = count > 0 ? 'flex' : 'none';
    });
  }

  function showToast(message, type = 'success') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.style.cssText = `
      position: fixed;
      top: 2rem;
      right: 2rem;
      padding: 1rem 1.5rem;
      background: ${type === 'success' ? '#28a745' : '#17a2b8'};
      color: white;
      border-radius: 6px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      z-index: 10000;
      animation: slideIn 0.3s ease;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.style.animation = 'slideOut 0.3s ease';
      setTimeout(() => {
        document.body.removeChild(toast);
      }, 300);
    }, 3000);
  }

  // Check wishlist status on page load
  document.addEventListener('DOMContentLoaded', function() {
    const wishlistBtn = document.getElementById('wishlistBtn');
    if (wishlistBtn) {
      const productId = parseInt(wishlistBtn.dataset.productId);
      if (isInWishlist(productId)) {
        wishlistBtn.classList.add('in-wishlist');
        wishlistBtn.querySelector('.wishlist-text').textContent = 'In Wishlist';
      }
    }
    updateWishlistCount();
  });

  // Add CSS animations for toast
  const style = document.createElement('style');
  style.textContent = `
    @keyframes slideIn {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    @keyframes slideOut {
      from {
        transform: translateX(0);
        opacity: 1;
      }
      to {
        transform: translateX(100%);
        opacity: 0;
      }
    }
  `;
  document.head.appendChild(style);

  // WhatsApp Order — number set by admin, exposed as window.SITE_WHATSAPP_NUMBER from layout
  const WA_NUMBER = window.SITE_WHATSAPP_NUMBER || '15551234567';

  function orderViaWhatsApp() {
    const name = @json($product->name);
    const price = @json($product->sale_price && $product->sale_price < $product->price ? $product->sale_price : $product->price);
    const originalPrice = @json($product->price);
    const hasSale = @json($product->sale_price && $product->sale_price < $product->price);
    const url = window.location.href;
    const sku = @json($product->sku);

    let sizeText = '';
    if (selectedSize && selectedSize.size) {
      sizeText = `\nSize: ${selectedSize.size}`;
      if (selectedSize.price) {
        sizeText += ` ($${parseFloat(selectedSize.price).toFixed(2)})`;
      }
    }

    let priceText = `$${parseFloat(price).toFixed(2)}`;
    if (hasSale) {
      priceText += ` (was $${parseFloat(originalPrice).toFixed(2)})`;
    }

    const message = `Hi, I'd like to order this product:\n\n` +
      `*${name}*\n` +
      `Price: ${priceText}${sizeText}\n` +
      `SKU: ${sku}\n\n` +
      `${url}`;

    const waUrl = `https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(message)}`;
    window.open(waUrl, '_blank');
  }
</script>
@endpush
