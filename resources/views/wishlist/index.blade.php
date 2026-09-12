@extends('layouts.app')

@section('title', __('common.pages.my_wishlist') . ' - ' . ($siteName ?? 'MyStore'))
@section('meta_robots', 'noindex, follow')
@section('canonical', route('wishlist.index'))

@push('styles')
<style>
  /* ===== Wishlist Page Styles ===== */
  .wishlist-hero {
    background: linear-gradient(135deg,
      rgba(239,68,68,0.1) 0%,
      rgba(240,194,75,0.1) 50%,
      rgba(99,102,241,0.1) 100%);
    border-radius: 20px;
    padding: 3rem 2rem;
    margin-bottom: 3rem;
    position: relative;
    overflow: hidden;
  }

  .wishlist-hero h1 {
    font-size: 3rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ef4444, var(--gold));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1rem;
  }

  .wishlist-hero p {
    font-size: 1.2rem;
    color: var(--muted);
  }

  .wishlist-actions {
    background: var(--glass);
    backdrop-filter: blur(20px);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
  }

  .wishlist-count strong {
    color: #ef4444;
    font-weight: 800;
  }

  .wishlist-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: nowrap;
    align-items: center;
  }

  .btn-wishlist,
  .btn-whatsapp {
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 700;
    font-size: 0.95rem;
    color: white;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    text-decoration: none;
  }

  .btn-wishlist {
    background: linear-gradient(135deg, #ef4444, #dc2626);
  }
  .btn-wishlist:hover { transform: translateY(-2px); }

  .btn-whatsapp {
    background: linear-gradient(135deg, #25d366, #128c7e);
  }
  .btn-whatsapp:hover { transform: translateY(-2px); }

  .wishlist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 2rem;
  }

  .empty-wishlist {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--glass);
    border-radius: 20px;
    border: 1px solid var(--border);
  }

  /* ==== Product Card ==== */
  .product-card {
    border: 1px solid var(--border);
    border-radius: 15px;
    overflow: hidden;
    background: var(--glass);
    display: flex;
    flex-direction: column;
    transition: transform 0.2s;
  }
  .product-card:hover { transform: translateY(-5px); }

  .product-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
  }

  .card-body {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    flex: 1;
  }

  .product-name {
    font-weight: 700;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
  }

  .price-section {
    font-size: 1rem;
    color: #333;
    margin-bottom: 1rem;
    font-weight: 600;
  }

  .product-actions {
    margin-top: auto;
    display: flex;
    justify-content: space-between;
  }

  .icon-btn {
    border: none;
    background: var(--glass);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text);
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .icon-btn:hover { background: var(--border); color: #ef4444; }

  /* Toast */
  #toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #333;
    color: #fff;
    padding: 1rem 1.5rem;
    border-radius: 10px;
    opacity: 0;
    transition: opacity 0.3s, transform 0.3s;
    transform: translateY(20px);
    z-index: 9999;
  }
  #toast.show {
    opacity: 1;
    transform: translateY(0);
  }
</style>
@endpush

@section('content')
<div class="container py-4">

  <x-enhanced-breadcrumb :items="[
    ['url' => url('/'), 'label' => __('common.nav.home')],
    ['label' => __('common.nav.wishlist')],
  ]" />

  {{-- Hero --}}
  <div class="wishlist-hero">
    <h1>{{ __('common.pages.my_wishlist') }}</h1>
    <p>{{ __('common.pages.your_favorite_products') }}</p>
  </div>

  {{-- Wishlist Actions --}}
  <div class="wishlist-actions">
    <div class="wishlist-count">
      {!! str_replace(':count', '<strong id="wishlistCount">0</strong>', __('common.pages.you_have_items_count')) !!}
    </div>
    <div class="wishlist-buttons">
      <button type="button" class="btn-wishlist" id="clearWishlistBtn">
        <i class="fas fa-trash"></i> {{ __('common.pages.clear_all') }}
      </button>
      <button type="button" class="btn-whatsapp" id="shareWishlistBtn">
        <i class="fab fa-whatsapp"></i> {{ __('common.actions.order_via_whatsapp') }}
      </button>
    </div>
  </div>

  {{-- Wishlist Grid --}}
  <div class="wishlist-grid" id="wishlistGrid"></div>

  {{-- Empty State --}}
  <div class="empty-wishlist" id="emptyWishlist" style="display: none;">
    <div class="empty-wishlist-icon">💔</div>
    <h3>{{ __('common.pages.your_wishlist_empty') }}</h3>
    <p>{{ __('common.pages.start_adding_products') }}</p>
    <a href="{{ route('products.index') }}" class="btn btn-vel-gold">
      <i class="fas fa-shopping-bag me-2"></i>{{ __('common.actions.browse_products') }}
    </a>
  </div>

  {{-- Toast --}}
  <div id="toast">{{ __('common.toast.link_copied') }}</div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const I18N = {
    view: @json(__('common.actions.view')),
    orderViaWhatsapp: @json(__('common.actions.order_via_whatsapp')),
    remove: @json(__('common.actions.remove')),
    linkCopied: @json(__('common.toast.link_copied')),
    copyFailed: @json(__('common.toast.copy_failed')),
    confirmClearWishlist: @json(__('common.messages.confirm_clear_wishlist')),
    wishlistEmpty: @json(__('common.pages.your_wishlist_empty')),
  };
  const WISHLIST_STORAGE_KEY = 'wishlist_items_v1';
  const SITE_WHATSAPP_NUMBER = window.SITE_WHATSAPP_NUMBER || '15551234567';

  function getWishlistItems() {
    const items = localStorage.getItem(WISHLIST_STORAGE_KEY);
    return items ? JSON.parse(items) : [];
  }

  function updateWishlistCount() {
    const items = getWishlistItems();
    document.getElementById('wishlistCount').textContent = items.length;
  }

  function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2000);
  }

  function renderWishlistItems() {
    const items = getWishlistItems();
    const grid = document.getElementById('wishlistGrid');
    const emptyState = document.getElementById('emptyWishlist');

    if (items.length === 0) {
      grid.style.display = 'none';
      emptyState.style.display = 'block';
      return;
    }

    grid.style.display = 'grid';
    emptyState.style.display = 'none';

    grid.innerHTML = items.map(item => {
      const hasSale = item.sale_price && parseFloat(item.sale_price) > 0 && parseFloat(item.sale_price) < parseFloat(item.price);
      const effectivePrice = hasSale ? parseFloat(item.sale_price) : parseFloat(item.price);
      const priceHtml = hasSale
        ? `<span style="text-decoration:line-through;opacity:0.6;font-size:0.85rem;">$${parseFloat(item.price).toFixed(2)}</span> <span style="color:var(--gold);font-weight:800;">$${effectivePrice.toFixed(2)}</span>`
        : `$${effectivePrice.toFixed(2)}`;

      const waMsg = encodeURIComponent(
        `Hi, I'd like to order:\n\n*${item.name}*\nPrice: $${effectivePrice.toFixed(2)}\n${window.location.origin + item.url}`
      );

      return `
        <div class="product-card" data-product-id="${item.id}">
          <img src="${item.image}" alt="${item.name}">
          <div class="card-body">
            <h5 class="product-name">${item.name}</h5>
            <div class="price-section">${priceHtml}</div>
            <div class="product-actions">
              <a href="${item.url}" class="icon-btn" title="${I18N.view}"><i class="fas fa-eye"></i></a>
              <a href="https://wa.me/${SITE_WHATSAPP_NUMBER}?text=${waMsg}" target="_blank" class="icon-btn" title="${I18N.orderViaWhatsapp}" style="color:#25d366;"><i class="fab fa-whatsapp"></i></a>
              <button type="button" class="icon-btn remove-from-wishlist" data-product-id="${item.id}" title="${I18N.remove}">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      `;
    }).join('');

    document.querySelectorAll('.remove-from-wishlist').forEach(btn => {
      btn.addEventListener('click', function() {
        removeFromWishlist(this.dataset.productId);
      });
    });

    document.querySelectorAll('.copy-link').forEach(btn => {
      btn.addEventListener('click', function() {
        navigator.clipboard.writeText(this.dataset.url)
          .then(() => showToast(I18N.linkCopied))
          .catch(() => showToast(I18N.copyFailed));
      });
    });
  }

  function removeFromWishlist(productId) {
    let items = getWishlistItems();
    items = items.filter(item => item.id !== productId);
    localStorage.setItem(WISHLIST_STORAGE_KEY, JSON.stringify(items));
    updateWishlistCount();
    renderWishlistItems();
  }

  function clearWishlist() {
    if (confirm(I18N.confirmClearWishlist)) {
      localStorage.removeItem(WISHLIST_STORAGE_KEY);
      updateWishlistCount();
      renderWishlistItems();
    }
  }

  function shareWishlist() {
    const items = getWishlistItems();
    if (items.length === 0) {
      showToast(I18N.wishlistEmpty);
      return;
    }

    let total = 0;
    let message = `Hi, I'd like to order these products:\n\n`;
    items.forEach((item, index) => {
      const price = item.sale_price && parseFloat(item.sale_price) > 0 && parseFloat(item.sale_price) < parseFloat(item.price)
        ? parseFloat(item.sale_price)
        : parseFloat(item.price);
      total += price;
      message += `${index + 1}. *${item.name}*\n   Price: $${price.toFixed(2)}\n   ${window.location.origin + item.url}\n\n`;
    });
    message += `Total: *$${total.toFixed(2)}*\n`;
    message += `Items: ${items.length}\n\nThank you!`;

    const waUrl = `https://wa.me/${SITE_WHATSAPP_NUMBER}?text=${encodeURIComponent(message)}`;
    window.open(waUrl, '_blank');
  }

  updateWishlistCount();
  renderWishlistItems();

  document.getElementById('clearWishlistBtn').addEventListener('click', clearWishlist);
  document.getElementById('shareWishlistBtn').addEventListener('click', shareWishlist);
});
</script>
@endpush
