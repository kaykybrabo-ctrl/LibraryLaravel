<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Order line item model.
 *
 * @property int $id
 * @property int $order_id
 * @property int $book_id
 * @property int $quantity
 * @property float $unit_price
 * @property-read Order $order
 * @property-read Book $book
 */
class OrderItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['order_id', 'book_id', 'quantity', 'unit_price'];
    protected $hidden = ['created_at', 'updated_at'];
    /**
     * Get the parent order for this item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }
    /**
     * Get the book associated with this order item.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class)->withTrashed();
    }
}
