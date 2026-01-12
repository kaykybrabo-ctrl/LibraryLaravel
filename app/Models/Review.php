<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Review model for book ratings and comments.
 *
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property int $rating
 * @property string|null $comment
 * @property-read User $user
 * @property-read Book $book
 */
class Review extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'book_id', 'rating', 'comment'];
    protected $hidden = ['created_at', 'updated_at'];
    /**
     * Get the user who created the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    /**
     * Get the reviewed book.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class)->withTrashed();
    }
}
