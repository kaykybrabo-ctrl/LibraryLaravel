<?php

namespace App\Policies;

use App\Models\Author;
use App\Models\User;

class AuthorPolicy
{
    public function create(User $user): bool
    {
        return (bool) ($user->is_admin ?? false);
    }

    public function update(User $user, Author $author): bool
    {
        return (bool) ($user->is_admin ?? false);
    }

    public function delete(User $user, Author $author): bool
    {
        return (bool) ($user->is_admin ?? false);
    }
}
