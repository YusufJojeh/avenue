<?php

return [
    'titles' => [
        'create' => 'Create Category',
        'edit' => 'Edit Category',
        'current_image' => 'Current Image',
    ],
    'fields' => [
        'parent_id' => 'Parent Category',
        'name' => 'Category Name',
        'slug' => 'Slug',
        'description' => 'Description',
        'image' => 'Image',
        'is_active' => 'Active',
        'sort_order' => 'Sort Order',
    ],
    'help' => [
        'parent_id' => 'Leave empty if no parent category',
        'slug' => 'Unique URL identifier',
        'image' => 'Upload category image (JPG/PNG/WebP). Max 3MB',
        'image_current_prefix' => 'Current image: ',
    ],
    'actions' => [
        'save' => 'Save',
        'remove' => 'Remove',
        'confirm_remove' => 'Are you sure you want to remove this category?',
    ],
    'toast' => [
        'saved' => 'Category saved successfully',
        'deleted' => 'Category deleted successfully',
        'delete_failed' => 'Failed to delete category: :error',
        'upload_failed' => 'Image upload failed: :error',
    ],
    'validation' => [
        'parent_self' => 'Category cannot be its own parent.',
    ],
];
