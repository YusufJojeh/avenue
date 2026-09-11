<?php

namespace App\Models;

use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Models\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_active',
        'permissions',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions'          => 'array',
        'email_verified_at'    => 'datetime',
        'is_active'           => 'boolean',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
           'id'         => Where::class,
           'name'       => Like::class,
           'email'      => Like::class,
           'updated_at' => WhereDateStartEnd::class,
           'created_at' => WhereDateStartEnd::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'updated_at',
        'created_at',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin || $this->hasRole('admin');
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    /**
     * Get all permissions for the user (combines role permissions and user permissions)
     */
    public function getAllPermissions(): array
    {
        $rolePermissions = [];
        
        // Get permissions from roles
        foreach ($this->roles as $role) {
            if (is_array($role->permissions)) {
                $rolePermissions = array_merge($rolePermissions, $role->permissions);
            }
        }
        
        // Get user-specific permissions
        $userPermissions = is_array($this->permissions) ? $this->permissions : [];
        
        // Merge and return unique permissions
        return array_unique(array_merge($rolePermissions, $userPermissions));
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        $allPermissions = $this->getAllPermissions();
        return isset($allPermissions[$permission]) && $allPermissions[$permission] == 1;
    }
}
