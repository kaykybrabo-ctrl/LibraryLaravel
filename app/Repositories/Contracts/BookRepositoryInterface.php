<?php

namespace App\Repositories\Contracts;

use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Contract for book persistence operations.
 */
interface BookRepositoryInterface
{
    /**
     * Retrieve all books with their authors.
     *
     * @return Collection<int, Book>
     */
    public function allWithAuthor(): Collection;

    /**
     * Paginate books including the author relation.
     */
    public function paginateWithAuthor(int $perPage): LengthAwarePaginator;

    /**
     * Find a single book with its author by id.
     */
    public function findWithAuthor(int $id): Book;

    /**
     * Paginate books with optional search, author filter and sort.
     */
    public function page(int $perPage, int $page, string $search, int $authorId, string $sort): LengthAwarePaginator;

    /**
     * Create a new book.
     *
     * @param array<string,mixed> $data
     */
    public function create(array $data): Book;

    /**
     * Update an existing book.
     *
     * @param array<string,mixed> $data
     */
    public function update(int $id, array $data): Book;

    /**
     * Delete a book by id.
     */
    public function delete(int $id): void;
}
