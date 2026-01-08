<?php
namespace App\Services;
use App\Repositories\Contracts\BookRepositoryInterface;
use App\Models\Book;
use App\Services\AuthorService;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service layer for book-related operations.
 */
class BookService
{
    /**
     * Create a new BookService instance.
     */
    public function __construct(private BookRepositoryInterface $repo, private AuthorService $authors)
    {
    }

    /**
     * Retrieve all books with their authors.
     *
     * @return Collection<int, Book>
     */
    public function listAll(): Collection
    {
        return $this->repo->allWithAuthor();
    }

    /**
     * Paginate books with author relation.
     */
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->repo->paginateWithAuthor($perPage);
    }

    /**
     * Find a single book with its author by id.
     */
    public function find(int $id): Book
    {
        return $this->repo->findWithAuthor($id);
    }

    /**
     * Paginate books with optional filters and return array payload.
     *
     * @return array{data: array<int, Book>, pageInfo: array<string,int|bool>}
     */
    public function page(int $perPage, int $page, string $search, int $authorId, string $sort): array
    {
        $paginator = $this->repo->page($perPage, $page, $search, $authorId, $sort);

        return [
            'data' => $paginator->items(),
            'pageInfo' => [
                'total' => $paginator->total(),
                'perPage' => $paginator->perPage(),
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'hasMorePages' => $paginator->hasMorePages(),
                'count' => $paginator->count(),
            ],
        ];
    }

    /**
     * Create a new book, optionally creating its author.
     *
     * @param array<string,mixed> $data
     */
    public function create(array $data): Book
    {
        if (empty($data['author_id']) && !empty($data['author_name'])) {
            $author = $this->authors->create([
                'name' => $data['author_name'],
                'bio' => $data['author_bio'] ?? '',
                'photo' => $data['author_photo'] ?? null,
            ]);
            $data['author_id'] = $author->id;
        }
        if (empty($data['description'] ?? null)) {
            $data['description'] = config('books.default_description');
        }
        if (empty($data['photo'] ?? null)) {
            $data['photo'] = config('books.default_photo');
        }
        unset($data['author_name'], $data['author_bio'], $data['author_photo']);
        $book = $this->repo->create($data);
        return $book;
    }

    /**
     * Update an existing book.
     *
     * @param array<string,mixed> $data
     */
    public function update(int $id, array $data): Book
    {
        $book = $this->repo->update($id, $data);
        return $book;
    }

    /**
     * Soft delete a book by id.
     */
    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }
}
