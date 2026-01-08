<?php
namespace App\GraphQL\Queries\Concerns;

use App\Models\Book;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

trait BookQueries
{
    public function books($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): iterable
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

        return $this->books->page($perPage, $page, $search, $authorId, $sort);
    }

    public function book($rootValue, array $args): Book
    {
        return $this->books->find((int) $args['id']);
    }
}
