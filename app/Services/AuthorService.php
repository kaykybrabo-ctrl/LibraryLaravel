<?php

namespace App\Services;

use App\Repositories\AuthorRepository;
use App\Models\Author;

class AuthorService
{
    public function __construct(private AuthorRepository $repo)
    {
    }

    public function listAll()
    {
        return $this->repo->allWithBooks();
    }

    public function find(int $id): Author
    {
        return $this->repo->findWithBooks($id);
    }

    public function create(array $data): Author
    {
        if (empty($data['photo'] ?? null)) {
            $data['photo'] = 'pedbook/profiles/default-user';
        }

        $author = $this->repo->create($data);
        return $author;
    }

    public function update(int $id, array $data): Author
    {
        if (array_key_exists('photo', $data) && empty($data['photo'])) {
            $data['photo'] = 'pedbook/profiles/default-user';
        }

        $author = $this->repo->update($id, $data);
        return $author;
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }
}
