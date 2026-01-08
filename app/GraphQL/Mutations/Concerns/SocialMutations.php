<?php
namespace App\GraphQL\Mutations\Concerns;

use App\Http\Requests\ReviewRequest;
use App\Http\Requests\ToggleFavoriteRequest;
use App\Models\Book;
use App\Models\Favorite;
use App\Models\Review;
use App\Services\SocialService;

trait SocialMutations
{
    public function toggleFavorite($rootValue, array $args): Book
    {
        $input = $args['input'] ?? [];
        $user = $this->requireUser();
        $data = $this->validatedInput($input, new ToggleFavoriteRequest());

        /** @var SocialService $service */
        $service = app(SocialService::class);

        return $service->toggleFavorite($user, $data);
    }

    public function removeFavorite($rootValue, array $args): array
    {
        $user = $this->requireUser();
        $userId = (int) ($args['user_id'] ?? 0);

        /** @var SocialService $service */
        $service = app(SocialService::class);

        $service->removeFavorite($user, $userId);
        return ['message' => __('messages.favorite_removed')];
    }

    public function upsertReview($rootValue, array $args): Review
    {
        $input = $args['input'] ?? [];
        $user = $this->requireUser();
        $data = $this->validatedInput($input, new ReviewRequest());

        /** @var SocialService $service */
        $service = app(SocialService::class);

        return $service->upsertReview($user, $data);
    }

    public function deleteReview($rootValue, array $args): array
    {
        $user = $this->requireUser();
        $id = (int) ($args['id'] ?? 0);

        /** @var SocialService $service */
        $service = app(SocialService::class);

        $service->deleteReview($user, $id);
        return ['message' => __('messages.review_deleted')];
    }
}
