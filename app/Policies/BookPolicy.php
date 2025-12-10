<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function create(User $user): bool
    {
        return (bool) ($user->is_admin ?? false);
    }

    public function update(User $user, Book $book): bool
    {
        return (bool) ($user->is_admin ?? false);
    }

    public function delete(User $user, Book $book): bool
    {
        return (bool) ($user->is_admin ?? false);
    }
}
