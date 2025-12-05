<?php

namespace App\Services;

use App\Repositories\BookRepository;
use App\Models\Book;

class BookService
{
    public function __construct(private BookRepository $repo)
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

