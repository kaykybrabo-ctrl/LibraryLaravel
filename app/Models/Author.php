<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Author aggregate root.
 *
 * @property int $id
 * @property string $name
 * @property string|null $bio
 * @property string|null $photo
 */
class Author extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'bio', 'photo'];
    protected $hidden = ['created_at', 'updated_at'];
    /**
     * Get the books written by the author.
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
