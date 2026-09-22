@if($image_url)
<div class="mb-3">
    <label class="form-label">{{ $title ?? 'Current Image' }}</label>
    <div class="d-flex align-items-center gap-3">
        <img src="{{ $image_url }}"
             alt="Current image"
             class="current-image-preview"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="current-image-placeholder">
            Image not found
        </div>
    </div>
</div>
@endif
