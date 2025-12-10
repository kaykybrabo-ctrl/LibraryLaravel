<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'book_id', 'loan_date', 'return_date', 'returned_at'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = ['loan_date' => 'date', 'return_date' => 'date', 'returned_at' => 'date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
