<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * Application user.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property bool $is_admin
 * @property string|null $photo
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'email', 'password', 'is_admin', 'photo'];
    protected $hidden = ['password', 'remember_token', 'created_at', 'updated_at'];
    protected $casts = ['is_admin' => 'boolean'];
    /**
     * Loans associated with the user.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
    /**
     * Reviews authored by the user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    /**
     * Favorite books of the user.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }
    /**
     * Orders placed by the user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    public function getJWTIdentifier(): int
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
