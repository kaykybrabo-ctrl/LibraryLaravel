<?php
namespace App\GraphQL\Queries\Concerns;

use App\Models\Book;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

trait BookQueries
{
    public function books($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (!empty($args['all']) || empty($args['per_page'])) {
            return $this->books->listAll();
        }
        $perPage = max(1, (int) ($args['per_page'] ?? 10));
        return $this->books->paginate($perPage)->items();
    }

    public function booksPage($rootValue, array $args): array
    {
        $perPage = max(1, (int) ($args['per_page'] ?? 12));
        $page = max(1, (int) ($args['page'] ?? 1));
        $search = trim((string) ($args['search'] ?? ''));
        $authorId = (int) ($args['author_id'] ?? 0);
        $sort = strtolower(trim((string) ($args['sort'] ?? 'recent')));

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

    public function book($rootValue, array $args): Book
    {
        return $this->books->find((int) $args['id']);
    }
}
