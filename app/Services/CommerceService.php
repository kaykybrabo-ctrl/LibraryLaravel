<?php
namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Models\Book;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Domain service for commerce operations (checkout and cart).
 */
class CommerceService
{
    /**
     * Perform checkout for a user based on validated CheckoutRequest data.
     *
     * @param array<string,mixed> $data
     */
    public function checkout(User $user, array $data): Order
    {
        $itemsInput = $data['items'];

        /** @var Order $order */
        $order = DB::transaction(function () use ($user, $itemsInput) {
            $bookIds = array_column($itemsInput, 'book_id');
            $books = Book::whereIn('id', $bookIds)->get()->keyBy('id');
            $total = 0.0;

            $order = Order::create([
                'user_id' => $user->id,
                'total' => 0,
                'status' => 'paid',
            ]);

            foreach ($itemsInput as $item) {
                $bookId = (int) $item['book_id'];
                $qty = (int) $item['quantity'];
                $book = $books->get($bookId);
                if (!$book) {
                    throw new NotFoundException(__('errors.not_found'), Book::class);
                }
                $unit = (float) ($book->price ?? 0);
                if ($unit <= 0) {
                    $unit = (float) config('commerce.default_price', 19.90);
                }
                $lineTotal = $unit * $qty;
                $total += $lineTotal;
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $bookId,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                ]);
            }

            $order->total = $total;
            $order->save();

            return $order;
        });

        return $order->load(['items.book.author', 'user']);
    }

    /**
     * Create or update a cart item for the user based on validated UpsertCartItemRequest data.
     *
     * @param array<string,mixed> $data
     */
    public function upsertCartItem(User $user, array $data): CartItem
    {
        $bookId = (int) $data['book_id'];
        $qty = (int) $data['quantity'];

        /** @var CartItem $item */
        $item = DB::transaction(function () use ($user, $bookId, $qty) {
            $existing = CartItem::withTrashed()
                ->where('user_id', $user->id)
                ->where('book_id', $bookId)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                $existing->quantity = $qty;
                $existing->save();
                return $existing;
            }

            return CartItem::create([
                'user_id' => (int) $user->id,
                'book_id' => $bookId,
                'quantity' => $qty,
            ]);
        });

        return $item->load('book.author');
    }

    public function removeCartItem(User $user, int $bookId): void
    {
        DB::transaction(function () use ($user, $bookId) {
            CartItem::where('user_id', $user->id)
                ->where('book_id', $bookId)
                ->delete();
        });
    }

    public function clearCart(User $user): void
    {
        DB::transaction(function () use ($user) {
            CartItem::where('user_id', $user->id)->delete();
        });
    }
}
