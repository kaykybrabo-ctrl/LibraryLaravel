<?php
namespace App\GraphQL\Mutations\Concerns;

use App\Exceptions\ClientException;
use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;

trait BookMutations
{
    public function createBook($rootValue, array $args): Book
    {
        $this->requireAdmin();
        $input = $args['input'] ?? $args;
        if (!is_array($input)) {
            $input = (array) $input;
        }

        if (empty($input['author_name'] ?? null) && !empty($input['new_author_name'] ?? null)) {
            $input['author_name'] = $input['new_author_name'];
        }

        $data = $this->validatedInput($input, new CreateBookRequest());
        unset($data['new_author_name']);

        return $this->books->create($data);
    }

    public function updateBook($rootValue, array $args): Book
    {
        $this->requireAdmin();
        $id = (int) ($args['id'] ?? 0);
        $input = $args['input'] ?? [];
        $payload = array_merge($input, ['id' => $id]);
        $data = $this->validatedInput($payload, new UpdateBookRequest());
        unset($data['id']);
        return $this->books->update($id, $data);
    }

    public function deleteBook($rootValue, array $args): array
    {
        $this->requireAdmin();
        $id = (int) ($args['id'] ?? 0);
        $this->books->delete($id);
        return ['message' => __('messages.book_deleted')];
    }

    public function restoreBook($rootValue, array $args): Book
    {
        $this->requireAdmin();
        $id = (int) ($args['id'] ?? 0);
        $book = Book::withTrashed()->findOrFail($id);
        $book->restore();
        return $book->load('author');
    }
}
