<?php

namespace App\GraphQL\Queries;

use App\Models\Book;
use App\Models\Author;
use App\Models\User;
use App\Models\Loan;
use App\Models\Favorite;
use App\Models\Review;
use App\Services\BookService;
use App\Services\AuthorService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use GraphQL\Type\Definition\ResolveInfo;

class LibraryQuery
{
    public function __construct(private BookService $books, private AuthorService $authors)
    {
    }

    public function books($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (!empty($args['all']) || empty($args['per_page'])) {
            return $this->books->listAll();
        }

        $perPage = max(1, (int) ($args['per_page'] ?? 10));

        return $this->books->paginate($perPage)->items();
    }

    public function book($rootValue, array $args): Book
    {
        return $this->books->find((int) $args['id']);
    }

    public function authors(): iterable
    {
        return $this->authors->listAll();
    }

    public function author($rootValue, array $args): Author
    {
        return $this->authors->find((int) $args['id']);
    }

    public function users($rootValue, array $args): iterable
    {
        $perPage = max(1, (int) ($args['per_page'] ?? 100));

        return User::orderBy('id')->paginate($perPage)->items();
    }

    public function me($rootValue = null, array $args = [], GraphQLContext $context = null): ?User
    {
        return auth('api')->user();
    }

    public function loans(): iterable
    {
        $user = auth('api')->user();

        if (!$user || !$user->is_admin) {
            throw new \Exception('Forbidden');
        }

        return Loan::with(['user', 'book.author'])->get();
    }

    public function userLoans($rootValue, array $args): iterable
    {
        $auth = auth('api')->user();
        $userId = (int) $args['user_id'];

        if ($auth && !$auth->is_admin && (int) $auth->id !== $userId) {
            throw new \Exception('Forbidden');
        }

        return Loan::where('user_id', $userId)
            ->with('book.author')
            ->get();
    }

    public function activeBookIds(): array
    {
        return Loan::whereNull('returned_at')
            ->pluck('book_id')
            ->unique()
            ->values()
            ->all();
    }

    public function favoriteBookByUser($rootValue, array $args): ?Book
    {
        $auth = auth('api')->user();
        $userId = (int) $args['user_id'];

        if ($auth && !$auth->is_admin && (int) $auth->id !== $userId) {
            throw new \Exception('Forbidden');
        }

        $fav = Favorite::where('user_id', $userId)->first();

        if (!$fav) {
            return null;
        }

        return Book::with('author')->find($fav->book_id);
    }

    public function reviewsByBook($rootValue, array $args): iterable
    {
        $bookId = (int) $args['book_id'];

        return Review::where('book_id', $bookId)
            ->with('user')
            ->orderByDesc('created_at')
            ->get();
    }
}
