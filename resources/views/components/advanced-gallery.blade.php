{{-- Advanced Gallery Component --}}
@props(['product', 'images' => null, 'show360' => false, 'showComparison' => false, 'showVideo' => false])

@php
  $galleryImages = $images ?? $product->images ?? collect();
  $mainImage = $product->primary_image_url ?? asset('images/placeholder-product.png');
@endphp

<div class="advanced-gallery" data-gallery-id="product-{{ $product->id }}">
  {{-- Main Image Container --}}
  <div class="gallery-main-container">
    <img src="{{ $mainImage }}" 
         class="gallery-main-image" 
         alt="{{ $product->name }}"
         loading="eager"
         data-full-src="{{ $mainImage }}">
    
    {{-- Loading State --}}
    <div class="gallery-loading" style="display: none;">
      <div class="gallery-spinner"></div>
    </div>
    
    {{-- Error State --}}
    <div class="gallery-error" style="display: none;">
      <div class="gallery-error-icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <div class="gallery-error-message">
        Failed to load image
      </div>
    </div>
  </div>

  {{-- Thumbnail Grid --}}
  @if($galleryImages->count() > 1)
    <div class="gallery-thumbnails">
      @foreach($galleryImages as $index => $image)
        <img src="{{ $image->url ?? $image }}" 
             class="gallery-thumbnail {{ $index === 0 ? 'active' : '' }}" 
             alt="{{ $image->alt ?? $product->name }}"
             loading="lazy"
             data-full-src="{{ $image->url ?? $image }}"
             data-index="{{ $index }}">
      @endforeach
    </div>
  @endif

  {{-- 360° View Container (Hidden by default) --}}
  @if($show360)
    <div class="gallery-360-container" style="display: none;">
      <img src="{{ $mainImage }}" 
           class="gallery-360-image" 
           alt="{{ $product->name }} 360° view">
      
      <div class="gallery-360-controls">
        <button class="rotate-btn" data-action="rotate-left" title="Rotate Left">
          <i class="fas fa-undo"></i>
        </button>
        <div class="rotate-indicator">0°</div>
        <button class="rotate-btn" data-action="rotate-right" title="Rotate Right">
          <i class="fas fa-redo"></i>
        </button>
      </div>
    </div>
  @endif

  {{-- Video Container (Hidden by default) --}}
  @if($showVideo && $product->video_url)
    <div class="gallery-video-container" style="display: none;">
      <video class="gallery-video" 
             controls 
             preload="metadata"
             poster="{{ $mainImage }}">
        <source src="{{ $product->video_url }}" type="video/mp4">
        Your browser does not support the video tag.
      </video>
      
      <div class="video-controls">
        <button class="video-play-btn" data-action="play-pause">
          <i class="fas fa-play"></i>
        </button>
        <div class="video-progress">
          <div class="video-progress-bar"></div>
        </div>
        <div class="video-time">0:00 / 0:00</div>
      </div>
    </div>
  @endif

  {{-- Comparison Container (Hidden by default) --}}
  @if($showComparison && $galleryImages->count() >= 2)
    <div class="gallery-comparison" style="display: none;">
      <div class="comparison-container">
        <img src="{{ $galleryImages->first()->url ?? $galleryImages->first() }}" 
             class="comparison-image before" 
             alt="{{ $product->name }} - Before">
        <img src="{{ $galleryImages->skip(1)->first()->url ?? $galleryImages->skip(1)->first() }}" 
             class="comparison-image after" 
             alt="{{ $product->name }} - After">
        <div class="comparison-slider">
          <div class="comparison-handle"></div>
        </div>
      </div>
    </div>
  @endif

  {{-- Navigation Arrows --}}
  @if($galleryImages->count() > 1)
    <button class="gallery-nav prev" data-action="prev" title="Previous Image">
      <i class="fas fa-chevron-left"></i>
    </button>
    <button class="gallery-nav next" data-action="next" title="Next Image">
      <i class="fas fa-chevron-right"></i>
    </button>
  @endif
</div>

{{-- Fullscreen Lightbox --}}
<div class="gallery-lightbox" id="lightbox-{{ $product->id }}">
  <div class="lightbox-content">
    <img class="lightbox-image" alt="{{ $product->name }} full view">
    <button class="lightbox-close" data-action="close-lightbox" title="Close">
      <i class="fas fa-times"></i>
    </button>
  </div>
</div>
