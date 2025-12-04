<?php

namespace App\Repositories;

use App\Models\Author;

class AuthorRepository
{
    public function allWithBooks()
    {
        return Author::with('books')->get();
    }

    public function findWithBooks(int $id)
    {
        return Author::with('books')->findOrFail($id);
    }

    public function create(array $data): Author
    {
        return Author::create($data);
    }

    public function update(int $id, array $data): Author
    {
        $author = Author::findOrFail($id);
        $author->update($data);
        return $author;
    }

    public function delete(int $id): void
    {
        Author::findOrFail($id)->delete();
    }
}
