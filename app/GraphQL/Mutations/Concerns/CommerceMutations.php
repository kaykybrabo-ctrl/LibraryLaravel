<?php
namespace App\GraphQL\Mutations\Concerns;

use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\RemoveCartItemRequest;
use App\Http\Requests\UpsertCartItemRequest;
use App\Models\CartItem;
use App\Models\Order;
use App\Services\CommerceService;
use Illuminate\Auth\Access\AuthorizationException;

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

        /** @var CommerceService $service */
        $service = app(CommerceService::class);

        return $service->checkout($user, $data);
    }

    public function upsertCartItem($rootValue, array $args): CartItem
    {
        $user = $this->requireUser();
        $isAdmin = (bool) ($user->is_admin ?? false);
        if ($isAdmin) {
            throw new AuthorizationException(__('errors.admin_cannot_add_to_cart'));
        }

        $data = $this->validatedInput($args, new UpsertCartItemRequest());

        /** @var CommerceService $service */
        $service = app(CommerceService::class);

        return $service->upsertCartItem($user, $data);
    }

    public function removeCartItem($rootValue, array $args): array
    {
        $user = $this->requireUser();
        $bookId = (int) $this->validatedInput($args, new RemoveCartItemRequest())['book_id'];

        /** @var CommerceService $service */
        $service = app(CommerceService::class);

        $service->removeCartItem($user, $bookId);

        return ['message' => __('messages.cart_item_removed')];
    }

    public function clearCart(): array
    {
        $user = $this->requireUser();

        /** @var CommerceService $service */
        $service = app(CommerceService::class);

        $service->clearCart($user);

        return ['message' => __('messages.cart_cleared')];
    }
}
