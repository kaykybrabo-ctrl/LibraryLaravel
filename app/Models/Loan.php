<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Book loan entity.
 *
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property \Carbon\Carbon|null $loan_date
 * @property \Carbon\Carbon|null $return_date
 * @property \Carbon\Carbon|null $returned_at
 * @property-read string $status
 * @property-read bool $is_overdue
 * @property-read int|null $days_remaining
 */
class Loan extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'book_id', 'loan_date', 'return_date', 'returned_at'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = ['loan_date' => 'date', 'return_date' => 'date', 'returned_at' => 'date'];
    protected $appends = ['status', 'is_overdue', 'days_remaining'];
    /**
     * Borrowing user (including soft deleted users).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    /**
     * Borrowed book (including soft deleted books).
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class)->withTrashed();
    }
    /**
     * Accessor for computed loan status.
     */
    protected function status(): Attribute
    {
        return Attribute::get(function () {
            if ($this->returned_at) {
                return 'returned';
            }
            if ($this->is_overdue) {
                return 'overdue';
            }
            return 'active';
        });
    }
    /**
     * Accessor that indicates whether the loan is overdue.
     */
    protected function isOverdue(): Attribute
    {
        return Attribute::get(function () {
            if ($this->returned_at || !$this->return_date) {
                return false;
            }
            $today = now()->startOfDay();
            $due = $this->return_date->copy()->startOfDay();
            return $due->lt($today);
        });
    }
    /**
     * Accessor for the number of days remaining (or overdue) for the loan.
     */
    protected function daysRemaining(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->return_date || $this->returned_at) {
                return null;
            }
            $today = now()->startOfDay();
            $due = $this->return_date->copy()->startOfDay();
            return (int) $today->diffInDays($due, false);
        });
    }
}
