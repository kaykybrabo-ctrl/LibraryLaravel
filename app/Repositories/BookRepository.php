<?php
namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Eloquent implementation of the BookRepositoryInterface.
 */
class BookRepository implements BookRepositoryInterface
{
    /**
     * Retrieve all books with their related author.
     *
     * @return Collection<int, Book>
     */
    public function allWithAuthor(): Collection
    {
        return Book::query()
            ->with('author')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
    }
    /**
     * Paginate books with eager-loaded author.
     */
    public function paginateWithAuthor(int $perPage): LengthAwarePaginator
    {
        return Book::query()
            ->with('author')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }
    /**
     * Find a single book with its author by id.
     */
    public function findWithAuthor(int $id): Book
    {
        return Book::query()->with('author')->findOrFail($id);
    }

    /**
     * Paginate books with optional search, author filter and sort.
     */
    public function page(int $perPage, int $page, string $search, int $authorId, string $sort): LengthAwarePaginator
    {
        $query = Book::query()->with('author');

        if ($authorId > 0) {
            $query->where('author_id', $authorId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('author', function ($aq) use ($search) {
                        $aq->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($sort === 'title') {
            $query->orderBy('title')->orderBy('id');
        } else {
            $query->orderByDesc('created_at')->orderByDesc('id');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
    /**
     * Create a new book.
     *
     * @param array<string,mixed> $data
     */
    public function create(array $data): Book
    {
        return Book::create($data);
    }
    /**
     * Update an existing book.
     *
     * @param array<string,mixed> $data
     */
    public function update(int $id, array $data): Book
    {
        $book = Book::findOrFail($id);
        $book->update($data);
        return $book;
    }
    /**
     * Soft delete a book by id.
     */
    public function delete(int $id): void
    {
        Book::findOrFail($id)->delete();
    }
}
