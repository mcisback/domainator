<?php

namespace App\Models;

use App\Traits\HasColumnType;
use App\Traits\HasGetColumns;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasGetColumns;

    protected $with = [
        'roles',
        'roles.permissions',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'firstName',
        'lastName',
        'email',
        'password',
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

    public function ftpServers() {
        return $this->hasMany(FtpServer::class, 'user_id', 'id');
    }

    public function isAdmin() {
        return $this->hasRole('admin');
    }

    public static function getAllRoles() {
        return Role::all('id', 'name');
    }

    public static function getStaffMembers() {
        return static::role(
            Role::where('name', 'staff')->pluck('id')->all()
        )->get();
    }

    public static function getAdmin() {
        return static::role(
            Role::where('name', 'admin')->pluck('id')->all()
        )->get()->first();
    }

    // user model function
    public function getPermissionArray()
    {
        return $this->getAllPermissions()->mapWithKeys(function($pr){
            return [$pr['name'] => true];
        });

    }
}
