<?php
namespace App\Services;
use App\Repositories\Contracts\AuthorRepositoryInterface;
use App\Models\Author;
use Illuminate\Support\Collection;

/**
 * Service layer for author-related operations.
 */
class AuthorService
{
    /**
     * Create a new AuthorService instance.
     */
    public function __construct(private AuthorRepositoryInterface $repo)
    {
    }

    /**
     * Retrieve all authors with their books.
     *
     * @return Collection<int, Author>
     */
    public function listAll(): Collection
    {
        return $this->repo->allWithBooks();
    }

    /**
     * Find a single author with its books by id.
     */
    public function find(int $id): Author
    {
        return $this->repo->findWithBooks($id);
    }

    /**
     * Paginate authors with optional search and sort and return array payload.
     *
     * @return array{data: array<int, Author>, pageInfo: array<string,int|bool>}
     */
    public function page(int $perPage, int $page, string $search, string $sort): array
    {
        $paginator = $this->repo->page($perPage, $page, $search, $sort);

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
     * Create a new author.
     *
     * @param array<string,mixed> $data
     */
    public function create(array $data): Author
    {
        if (empty($data['photo'] ?? null)) {
            $data['photo'] = 'pedbook/profiles/default-user';
        }
        $author = $this->repo->create($data);
        return $author;
    }

    /**
     * Update an existing author.
     *
     * @param array<string,mixed> $data
     */
    public function update(int $id, array $data): Author
    {
        if (array_key_exists('photo', $data) && empty($data['photo'])) {
            $data['photo'] = 'pedbook/profiles/default-user';
        }
        $author = $this->repo->update($id, $data);
        return $author;
    }

    /**
     * Soft delete an author by id.
     */
    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }
}
