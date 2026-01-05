<?php

use App\Models\Book;
use App\Models\User;

it('allows admin to query deletedBooks and restore a soft-deleted book', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $book = Book::factory()->create();
    $bookId = (int) $book->id;
    $book->delete();

    $queryDeleted = '
        query {
            deletedBooks {
                id
                title
            }
        }
    ';

    $respDeleted = $this->postJson('/graphql', ['query' => $queryDeleted], authHeadersFor($admin));
    $respDeleted->assertStatus(200);
    $respDeleted->assertJsonFragment(['id' => (string) $bookId]);

    $mutationRestore = '
        mutation RestoreBook($id: ID!) {
            restoreBook(id: $id) {
                id
                title
            }
        }
    ';

    $respRestore = $this->postJson(
        '/graphql',
        ['query' => $mutationRestore, 'variables' => ['id' => $bookId]],
        authHeadersFor($admin)
    );

    $respRestore->assertStatus(200);
    $respRestore->assertJsonPath('data.restoreBook.id', (string) $bookId);

    $this->assertDatabaseHas('books', ['id' => $bookId, 'deleted_at' => null]);

    $queryAllBooks = '
        query {
            books(all: true) {
                id
            }
        }
    ';

    $respAll = $this->postJson('/graphql', ['query' => $queryAllBooks]);
    $respAll->assertStatus(200);
    $respAll->assertJsonFragment(['id' => (string) $bookId]);
});

it('forbids non-admin from querying deletedBooks or restoring books', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $book = Book::factory()->create();
    $bookId = (int) $book->id;
    $book->delete();

    $queryDeleted = '
        query {
            deletedBooks {
                id
            }
        }
    ';

    $respDeleted = $this->postJson('/graphql', ['query' => $queryDeleted], authHeadersFor($user));
    $respDeleted->assertStatus(200);
    $this->assertArrayHasKey('errors', $respDeleted->json());

    $mutationRestore = '
        mutation RestoreBook($id: ID!) {
            restoreBook(id: $id) {
                id
            }
        }
    ';

    $respRestore = $this->postJson(
        '/graphql',
        ['query' => $mutationRestore, 'variables' => ['id' => $bookId]],
        authHeadersFor($user)
    );

    $respRestore->assertStatus(200);
    $this->assertArrayHasKey('errors', $respRestore->json());
    $this->assertDatabaseMissing('books', ['id' => $bookId, 'deleted_at' => null]);
});
