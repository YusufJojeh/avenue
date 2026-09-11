{{-- Crystal Card Component --}}
@props(['class' => '', 'hover' => true, 'loading' => false])

<div class="crystal-card {{ $class }}" 
     @if($loading) data-loading="true" @endif
     @if($hover) data-hover="true" @endif>
  {{ $slot }}
</div>

@if($loading)
  <div class="skeleton-card">
    <div class="skeleton-image"></div>
    <div class="skeleton-content">
      <div class="skeleton-line"></div>
      <div class="skeleton-line short"></div>
      <div class="skeleton-line"></div>
    </div>
  </div>
@endif
