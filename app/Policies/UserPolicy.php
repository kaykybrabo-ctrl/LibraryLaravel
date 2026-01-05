<?php
namespace App\Policies;
use App\Models\User;
class UserPolicy
{
    public function update(User $auth, User $user): bool
    {
        return (bool) ($auth->is_admin ?? false) || $auth->id === $user->id;
    }
}
