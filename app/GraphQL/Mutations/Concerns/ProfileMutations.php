<?php
namespace App\GraphQL\Mutations\Concerns;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Services\ProfileService;

trait ProfileMutations
{
    public function updateProfile($rootValue, array $args): User
    {
        $user = $this->requireUser();
        $input = $args['input'] ?? [];
        $data = $this->validatedInput($input, new UpdateProfileRequest());

        /** @var ProfileService $service */
        $service = app(ProfileService::class);

        return $service->updateProfile($user, $data);
    }
}
