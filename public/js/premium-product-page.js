/**
 * Premium Product Details Page - Enhanced JavaScript
 * 300% Enhanced Features with Ultra-Premium Interactions
 */

class PremiumProductPage {
  constructor() {
    this.init();
    this.setupEventListeners();
    this.initializePremiumFeatures();
  }

  init() {
    this.elements = {
      mainImage: document.getElementById('mainImage'),
      thumbnails: document.querySelectorAll('.thumbnail-premium'),
      lightbox: document.getElementById('lightbox'),
      lightboxImg: document.getElementById('lightboxImg'),
      progressBar: document.getElementById('progressBar'),
      fabButton: document.getElementById('fabButton'),
      toast: document.getElementById('toastNotification'),
      qtyInput: document.getElementById('quantity'),
      qtyMinus: document.getElementById('qtyMinus'),
      qtyPlus: document.getElementById('qtyPlus'),
      addToCartBtn: document.getElementById('addToCartBtn'),
      wishlistBtn: document.getElementById('wishlistBtn'),
      tabButtons: document.querySelectorAll('.tab-btn-premium')
    };

    this.state = {
      currentImageIndex: 0,
      isLightboxOpen: false,
      currentTab: 'details',
      quantity: 1,
      isWishlisted: false
    };

    this.animations = {
      duration: 300,
      easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
    };
  }

  setupEventListeners() {
    // Premium scroll progress
    window.addEventListener('scroll', this.throttle(this.updateProgress.bind(this), 16));
    
    // Premium image gallery
    this.setupImageGallery();
    
    // Premium lightbox
    this.setupLightbox();
    
    // Premium quantity controls
    this.setupQuantityControls();
    
    // Premium action buttons
    this.setupActionButtons();
    
    // Premium tabs
    this.setupTabs();
    
    // Premium keyboard shortcuts
    this.setupKeyboardShortcuts();
    
    // Premium floating action button
    this.setupFAB();
    
    // Premium error handling
    window.addEventListener('error', this.handleError.bind(this));
  }

  initializePremiumFeatures() {
    // Initialize scroll reveal animations
    this.initializeScrollReveal();
    
    // Initialize premium loading states
    this.initializeLoadingStates();
    
    // Initialize premium micro-interactions
    this.initializeMicroInteractions();
    
    console.log(' Premium Product Page Initialized with 300% Enhanced Features!');
  }

  // Premium Scroll Progress
  updateProgress() {
    const scrollTop = window.pageYOffset;
    const docHeight = document.body.scrollHeight - window.innerHeight;
    const scrollPercent = Math.min((scrollTop / docHeight) * 100, 100);
    
    if (this.elements.progressBar) {
      this.elements.progressBar.style.width = ${scrollPercent}%;
    }
  }

  // Premium Image Gallery
  setupImageGallery() {
    if (!this.elements.thumbnails.length) return;

    this.elements.thumbnails.forEach((thumbnail, index) => {
      thumbnail.addEventListener('click', () => {
        this.switchToImage(index);
      });
    });
  }

  switchToImage(index) {
    const thumbnail = this.elements.thumbnails[index];
    const newSrc = thumbnail.dataset.mainSrc;
    
    // Premium transition effect
    this.elements.mainImage.style.opacity = '0';
    this.elements.mainImage.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
      this.elements.mainImage.src = newSrc;
      this.elements.mainImage.setAttribute('data-full-src', newSrc);
      this.elements.mainImage.style.opacity = '1';
      this.elements.mainImage.style.transform = 'scale(1)';
    }, 150);

    // Update active thumbnail
    this.elements.thumbnails.forEach(t => t.classList.remove('active'));
    thumbnail.classList.add('active');
    thumbnail.classList.add('micro-bounce');
    
    setTimeout(() => thumbnail.classList.remove('micro-bounce'), 600);
    
    this.state.currentImageIndex = index;
  }

  // Premium Lightbox
  setupLightbox() {
    if (!this.elements.mainImage || !this.elements.lightbox) return;

    this.elements.mainImage.addEventListener('click', () => {
      this.openLightbox();
    });

    this.elements.lightbox.addEventListener('click', (e) => {
      if (e.target === this.elements.lightbox) {
        this.closeLightbox();
      }
    });
  }

  openLightbox() {
    const src = this.elements.mainImage.getAttribute('data-full-src') || this.elements.mainImage.src;
    
    this.elements.lightboxImg.src = src;
    this.elements.lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Premium entrance animation
    this.elements.lightboxImg.style.transform = 'scale(0.8) rotate(5deg)';
    this.elements.lightboxImg.style.opacity = '0';
    
    setTimeout(() => {
      this.elements.lightboxImg.style.transform = 'scale(1) rotate(0deg)';
      this.elements.lightboxImg.style.opacity = '1';
    }, 50);
    
    this.state.isLightboxOpen = true;
  }

  closeLightbox() {
    this.elements.lightboxImg.style.transform = 'scale(0.8) rotate(-5deg)';
    this.elements.lightboxImg.style.opacity = '0';
    
    setTimeout(() => {
      this.elements.lightbox.classList.remove('active');
      document.body.style.overflow = '';
    }, 300);
    
    this.state.isLightboxOpen = false;
  }

  // Premium Quantity Controls
  setupQuantityControls() {
    if (!this.elements.qtyInput || !this.elements.qtyMinus || !this.elements.qtyPlus) return;

    const maxQty = parseInt(this.elements.qtyInput.getAttribute('max') || '1', 10);

    this.elements.qtyMinus.addEventListener('click', () => {
      this.updateQuantity(this.state.quantity - 1, maxQty);
    });

    this.elements.qtyPlus.addEventListener('click', () => {
      this.updateQuantity(this.state.quantity + 1, maxQty);
    });

    this.elements.qtyInput.addEventListener('input', () => {
      this.updateQuantity(parseInt(this.elements.qtyInput.value || '1', 10), maxQty);
    });
  }

  updateQuantity(newValue, maxQty) {
    const clampedValue = Math.min(Math.max(newValue, 1), maxQty);
    
    this.state.quantity = clampedValue;
    this.elements.qtyInput.value = clampedValue;
    
    // Premium feedback
    this.elements.qtyInput.classList.add('micro-bounce');
    setTimeout(() => this.elements.qtyInput.classList.remove('micro-bounce'), 600);
    
    // Update button states
    this.elements.qtyMinus.disabled = clampedValue <= 1;
    this.elements.qtyPlus.disabled = clampedValue >= maxQty;
  }

  // Premium Action Buttons
  setupActionButtons() {
    this.setupAddToCart();
    this.setupWishlist();
  }

  setupAddToCart() {
    if (!this.elements.addToCartBtn) return;

    this.elements.addToCartBtn.addEventListener('click', () => {
      this.handleAddToCart();
    });
  }

  handleAddToCart() {
    const originalText = this.elements.addToCartBtn.innerHTML;
    const originalBg = this.elements.addToCartBtn.style.background;
    
    // Premium loading state
    this.elements.addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
    this.elements.addToCartBtn.disabled = true;
    this.elements.addToCartBtn.style.background = 'linear-gradient(135deg, #6b7280, #4b5563)';
    
    // Simulate API call
    setTimeout(() => {
      // Success state with premium effects
      this.elements.addToCartBtn.innerHTML = '<i class="fas fa-check me-2"></i>Added to Cart!';
      this.elements.addToCartBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
      this.elements.addToCartBtn.classList.add('neon-glow');
      
      this.showToast(Added  to cart successfully!, 'success');
      
      // Reset after premium delay
      setTimeout(() => {
        this.elements.addToCartBtn.innerHTML = originalText;
        this.elements.addToCartBtn.disabled = false;
        this.elements.addToCartBtn.style.background = originalBg;
        this.elements.addToCartBtn.classList.remove('neon-glow');
      }, 2500);
    }, 1200);
  }

  setupWishlist() {
    if (!this.elements.wishlistBtn) return;

    this.elements.wishlistBtn.addEventListener('click', () => {
      this.toggleWishlist();
    });
  }

  toggleWishlist() {
    this.state.isWishlisted = !this.state.isWishlisted;
    
    // Premium toggle animation
    this.elements.wishlistBtn.classList.toggle('in-wishlist', this.state.isWishlisted);
    this.elements.wishlistBtn.classList.add('micro-bounce');
    
    // Update button content
    setTimeout(() => {
      this.elements.wishlistBtn.innerHTML = this.state.isWishlisted
        ? '<i class="fas fa-heart me-2"></i>In Wishlist'
        : '<i class="fas fa-heart me-2"></i>Add to Wishlist';
      
      this.elements.wishlistBtn.setAttribute('aria-pressed', this.state.isWishlisted);
      
      const message = this.state.isWishlisted ? 'Added to wishlist!' : 'Removed from wishlist!';
      this.showToast(message, 'success');
    }, 300);
    
    setTimeout(() => this.elements.wishlistBtn.classList.remove('micro-bounce'), 600);
  }

  // Premium Tabs
  setupTabs() {
    this.elements.tabButtons.forEach(button => {
      button.addEventListener('click', () => {
        this.switchTab(button.dataset.tab);
      });
    });
  }

  switchTab(tabKey) {
    // Premium tab switching animation
    this.elements.tabButtons.forEach(button => {
      const isActive = button.dataset.tab === tabKey;
      button.classList.toggle('active', isActive);
      
      if (isActive) {
        button.classList.add('micro-bounce');
        setTimeout(() => button.classList.remove('micro-bounce'), 600);
      }
    });
    
    // Smooth panel transitions
    document.querySelectorAll('.tab-panel').forEach(panel => {
      panel.style.opacity = '0';
      panel.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        panel.classList.add('d-none');
        
        if (panel.id === 	ab-) {
          panel.classList.remove('d-none');
          setTimeout(() => {
            panel.style.opacity = '1';
            panel.style.transform = 'translateY(0)';
          }, 50);
        }
      }, 200);
    });
    
    this.state.currentTab = tabKey;
  }

  // Premium Floating Action Button
  setupFAB() {
    if (!this.elements.fabButton) return;

    this.elements.fabButton.addEventListener('click', () => {
      this.scrollToTop();
    });
  }

  scrollToTop() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
    
    // Premium feedback
    this.elements.fabButton.classList.add('micro-bounce');
    setTimeout(() => this.elements.fabButton.classList.remove('micro-bounce'), 600);
  }

  // Premium Keyboard Shortcuts
  setupKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
      // Escape to close lightbox
      if (e.key === 'Escape' && this.state.isLightboxOpen) {
        this.closeLightbox();
      }
      
      // Space to toggle lightbox
      if (e.key === ' ' && !e.target.matches('input, textarea')) {
        e.preventDefault();
        if (this.state.isLightboxOpen) {
          this.closeLightbox();
        } else {
          this.openLightbox();
        }
      }
      
      // Arrow keys for image navigation
      if (this.state.isLightboxOpen && this.elements.thumbnails.length > 1) {
        if (e.key === 'ArrowLeft') {
          e.preventDefault();
          this.navigateImage(-1);
        } else if (e.key === 'ArrowRight') {
          e.preventDefault();
          this.navigateImage(1);
        }
      }
      
      // Ctrl/Cmd + S to share
      if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        this.shareOnWhatsApp();
      }
    });
  }

  navigateImage(direction) {
    const newIndex = this.state.currentImageIndex + direction;
    const maxIndex = this.elements.thumbnails.length - 1;
    
    if (newIndex >= 0 && newIndex <= maxIndex) {
      this.switchToImage(newIndex);
    }
  }

  // Premium Toast System
  showToast(message, type = 'success') {
    if (!this.elements.toast) return;

    const toastContent = this.elements.toast.querySelector('.toast-content');
    const toastMessage = this.elements.toast.querySelector('.toast-message');
    const icon = this.elements.toast.querySelector('i');
    
    // Update content
    toastMessage.textContent = message;
    this.elements.toast.className = 	oast-premium ;
    
    // Update icon
    if (type === 'success') {
      icon.className = 'fas fa-check-circle me-2';
    } else if (type === 'error') {
      icon.className = 'fas fa-exclamation-circle me-2';
    }
    
    // Show toast
    this.elements.toast.classList.add('show');
    
    // Auto hide
    setTimeout(() => {
      this.elements.toast.classList.remove('show');
    }, 4000);
  }

  // Premium Share Functions
  shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(https://www.facebook.com/sharer/sharer.php?u=, '_blank', 'width=600,height=400');
    this.showToast('Opening Facebook share...', 'success');
  }

  shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    window.open(https://twitter.com/intent/tweet?url=&text=, '_blank', 'width=600,height=400');
    this.showToast('Opening Twitter share...', 'success');
  }

  shareOnWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    window.open(https://wa.me/?text= , '_blank');
    this.showToast('Opening WhatsApp share...', 'success');
  }

  // Premium Scroll Reveal
  initializeScrollReveal() {
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);
    
    document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));
  }

  // Premium Loading States
  initializeLoadingStates() {
    // Add loading shimmer to images
    document.querySelectorAll('img').forEach(img => {
      img.addEventListener('load', () => {
        img.classList.remove('loading-shimmer');
      });
      
      if (!img.complete) {
        img.classList.add('loading-shimmer');
      }
    });
  }

  // Premium Micro-interactions
  initializeMicroInteractions() {
    // Add hover effects to interactive elements
    document.querySelectorAll('.btn-add-to-cart-premium, .btn-wishlist-premium, .share-btn-premium').forEach(btn => {
      btn.addEventListener('mouseenter', () => {
        btn.style.transform = 'translateY(-2px) scale(1.02)';
      });
      
      btn.addEventListener('mouseleave', () => {
        btn.style.transform = 'translateY(0) scale(1)';
      });
    });
  }

  // Premium Error Handling
  handleError(e) {
    console.error('Premium Product Page Error:', e.error);
    this.showToast('Something went wrong. Please refresh the page.', 'error');
  }

  // Utility Functions
  throttle(func, limit) {
    let inThrottle;
    return function() {
      const args = arguments;
      const context = this;
      if (!inThrottle) {
        func.apply(context, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  }
}

// Initialize Premium Product Page when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new PremiumProductPage();
});

// Export for global access
window.PremiumProductPage = PremiumProductPage;
