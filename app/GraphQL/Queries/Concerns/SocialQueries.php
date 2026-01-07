<?php
namespace App\GraphQL\Queries\Concerns;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Review;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

trait SocialQueries
{
    public function favoriteBookByUser($rootValue, array $args): ?Book
    {
        $auth = $this->requireUser();
        $userId = (int) $args['user_id'];
        if (!Gate::forUser($auth)->allows('admin') && (int) $auth->id !== $userId) {
            throw new AuthorizationException(__('errors.forbidden'));
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
