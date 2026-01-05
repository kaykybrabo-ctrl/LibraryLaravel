<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class OrderItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['order_id', 'book_id', 'quantity', 'unit_price'];
    protected $hidden = ['created_at', 'updated_at'];
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class)->withTrashed();
    }
}
