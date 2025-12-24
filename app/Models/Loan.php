<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'book_id', 'loan_date', 'return_date', 'returned_at'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = ['loan_date' => 'date', 'return_date' => 'date', 'returned_at' => 'date'];
    protected $appends = ['status', 'is_overdue', 'days_remaining'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

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

    protected function daysRemaining(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->return_date || $this->returned_at) {
                return null;
            }
            $today = now()->startOfDay();
            $due = $this->return_date->copy()->startOfDay();
            return $today->diffInDays($due, false);
        });
    }
}
