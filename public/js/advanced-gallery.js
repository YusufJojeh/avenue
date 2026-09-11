/* ===== Advanced Gallery JavaScript - Part 1: Core Class ===== */

class AdvancedGallery {
  constructor(container) {
    this.container = container;
    this.currentImageIndex = 0;
    this.images = [];
    this.currentMode = 'standard';
    this.zoomLevel = 1;
    this.isDragging = false;
    this.startX = 0;
    this.startY = 0;
    this.translateX = 0;
    this.translateY = 0;
    this.rotationAngle = 0;
    
    this.init();
  }

  init() {
    this.setupElements();
    this.bindEvents();
    this.loadImages();
  }

  setupElements() {
    this.mainContainer = this.container.querySelector('.gallery-main-container');
    this.mainImage = this.container.querySelector('.gallery-main-image');
    this.thumbnails = this.container.querySelectorAll('.gallery-thumbnail');
    this.zoomControls = this.container.querySelector('.gallery-zoom-controls');
    this.modeSelector = this.container.querySelector('.gallery-mode-selector');
    this.lightbox = this.container.querySelector('.gallery-lightbox');
    
    // Create missing elements if they don't exist
    this.createMissingElements();
  }

  createMissingElements() {
    // Create zoom controls if they don't exist
    if (!this.zoomControls) {
      this.zoomControls = document.createElement('div');
      this.zoomControls.className = 'gallery-zoom-controls';
      this.zoomControls.innerHTML = `
        <button class="zoom-btn" data-action="zoom-in" title="Zoom In">
          <i class="fas fa-plus"></i>
        </button>
        <button class="zoom-btn" data-action="zoom-out" title="Zoom Out">
          <i class="fas fa-minus"></i>
        </button>
        <button class="zoom-btn" data-action="zoom-reset" title="Reset Zoom">
          <i class="fas fa-expand-arrows-alt"></i>
        </button>
      `;
      this.mainContainer.appendChild(this.zoomControls);
    }

    // Create mode selector if it doesn't exist
    if (!this.modeSelector) {
      this.modeSelector = document.createElement('div');
      this.modeSelector.className = 'gallery-mode-selector';
      this.modeSelector.innerHTML = `
        <button class="mode-btn active" data-mode="standard">Standard</button>
        <button class="mode-btn" data-mode="360">360°</button>
        <button class="mode-btn" data-mode="comparison">Compare</button>
      `;
      this.mainContainer.appendChild(this.modeSelector);
    }
  }

  bindEvents() {
    // Zoom controls
    this.zoomControls.addEventListener('click', (e) => {
      const action = e.target.closest('.zoom-btn')?.dataset.action;
      if (action) this.handleZoom(action);
    });

    // Mode selector
    this.modeSelector.addEventListener('click', (e) => {
      const mode = e.target.closest('.mode-btn')?.dataset.mode;
      if (mode) this.switchMode(mode);
    });

    // Thumbnail clicks
    this.thumbnails.forEach((thumb, index) => {
      thumb.addEventListener('click', () => this.switchImage(index));
    });

    // Main image interactions
    this.mainImage.addEventListener('click', () => this.openLightbox());
    this.mainImage.addEventListener('mousedown', (e) => this.startDrag(e));
    this.mainImage.addEventListener('mousemove', (e) => this.drag(e));
    this.mainImage.addEventListener('mouseup', () => this.endDrag());
    this.mainImage.addEventListener('mouseleave', () => this.endDrag());

    // Touch events for mobile
    this.mainImage.addEventListener('touchstart', (e) => this.startTouch(e));
    this.mainImage.addEventListener('touchmove', (e) => this.touchMove(e));
    this.mainImage.addEventListener('touchend', () => this.endTouch());

    // Keyboard navigation
    document.addEventListener('keydown', (e) => this.handleKeyboard(e));
  }

  loadImages() {
    // Extract images from thumbnails
    this.images = Array.from(this.thumbnails).map(thumb => ({
      src: thumb.src,
      alt: thumb.alt,
      fullSrc: thumb.dataset.fullSrc || thumb.src
    }));

    if (this.images.length > 0) {
      this.updateMainImage(0);
    }
  }

  switchImage(index) {
    if (index >= 0 && index < this.images.length) {
      this.currentImageIndex = index;
      this.updateMainImage(index);
      this.updateThumbnails(index);
    }
  }

  updateMainImage(index) {
    const image = this.images[index];
    if (image) {
      this.mainImage.src = image.src;
      this.mainImage.alt = image.alt;
      this.mainImage.dataset.fullSrc = image.fullSrc;
    }
  }

  updateThumbnails(activeIndex) {
    this.thumbnails.forEach((thumb, index) => {
      thumb.classList.toggle('active', index === activeIndex);
    });
  }

  handleZoom(action) {
    switch (action) {
      case 'zoom-in':
        this.zoomIn();
        break;
      case 'zoom-out':
        this.zoomOut();
        break;
      case 'zoom-reset':
        this.resetZoom();
        break;
    }
  }

  zoomIn() {
    if (this.zoomLevel < 4) {
      this.zoomLevel = Math.min(this.zoomLevel * 1.5, 4);
      this.applyZoom();
    }
  }

  zoomOut() {
    if (this.zoomLevel > 1) {
      this.zoomLevel = Math.max(this.zoomLevel / 1.5, 1);
      this.applyZoom();
    }
  }

  resetZoom() {
    this.zoomLevel = 1;
    this.translateX = 0;
    this.translateY = 0;
    this.applyZoom();
  }

  applyZoom() {
    const scale = this.zoomLevel;
    this.mainImage.style.transform = `scale(${scale}) translate(${this.translateX}px, ${this.translateY}px)`;
    
    // Update container cursor
    if (scale > 1) {
      this.mainContainer.classList.add('zoom-active');
    } else {
      this.mainContainer.classList.remove('zoom-active');
    }

    // Update zoom button states
    const zoomInBtn = this.zoomControls.querySelector('[data-action="zoom-in"]');
    const zoomOutBtn = this.zoomControls.querySelector('[data-action="zoom-out"]');
    
    zoomInBtn.disabled = this.zoomLevel >= 4;
    zoomOutBtn.disabled = this.zoomLevel <= 1;
  }

  switchMode(mode) {
    this.currentMode = mode;
    
    // Update mode buttons
    this.modeSelector.querySelectorAll('.mode-btn').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.mode === mode);
    });

    // Apply mode-specific functionality
    this.applyMode(mode);
  }

  applyMode(mode) {
    // Remove all mode classes
    this.mainContainer.classList.remove('mode-360', 'mode-comparison', 'mode-video');
    
    switch (mode) {
      case '360':
        this.mainContainer.classList.add('mode-360');
        this.init360Mode();
        break;
      case 'comparison':
        this.mainContainer.classList.add('mode-comparison');
        this.initComparisonMode();
        break;
      case 'video':
        this.mainContainer.classList.add('mode-video');
        this.initVideoMode();
        break;
      default:
        this.initStandardMode();
    }
  }

  initStandardMode() {
    // Standard image viewing mode
    this.mainImage.style.cursor = 'zoom-in';
  }

  init360Mode() {
    // 360° view mode
    this.mainImage.style.cursor = 'grab';
    this.rotationAngle = 0;
  }

  initComparisonMode() {
    // Image comparison mode
    this.mainImage.style.cursor = 'col-resize';
  }

  initVideoMode() {
    // Video player mode
    this.mainImage.style.cursor = 'pointer';
  }

  openLightbox() {
    if (this.lightbox) {
      const lightboxImg = this.lightbox.querySelector('.lightbox-image');
      if (lightboxImg) {
        lightboxImg.src = this.mainImage.dataset.fullSrc || this.mainImage.src;
        this.lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    }
  }

  closeLightbox() {
    if (this.lightbox) {
      this.lightbox.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  handleKeyboard(e) {
    switch (e.key) {
      case 'Escape':
        this.closeLightbox();
        break;
      case 'ArrowLeft':
        this.switchImage(this.currentImageIndex - 1);
        break;
      case 'ArrowRight':
        this.switchImage(this.currentImageIndex + 1);
        break;
      case '+':
      case '=':
        e.preventDefault();
        this.zoomIn();
        break;
      case '-':
        e.preventDefault();
        this.zoomOut();
        break;
      case '0':
        e.preventDefault();
        this.resetZoom();
        break;
    }
  }

  // Drag functionality for zoomed images
  startDrag(e) {
    if (this.zoomLevel > 1) {
      this.isDragging = true;
      this.startX = e.clientX - this.translateX;
      this.startY = e.clientY - this.translateY;
      this.mainImage.style.cursor = 'grabbing';
    }
  }

  drag(e) {
    if (this.isDragging && this.zoomLevel > 1) {
      e.preventDefault();
      this.translateX = e.clientX - this.startX;
      this.translateY = e.clientY - this.startY;
      this.applyZoom();
    }
  }

  endDrag() {
    this.isDragging = false;
    if (this.zoomLevel > 1) {
      this.mainImage.style.cursor = 'grab';
    } else {
      this.mainImage.style.cursor = 'zoom-in';
    }
  }

  // Touch events for mobile
  startTouch(e) {
    if (this.zoomLevel > 1) {
      this.isDragging = true;
      const touch = e.touches[0];
      this.startX = touch.clientX - this.translateX;
      this.startY = touch.clientY - this.translateY;
    }
  }

  touchMove(e) {
    if (this.isDragging && this.zoomLevel > 1) {
      e.preventDefault();
      const touch = e.touches[0];
      this.translateX = touch.clientX - this.startX;
      this.translateY = touch.clientY - this.startY;
      this.applyZoom();
    }
  }

  endTouch() {
    this.isDragging = false;
  }
}

// Initialize galleries when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  const galleryContainers = document.querySelectorAll('.advanced-gallery');
  galleryContainers.forEach(container => {
    new AdvancedGallery(container);
  });
});
