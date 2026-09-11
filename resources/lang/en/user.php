<?php

return [
    'titles' => [
        'create' => 'Create User',
        'edit' => 'Edit User',
    ],
    'descriptions' => [
        'edit' => 'User profile and privileges, including their associated role.',
    ],
    'fields' => [
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm New Password',
    ],
    'help' => [
        'password_leave_empty' => 'Leave empty to keep current password',
        'password_enter_new' => 'Enter the password to be set',
    ],
    'actions' => [
        'save' => 'Save',
        'remove' => 'Remove',
        'impersonate' => 'Impersonate user',
        'confirm_impersonate' => 'You can revert to your original state by logging out.',
        'confirm_remove' => 'Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.',
    ],
    'toast' => [
        'saved' => 'User saved successfully',
        'deleted' => 'User deleted successfully',
        'delete_failed' => 'Failed to delete user: :error',
    ],
];
