<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Book aggregate root.
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $photo
 * @property float|null $price
 * @property int $author_id
 */
class Book extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['title', 'description', 'photo', 'price', 'author_id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'price' => 'float',
    ];
    /**
     * Get the author that owns the book (including soft deleted authors).
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class)->withTrashed();
    }
    /**
     * Get all loans associated with the book.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
    /**
     * Determine if the book currently has an active (not returned) loan.
     */
    public function isBorrowed(): bool
    {
        return $this->loans()
            ->whereNull('returned_at')
            ->exists();
    }
    /**
     * Get all reviews for the book.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    /**
     * Get all favorite relations where this book is marked as favorite.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }
    /**
     * Get order items that reference this book.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
