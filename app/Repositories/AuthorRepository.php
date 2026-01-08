<?php
namespace App\Repositories;

use App\Models\Author;
use App\Repositories\Contracts\AuthorRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Eloquent implementation of the AuthorRepositoryInterface.
 */
class AuthorRepository implements AuthorRepositoryInterface
{
    /**
     * Retrieve all authors with their related books.
     *
     * @return Collection<int, Author>
     */
    public function allWithBooks(): Collection
    {
        return Author::query()
            ->with('books')
            ->orderBy('name')
            ->orderBy('id')
            ->get();
    }
    /**
     * Find a single author with its books by id.
     */
    public function findWithBooks(int $id): Author
    {
        return Author::query()->with('books')->findOrFail($id);
    }

    /**
     * Paginate authors with optional search and sort.
     */
    public function page(int $perPage, int $page, string $search, string $sort): LengthAwarePaginator
    {
        $query = Author::query()->with('books');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('bio', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'recent') {
            $query->orderByDesc('created_at')->orderByDesc('id');
        } else {
            $query->orderBy('name')->orderBy('id');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
    /**
     * Create a new author.
     *
     * @param array<string,mixed> $data
     */
    public function create(array $data): Author
    {
        return Author::create($data);
    }
    /**
     * Update an existing author.
     *
     * @param array<string,mixed> $data
     */
    public function update(int $id, array $data): Author
    {
        $author = Author::findOrFail($id);
        $author->update($data);
        return $author;
    }
    /**
     * Soft delete an author by id.
     */
    public function delete(int $id): void
    {
        Author::findOrFail($id)->delete();
    }
}
