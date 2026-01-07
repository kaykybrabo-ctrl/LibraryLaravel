<?php
namespace App\GraphQL\Mutations;
use App\Models\User;
use App\Models\Book;
use App\Models\Author;
use App\Models\Loan;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Mail\ResetPasswordMail;
use App\Exceptions\ClientException;
use App\Exceptions\NotFoundException;
use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\CreateAuthorRequest;
use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\LoanRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RemoveCartItemRequest;
use App\Http\Requests\RequestPasswordResetRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ReviewRequest;
use App\Http\Requests\ToggleFavoriteRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UploadImageRequest;
use App\Http\Requests\UpsertCartItemRequest;
use App\Services\BookService;
use App\Services\AuthorService;
use App\Jobs\SendBookDueNotification;
use App\Jobs\SendPasswordResetEmail;
use App\Jobs\SendCartEngagementEmail;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

use App\GraphQL\Mutations\Concerns\AuthMutations;
use App\GraphQL\Mutations\Concerns\BookMutations;
use App\GraphQL\Mutations\Concerns\AuthorMutations;
use App\GraphQL\Mutations\Concerns\LoanMutations;
use App\GraphQL\Mutations\Concerns\SocialMutations;
use App\GraphQL\Mutations\Concerns\ImageMutations;
use App\GraphQL\Mutations\Concerns\ProfileMutations;
use App\GraphQL\Mutations\Concerns\CommerceMutations;

class LibraryMutation
{
    use AuthMutations;
    use BookMutations;
    use AuthorMutations;
    use LoanMutations;
    use SocialMutations;
    use ImageMutations;
    use ProfileMutations;
    use CommerceMutations;

    public function __construct(private BookService $books, private AuthorService $authors)
    {
    }

    protected function validatedInput(array $input, FormRequest $form): array
    {
        $messages = method_exists($form, 'messages') ? $form->messages() : [];
        $validator = Validator::make($input, $form->rules(), $messages);
        if ($validator->fails()) {
            throw new ClientException($validator->errors()->first(), 'validation_failed');
        }
        return $validator->validated();
    }

    protected function requireUser(): User
    {
        $user = auth('api')->user();
        if (!$user) {
            throw new AuthenticationException(__('errors.unauthenticated'));
        }
        return $user;
    }

    protected function requireAdmin(): User
    {
        $user = auth('api')->user();
        if (!$user) {
            throw new AuthenticationException(__('errors.unauthenticated'));
        }
        if (!Gate::forUser($user)->allows('admin')) {
            throw new AuthorizationException(__('errors.forbidden'));
        }
        return $user;
    }
}
