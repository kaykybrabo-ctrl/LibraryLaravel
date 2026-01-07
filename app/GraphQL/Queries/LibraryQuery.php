<?php
namespace App\GraphQL\Queries;
use App\Models\Book;
use App\Models\Author;
use App\Models\User;
use App\Models\Loan;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\Order;
use App\Models\CartItem;
use App\Services\BookService;
use App\Services\AuthorService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Gate;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use GraphQL\Type\Definition\ResolveInfo;

use App\GraphQL\Queries\Concerns\AdminTrashQueries;
use App\GraphQL\Queries\Concerns\BookQueries;
use App\GraphQL\Queries\Concerns\AuthorQueries;
use App\GraphQL\Queries\Concerns\UserQueries;
use App\GraphQL\Queries\Concerns\LoanQueries;
use App\GraphQL\Queries\Concerns\SocialQueries;
use App\GraphQL\Queries\Concerns\OrderQueries;
use App\GraphQL\Queries\Concerns\CartQueries;

class LibraryQuery
{
    use AdminTrashQueries;
    use BookQueries;
    use AuthorQueries;
    use UserQueries;
    use LoanQueries;
    use SocialQueries;
    use OrderQueries;
    use CartQueries;

    public function __construct(private BookService $books, private AuthorService $authors)
    {
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
        $user = $this->requireUser();
        if (!Gate::forUser($user)->allows('admin')) {
            throw new AuthorizationException(__('errors.forbidden'));
        }
        return $user;
    }
}
