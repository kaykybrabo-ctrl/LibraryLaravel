<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Shopping cart item model.
 *
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property int $quantity
 * @property-read User $user
 * @property-read Book $book
 */
class CartItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'book_id', 'quantity'];
    protected $hidden = ['created_at', 'updated_at'];
    /**
     * Get the user who owns this cart item (including soft-deleted users).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    /**
     * Get the book referenced by this cart item (including soft-deleted books).
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class)->withTrashed();
    }
}
