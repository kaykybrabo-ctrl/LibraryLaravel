<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Commerce order.
 *
 * @property int $id
 * @property int $user_id
 * @property float $total
 * @property string $status
 */
class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'total', 'status'];
    protected $hidden = ['updated_at'];
    protected $casts = [
        'total' => 'float',
    ];
    /**
     * Owning user (including soft deleted users).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    /**
     * Order items composing this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
