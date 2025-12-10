<?php

namespace App\Services;

use App\Repositories\BookRepository;
use App\Models\Book;
use App\Services\AuthorService;

class BookService
{
    public function __construct(private BookRepository $repo, private AuthorService $authors)
    {
    }

    public function listAll()
    {
        return $this->repo->allWithAuthor();
    }

    public function paginate(int $perPage)
    {
        return $this->repo->paginateWithAuthor($perPage);
    }

    public function find(int $id): Book
    {
        return $this->repo->findWithAuthor($id);
    }

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

        if (empty($data['photo'] ?? null)) {
            $data['photo'] = 'pedbook/books/default-book';
        }

        unset($data['author_name'], $data['author_bio'], $data['author_photo']);

        $book = $this->repo->create($data);
        return $book;
    }

    public function update(int $id, array $data): Book
    {
        $book = $this->repo->update($id, $data);
        return $book;
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }
}

