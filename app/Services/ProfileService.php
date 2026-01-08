<?php
namespace App\Services;

use App\Models\User;

/**
 * Domain service for profile-related operations.
 */
class ProfileService
{
    /**
     * Update the authenticated user's profile data.
     *
     * @param array<string,mixed> $data
     */
    public function updateProfile(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'],
            'photo' => $data['photo'] ?? $user->photo,
        ]);

        return $user->fresh();
    }
}
