<?php

return [
    'titles' => [
        'create' => 'Create Offer',
        'edit' => 'Edit Offer',
        'current_banner' => 'Current Banner',
    ],
    'fields' => [
        'title' => 'Title',
        'description' => 'Description',
        'type' => 'Type',
        'value' => 'Value',
        'starts_at' => 'Starts at',
        'ends_at' => 'Ends at',
        'is_active' => 'Active',
        'banner' => 'Banner',
    ],
    'help' => [
        'type' => 'If free shipping, value is ignored.',
        'value' => 'For percent: 1–100. For fixed: >= 0',
        'banner' => 'Upload banner (JPG/PNG/WebP). Max 5MB',
        'banner_current_prefix' => 'Current: ',
    ],
    'types' => [
        'percent' => 'Percent (%)',
        'fixed' => 'Fixed amount',
        'free_shipping' => 'Free shipping',
    ],
    'actions' => [
        'save' => 'Save',
        'remove' => 'Remove',
        'confirm_remove' => 'Delete this offer?',
    ],
    'toast' => [
        'saved' => 'Offer saved successfully',
        'deleted' => 'Offer deleted successfully',
        'delete_failed' => 'Failed to delete offer: :error',
        'upload_failed' => 'Banner upload failed: :error',
    ],
];
