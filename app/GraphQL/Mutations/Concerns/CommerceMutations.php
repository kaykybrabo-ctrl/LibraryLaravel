<?php
namespace App\GraphQL\Mutations\Concerns;

use App\Exceptions\NotFoundException;
use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\RemoveCartItemRequest;
use App\Http\Requests\UpsertCartItemRequest;
use App\Models\Book;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

trait CommerceMutations
{
    public function checkout($rootValue, array $args): Order
    {
        $user = $this->requireUser();
        $isAdmin = (bool) ($user->is_admin ?? false);
        if ($isAdmin) {
            throw new AuthorizationException(__('errors.admin_cannot_checkout'));
        }
        $input = $args['input'] ?? [];
        $data = $this->validatedInput($input, new CheckoutRequest());
        $itemsInput = $data['items'];
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
                    $unit = 19.90;
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

    public function upsertCartItem($rootValue, array $args): CartItem
    {
        $user = $this->requireUser();
        $isAdmin = (bool) ($user->is_admin ?? false);
        if ($isAdmin) {
            throw new AuthorizationException(__('errors.admin_cannot_add_to_cart'));
        }
        $data = $this->validatedInput($args, new UpsertCartItemRequest());
        $bookId = (int) $data['book_id'];
        $qty = (int) $data['quantity'];
        $existing = CartItem::withTrashed()
            ->where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->first();
        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->quantity = $qty;
            $existing->save();
            return $existing->load('book.author');
        }
        $item = CartItem::create([
            'user_id' => (int) $user->id,
            'book_id' => $bookId,
            'quantity' => $qty,
        ]);
        return $item->load('book.author');
    }

    public function removeCartItem($rootValue, array $args): array
    {
        $user = $this->requireUser();
        $bookId = (int) $this->validatedInput($args, new RemoveCartItemRequest())['book_id'];
        CartItem::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->delete();
        return ['message' => __('messages.cart_item_removed')];
    }

    public function clearCart(): array
    {
        $user = $this->requireUser();
        CartItem::where('user_id', $user->id)->delete();
        return ['message' => __('messages.cart_cleared')];
    }
}
