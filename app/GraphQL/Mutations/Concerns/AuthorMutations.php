<?php
namespace App\GraphQL\Mutations\Concerns;

use App\Http\Requests\CreateAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Models\Author;

trait AuthorMutations
{
    public function createAuthor($rootValue, array $args): Author
    {
        $this->requireAdmin();
        $input = $args['input'] ?? [];
        $data = $this->validatedInput($input, new CreateAuthorRequest());
        return $this->authors->create($data);
    }

    public function updateAuthor($rootValue, array $args): Author
    {
        $this->requireAdmin();
        $id = (int) ($args['id'] ?? 0);
        $input = $args['input'] ?? [];
        $data = $this->validatedInput($input, new UpdateAuthorRequest());
        return $this->authors->update($id, $data);
    }

    public function deleteAuthor($rootValue, array $args): array
    {
        $this->requireAdmin();
        $id = (int) ($args['id'] ?? 0);
        $this->authors->delete($id);
        return ['message' => __('messages.author_deleted')];
    }

    public function restoreAuthor($rootValue, array $args): Author
    {
        $this->requireAdmin();
        $id = (int) ($args['id'] ?? 0);
        $author = Author::withTrashed()->findOrFail($id);
        $author->restore();
        return $author->load('books');
    }
}
