<?php
namespace App\Services;

use App\Http\Requests\ReviewRequest;
use App\Http\Requests\ToggleFavoriteRequest;
use App\Models\Book;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Domain service for social features such as favorites and reviews.
 */
class SocialService
{
    /**
     * Toggle favorite book for a user.
     *
     * @param array<string,mixed> $data Validated data from ToggleFavoriteRequest
     */
    public function toggleFavorite(User $user, array $data): Book
    {
        $isAdmin = (bool) ($user->is_admin ?? false);
        if ($isAdmin) {
            throw new AuthorizationException(__('errors.admin_cannot_favorite'));
        }

        if ((int) $data['user_id'] !== (int) $user->id) {
            throw new AuthorizationException(__('errors.access_not_allowed'));
        }

        $existing = Favorite::withTrashed()
            ->where('user_id', $data['user_id'])
            ->first();

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

    public function removeFavorite(User $caller, int $userId): void
    {
        $isAdmin = (bool) ($caller->is_admin ?? false);
        if ((int) $caller->id !== $userId && !$isAdmin) {
            throw new AuthorizationException(__('errors.access_not_allowed'));
        }

        Favorite::where('user_id', $userId)->delete();
    }

    /**
     * Create or update a review for a book by the given user.
     *
     * @param array<string,mixed> $data Validated data from ReviewRequest
     */
    public function upsertReview(User $user, array $data): Review
    {
        $isAdmin = (bool) ($user->is_admin ?? false);
        if ($isAdmin) {
            throw new AuthorizationException(__('errors.admin_cannot_review'));
        }

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

    public function deleteReview(User $caller, int $reviewId): void
    {
        $isAdmin = (bool) ($caller->is_admin ?? false);

        $review = Review::findOrFail($reviewId);
        if ((int) $review->user_id !== (int) $caller->id && !$isAdmin) {
            throw new AuthorizationException(__('errors.access_not_allowed'));
        }

        $review->delete();
    }
}
