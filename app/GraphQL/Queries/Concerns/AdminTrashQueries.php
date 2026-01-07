<?php
namespace App\GraphQL\Queries\Concerns;

use App\Models\Author;
use App\Models\Book;

trait AdminTrashQueries
{
    public function deletedBooks($rootValue, array $args = []): iterable
    {
        $this->requireAdmin();

        $sort = strtolower(trim((string) ($args['sort'] ?? 'recent')));

        $query = Book::onlyTrashed()->with('author');

        if ($sort === 'title') {
            $query->orderBy('title')->orderBy('id');
        } else {
            $query->orderByDesc('deleted_at')->orderByDesc('id');
        }

        return $query->get();
    }

    public function deletedAuthors($rootValue, array $args = []): iterable
    {
        $this->requireAdmin();

        $sort = strtolower(trim((string) ($args['sort'] ?? 'recent')));

        $query = Author::onlyTrashed()->with('books');

        if ($sort === 'name') {
            $query->orderBy('name')->orderBy('id');
        } else {
            $query->orderByDesc('deleted_at')->orderByDesc('id');
        }

        return $query->get();
    }
}
