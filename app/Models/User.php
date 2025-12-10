<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'password', 'is_admin', 'photo'];
    protected $hidden = ['password', 'remember_token', 'created_at', 'updated_at'];
    protected $casts = ['is_admin' => 'boolean'];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
