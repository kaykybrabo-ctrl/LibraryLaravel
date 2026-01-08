<?php

namespace App\Repositories\Contracts;

use App\Models\Author;
use Illuminate\Support\Collection;

/**
 * Contract for author persistence operations.
 */
interface AuthorRepositoryInterface
{
    /**
     * Retrieve all authors with their books.
     *
     * @return Collection<int, Author>
     */
    public function allWithBooks(): Collection;

    /**
     * Find a single author with its books by id.
     */
    public function findWithBooks(int $id): Author;

    /**
     * Create a new author.
     *
     * @param array<string,mixed> $data
     */
    public function create(array $data): Author;

    /**
     * Update an existing author.
     *
     * @param array<string,mixed> $data
     */
    public function update(int $id, array $data): Author;

    /**
     * Delete an author by id.
     */
    public function delete(int $id): void;

    /**
     * Paginate authors with optional search and sort.
     */
    public function page(int $perPage, int $page, string $search, string $sort): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}
