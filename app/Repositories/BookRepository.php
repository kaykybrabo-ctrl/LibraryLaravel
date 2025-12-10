<?php

namespace App\Repositories;

use App\Models\Book;

class BookRepository
{
    public function allWithAuthor()
    {
        return Book::with('author')->get();
    }

    public function paginateWithAuthor(int $perPage)
    {
        return Book::with('author')->paginate($perPage);
    }

    public function findWithAuthor(int $id)
    {
        return Book::with('author')->findOrFail($id);
    }

    public function create(array $data): Book
    {
        return Book::create($data);
    }

    public function update(int $id, array $data): Book
    {
        $book = Book::findOrFail($id);
        $book->update($data);
        return $book;
    }

    public function delete(int $id): void
    {
        Book::findOrFail($id)->delete();
    }
}
