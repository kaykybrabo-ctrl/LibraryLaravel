<?php
namespace App\GraphQL\Queries\Concerns;

use App\Models\Author;

trait AuthorQueries
{
    public function authors(): iterable
    {
        return $this->authors->listAll();
    }

    public function authorsPage($rootValue, array $args): array
    {
        $perPage = max(1, (int) ($args['per_page'] ?? 12));
        $page = max(1, (int) ($args['page'] ?? 1));
        $search = trim((string) ($args['search'] ?? ''));
        $sort = strtolower(trim((string) ($args['sort'] ?? 'name')));

        return $this->authors->page($perPage, $page, $search, $sort);
    }

    public function author($rootValue, array $args): Author
    {
        return $this->authors->find((int) $args['id']);
    }
}
