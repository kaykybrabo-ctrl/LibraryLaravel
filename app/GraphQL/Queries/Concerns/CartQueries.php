<?php
namespace App\GraphQL\Queries\Concerns;

use App\Models\CartItem;

trait CartQueries
{
    public function myCart(): iterable
    {
        $user = $this->requireUser();
        return CartItem::where('user_id', $user->id)
            ->with('book.author')
            ->orderByDesc('updated_at')
            ->get();
    }
}
