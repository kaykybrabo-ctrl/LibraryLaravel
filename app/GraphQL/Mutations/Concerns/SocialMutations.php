<?php
namespace App\GraphQL\Mutations\Concerns;

use App\Http\Requests\ReviewRequest;
use App\Http\Requests\ToggleFavoriteRequest;
use App\Models\Book;
use App\Models\Favorite;
use App\Models\Review;
use Illuminate\Auth\Access\AuthorizationException;

trait SocialMutations
{
    public function toggleFavorite($rootValue, array $args): Book
    {
        $input = $args['input'] ?? [];
        $user = $this->requireUser();
        $isAdmin = (bool) ($user->is_admin ?? false);
        if ($isAdmin) {
            throw new AuthorizationException(__('errors.admin_cannot_favorite'));
        }
        $data = $this->validatedInput($input, new ToggleFavoriteRequest());
        if ((int) $data['user_id'] !== (int) $user->id) {
            throw new AuthorizationException(__('errors.access_not_allowed'));
        }
        $existing = Favorite::withTrashed()->where('user_id', $data['user_id'])->first();
        if ($existing) {
            if ((int) $existing->book_id === (int) $data['book_id']) {
                if ($existing->trashed()) {
                    $existing->restore();
                } else {
                    $existing->delete();
                }
            } else {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                $existing->book_id = (int) $data['book_id'];
                $existing->save();
            }
        } else {
            Favorite::create([
                'user_id' => (int) $data['user_id'],
                'book_id' => (int) $data['book_id'],
            ]);
        }
        return Book::with('author')->findOrFail($data['book_id']);
    }

    public function removeFavorite($rootValue, array $args): array
    {
        $user = $this->requireUser();
        $isAdmin = (bool) ($user->is_admin ?? false);
        $userId = (int) ($args['user_id'] ?? 0);
        if ((int) $user->id !== $userId && !$isAdmin) {
            throw new AuthorizationException(__('errors.access_not_allowed'));
        }
        Favorite::where('user_id', $userId)->delete();
        return ['message' => __('messages.favorite_removed')];
    }

    public function upsertReview($rootValue, array $args): Review
    {
        $input = $args['input'] ?? [];
        $user = $this->requireUser();
        $isAdmin = (bool) ($user->is_admin ?? false);
        if ($isAdmin) {
            throw new AuthorizationException(__('errors.admin_cannot_review'));
        }
        $data = $this->validatedInput($input, new ReviewRequest());
        $review = Review::withTrashed()
            ->where('user_id', $user->id)
            ->where('book_id', (int) $data['book_id'])
            ->first();
        if ($review) {
            if ($review->trashed()) {
                $review->restore();
            }
            $review->rating = (int) $data['rating'];
            $review->comment = $data['comment'] ?? null;
            $review->save();
        } else {
            $review = Review::create([
                'user_id' => (int) $user->id,
                'book_id' => (int) $data['book_id'],
                'rating' => (int) $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);
        }
        return $review->load('user', 'book');
    }

    public function deleteReview($rootValue, array $args): array
    {
        $user = $this->requireUser();
        $isAdmin = (bool) ($user->is_admin ?? false);
        $id = (int) ($args['id'] ?? 0);
        $review = Review::findOrFail($id);
        if ((int) $review->user_id !== (int) $user->id && !$isAdmin) {
            throw new AuthorizationException(__('errors.access_not_allowed'));
        }
        $review->delete();
        return ['message' => __('messages.review_deleted')];
    }
}
