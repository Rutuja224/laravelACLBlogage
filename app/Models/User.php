<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable , SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role() {
    return $this->belongsTo(Role::class);
    }


    public function posts() {

        return $this->hasMany(Post::class);

    }

    public function hasPermission(string $permissionName): bool
    {
        $role = $this->role; // singular role
        if ($role && $role->permissions()->where('name', $permissionName)->exists()) {
            return true;
        }
        return false;
    }

    public function hasRole($roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        $role = $this->role;
        return $role && in_array($role->name, $roles);
    }

    public function permissions() {
    return $this->belongsToMany(Permission::class); // or adapt if different
}

    
    
}
