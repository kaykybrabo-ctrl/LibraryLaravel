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
    public function borrow(User $user, Book $book): bool
    {
        return true;
    }
    public function return(User $user, Book $book): bool
    {
        return $book->loans()
            ->where('user_id', $user->id)
            ->whereNull('returned_at')
            ->exists();
    }
    public function addToFavorites(User $user, Book $book): bool
    {
        return true;
    }
    public function removeFromFavorites(User $user, Book $book): bool
    {
        return $user->favorites()
            ->where('book_id', $book->id)
            ->exists();
    }
    public function addToCart(User $user, Book $book): bool
    {
        return true;
    }
    public function view(User $user, Book $book): bool
    {
        return true;
    }
    public function viewAny(User $user): bool
    {
        return true;
    }
}
