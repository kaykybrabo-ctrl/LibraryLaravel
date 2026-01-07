<?php
namespace App\GraphQL\Mutations\Concerns;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;

trait ProfileMutations
{
    public function updateProfile($rootValue, array $args): User
    {
        $user = $this->requireUser();
        $input = $args['input'] ?? [];
        $data = $this->validatedInput($input, new UpdateProfileRequest());
        $user->update([
            'name' => $data['name'],
            'photo' => $data['photo'] ?? $user->photo,
        ]);
        return $user->fresh();
    }
}
