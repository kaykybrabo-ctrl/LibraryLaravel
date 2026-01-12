<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Favorite relation between user and book.
 *
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property-read User $user
 * @property-read Book $book
 */
class Favorite extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'book_id'];
    protected $hidden = ['created_at', 'updated_at'];
    /**
     * Get the user who marked the book as favorite.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    /**
     * Get the favorited book.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class)->withTrashed();
    }
}
