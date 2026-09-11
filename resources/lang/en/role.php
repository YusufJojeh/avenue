<?php

return [
    'titles' => [
        'create' => 'Create Role',
        'edit' => 'Edit Role',
    ],
    'descriptions' => [
        'edit' => 'Modify the privileges and permissions associated with a specific role.',
        'role' => 'Defines a set of privileges that grant users access to various services and allow them to perform specific tasks or operations.',
        'permission' => 'Permission/Privilege',
    ],
    'fields' => [
        'name' => 'Name',
        'slug' => 'Slug',
    ],
    'help' => [
        'name' => 'Role display name',
        'slug' => 'Actual name in the system',
    ],
    'actions' => [
        'save' => 'Save',
        'remove' => 'Remove',
        'add' => 'Add',
    ],
    'toast' => [
        'saved' => 'Role was saved',
        'deleted' => 'Role was removed',
        'delete_failed' => 'Failed to delete role: :error',
    ],
];
