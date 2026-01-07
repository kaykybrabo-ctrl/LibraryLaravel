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

        $query = Author::query()->with('books');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('bio', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'recent') {
            $query->orderByDesc('created_at')->orderByDesc('id');
        } else {
            $query->orderBy('name')->orderBy('id');
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

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

    public function author($rootValue, array $args): Author
    {
        return $this->authors->find((int) $args['id']);
    }
}
