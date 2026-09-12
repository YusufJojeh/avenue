@if($image_url)
<div class="mb-3">
    <label class="form-label">{{ $title ?? 'Current Image' }}</label>
    <div class="d-flex align-items-center gap-3">
        <img src="{{ $image_url }}"
             alt="Current image"
             class="current-image-preview"
             data-fallback-hide="true">
        <div class="current-image-placeholder">
            Image not found
        </div>
        <div class="small text-muted">
            <div><strong>Path:</strong> {{ $image_path }}</div>
            <div><strong>URL:</strong> <a href="{{ $image_url }}" target="_blank">{{ $image_url }}</a></div>
            <div><strong>Status:</strong>
                <span class="image-status-indicator">Checking&hellip;</span>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var img = document.querySelector('.current-image-preview');
    var status = document.querySelector('.image-status-indicator');
    if (!img) return;

    img.addEventListener('load', function() {
        if (status) status.innerHTML = '<span class="text-success">Loaded</span>';
    });
    img.addEventListener('error', function() {
        if (status) status.innerHTML = '<span class="text-danger">Failed to load</span>';
    });

    if (img.complete) {
        if (img.naturalWidth > 0) {
            if (status) status.innerHTML = '<span class="text-success">Loaded</span>';
        } else {
            if (status) status.innerHTML = '<span class="text-danger">Failed to load</span>';
        }
    }
})();
</script>
@endif
