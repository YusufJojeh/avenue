@if($summary)
    <div class="alert alert-info">
        Total: {{ $summary['total'] }} &middot; Ready: {{ $summary['valid'] }} &middot;
        Warnings: {{ $summary['warnings'] }} &middot; Invalid: {{ $summary['invalid'] }}
    </div>
@endif
@if($result)
    <div class="alert alert-success">
        Products imported: {{ $result['imported'] }} &middot; Products skipped: {{ $result['skipped'] }} &middot;
        Images imported: {{ $result['images_imported'] ?? 0 }} &middot; Image warnings: {{ $result['image_warnings'] ?? 0 }} &middot;
        Failed: {{ $result['failed'] }}
    </div>
@endif
