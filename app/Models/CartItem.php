<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class CartItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'book_id', 'quantity'];
    protected $hidden = ['created_at', 'updated_at'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class)->withTrashed();
    }
}
