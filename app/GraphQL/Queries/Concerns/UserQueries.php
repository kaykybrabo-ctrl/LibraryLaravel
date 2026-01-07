<?php
namespace App\GraphQL\Queries\Concerns;

use App\Models\User;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

trait UserQueries
{
    public function users($rootValue, array $args): iterable
    {
        $perPage = max(1, (int) ($args['per_page'] ?? 100));
        return User::orderBy('id')->paginate($perPage)->items();
    }

    public function me($rootValue = null, array $args = [], GraphQLContext $context = null): ?User
    {
        return $this->requireUser();
    }
}
