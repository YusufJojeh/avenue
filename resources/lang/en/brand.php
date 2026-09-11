<?php

return [
    'titles' => [
        'create' => 'Create Brand',
        'edit' => 'Edit Brand',
        'current_logo' => 'Current Logo',
    ],
    'fields' => [
        'name' => 'Brand Name',
        'slug' => 'Slug',
        'logo' => 'Logo',
        'is_external' => 'External Brand',
        'is_active' => 'Active',
        'sort_order' => 'Sort Order',
    ],
    'help' => [
        'slug' => 'URL-friendly identifier',
        'logo' => 'Upload brand logo (JPG/PNG/WebP). Max 3MB',
        'logo_current_prefix' => 'Current logo: ',
    ],
    'actions' => [
        'save' => 'Save',
        'remove' => 'Remove',
        'confirm_remove' => 'Are you sure you want to remove this brand?',
    ],
    'toast' => [
        'saved' => 'Brand saved successfully',
        'deleted' => 'Brand deleted successfully',
        'delete_failed' => 'Failed to delete brand: :error',
        'upload_failed' => 'Logo upload failed: :error',
    ],
];
