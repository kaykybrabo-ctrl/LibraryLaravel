<?php
namespace App\GraphQL\Queries\Concerns;

use App\Models\Order;

trait OrderQueries
{
    public function myOrders(): iterable
    {
        $user = $this->requireUser();
        return Order::where('user_id', $user->id)
            ->with(['items.book.author'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function orders(): iterable
    {
        $this->requireAdmin();
        return Order::with(['user', 'items.book.author'])
            ->orderByDesc('created_at')
            ->get();
    }
}
