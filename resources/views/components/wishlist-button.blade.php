{{-- Wishlist Button Component --}}
@props(['product', 'size' => 'medium', 'position' => 'inline', 'showText' => true])

@php
  $sizeClasses = [
    'small' => 'wishlist-btn-sm',
    'medium' => 'wishlist-btn-md',
    'large' => 'wishlist-btn-lg'
  ];

  $positionClasses = [
    'inline' => 'wishlist-btn-inline',
    'floating' => 'wishlist-btn-floating',
    'sticky' => 'wishlist-btn-sticky'
  ];
@endphp

<button type="button"
        class="wishlist-btn {{ $sizeClasses[$size] }} {{ $positionClasses[$position] }}"
        data-product-id="{{ $product->id }}"
        data-product-name="{{ $product->name }}"
        data-product-url="{{ route('products.show', ['slug' => $product->slug]) }}"
        aria-label="Add to wishlist"
        title="Add to wishlist">

  <span class="wishlist-icon">
    <i class="fas fa-heart"></i>
  </span>

  @if($showText)
    <span class="wishlist-text">Add to Wishlist</span>
  @endif

  <span class="wishlist-count" style="display: none;">0</span>
</button>

<style>
/* ===== Wishlist Button Styles ===== */
.wishlist-btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border: 2px solid var(--glass-border);
  background: var(--glass-bg);
  backdrop-filter: var(--glass-blur-light);
  -webkit-backdrop-filter: var(--glass-blur-light);
  color: var(--text-primary);
  font-weight: 600;
  transition: all var(--transition-normal);
  cursor: pointer;
  outline: none;
  user-select: none;
  -webkit-tap-highlight-color: transparent;
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-light);
}

.wishlist-btn:hover {
  border-color: var(--primary-gold);
  background: var(--surface-glass);
  color: var(--primary-gold);
  transform: translateY(-2px);
  box-shadow: var(--shadow-gold);
}

.wishlist-btn:focus-visible {
  outline: 2px solid var(--primary-gold);
  outline-offset: 2px;
}

.wishlist-btn:active {
  transform: translateY(0);
}

/* Size Variations */
.wishlist-btn-sm {
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  min-height: 36px;
}

.wishlist-btn-md {
  padding: 0.75rem 1rem;
  font-size: 1rem;
  min-height: 44px;
}

.wishlist-btn-lg {
  padding: 1rem 1.25rem;
  font-size: 1.125rem;
  min-height: 52px;
}

/* Position Variations */
.wishlist-btn-inline {
  position: relative;
}

.wishlist-btn-floating {
  position: fixed;
  top: 50%;
  right: 2rem;
  transform: translateY(-50%);
  z-index: var(--z-fixed);
  border-radius: 50%;
  width: 60px;
  height: 60px;
  padding: 0;
  box-shadow: 0 8px 32px rgba(0,0,0,0.2);
}

.wishlist-btn-floating .wishlist-text {
  display: none;
}

.wishlist-btn-sticky {
  position: sticky;
  top: 2rem;
  align-self: flex-start;
}

/* Active State (In Wishlist) */
.wishlist-btn.in-wishlist {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  border-color: #ef4444;
  color: white;
  box-shadow: 0 8px 32px rgba(239,68,68,0.3);
}

.wishlist-btn.in-wishlist:hover {
  background: linear-gradient(135deg, #dc2626, #b91c1c);
  border-color: #dc2626;
  color: white;
}

.wishlist-btn.in-wishlist .wishlist-text::after {
  content: "ed";
}

/* Icon Animation */
.wishlist-icon {
  position: relative;
  transition: transform var(--transition-normal);
}

.wishlist-btn:hover .wishlist-icon {
  transform: scale(1.1);
}

.wishlist-btn.in-wishlist .wishlist-icon {
  animation: heartBeat 0.6s ease-in-out;
}

@keyframes heartBeat {
  0% { transform: scale(1); }
  50% { transform: scale(1.3); }
  100% { transform: scale(1); }
}

/* Loading State */
.wishlist-btn.loading {
  pointer-events: none;
  opacity: 0.7;
}

.wishlist-btn.loading .wishlist-icon {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Count Badge */
.wishlist-count {
  position: absolute;
  top: -8px;
  right: -8px;
  background: var(--primary-gold);
  color: var(--text-primary);
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .wishlist-btn-floating {
    right: 1rem;
    width: 50px;
    height: 50px;
  }

  .wishlist-btn-sm {
    padding: 0.4rem 0.6rem;
    font-size: 0.8rem;
    min-height: 32px;
  }

  .wishlist-btn-md {
    padding: 0.6rem 0.8rem;
    font-size: 0.9rem;
    min-height: 40px;
  }

  .wishlist-btn-lg {
    padding: 0.8rem 1rem;
    font-size: 1rem;
    min-height: 48px;
  }
}

/* Dark Mode */
html[data-theme="dark"] .wishlist-btn {
  background: var(--glass-bg-dark);
  border-color: var(--glass-border-dark);
  color: var(--text-primary);
}

html[data-theme="dark"] .wishlist-btn:hover {
  background: var(--surface-glass);
  border-color: var(--primary-gold);
  color: var(--primary-gold);
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
  .wishlist-btn,
  .wishlist-icon {
    transition: none;
  }

  .wishlist-btn.in-wishlist .wishlist-icon {
    animation: none;
  }

  .wishlist-btn.loading .wishlist-icon {
    animation: none;
  }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
  .wishlist-btn {
    border-width: 3px;
  }

  .wishlist-btn.in-wishlist {
    border-width: 3px;
  }
}
</style>
