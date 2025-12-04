<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens;
    protected $fillable = ['name', 'email', 'password', 'is_admin', 'photo'];
    protected $hidden = ['password', 'remember_token', 'created_at', 'updated_at'];
    protected $casts = ['is_admin' => 'boolean'];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
