<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Book extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['title', 'description', 'photo', 'price', 'author_id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $with = ['author'];
    protected $casts = [
        'price' => 'float',
    ];
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class)->withTrashed();
    }
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
