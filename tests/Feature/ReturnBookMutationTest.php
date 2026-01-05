<?php

use App\Models\Loan;
use App\Models\User;

it('allows loan owner to return their own loan', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $loan = Loan::factory()->create([
        'user_id' => $user->id,
        'returned_at' => null,
    ]);

    $mutation = '
        mutation ReturnBook($id: ID!) {
            returnBook(id: $id) {
                id
                returned_at
                user { id }
                book { id }
            }
        }
    ';

    $resp = $this->postJson(
        '/graphql',
        ['query' => $mutation, 'variables' => ['id' => (int) $loan->id]],
        authHeadersFor($user)
    );

    $resp->assertStatus(200);
    $resp->assertJsonPath('data.returnBook.id', (string) $loan->id);

    $this->assertDatabaseHas('loans', ['id' => (int) $loan->id]);
});

it('prevents a different non-admin user from returning someone else loan', function () {
    $owner = User::factory()->create(['is_admin' => false]);
    $other = User::factory()->create(['is_admin' => false]);

    $loan = Loan::factory()->create([
        'user_id' => $owner->id,
        'returned_at' => null,
    ]);

    $mutation = '
        mutation ReturnBook($id: ID!) {
            returnBook(id: $id) {
                id
            }
        }
    ';

    $resp = $this->postJson(
        '/graphql',
        ['query' => $mutation, 'variables' => ['id' => (int) $loan->id]],
        authHeadersFor($other)
    );

    $resp->assertStatus(200);
    $this->assertArrayHasKey('errors', $resp->json());
});

it('allows admin to return any loan', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $owner = User::factory()->create(['is_admin' => false]);

    $loan = Loan::factory()->create([
        'user_id' => $owner->id,
        'returned_at' => null,
    ]);

    $mutation = '
        mutation ReturnBook($id: ID!) {
            returnBook(id: $id) {
                id
                returned_at
            }
        }
    ';

    $resp = $this->postJson(
        '/graphql',
        ['query' => $mutation, 'variables' => ['id' => (int) $loan->id]],
        authHeadersFor($admin)
    );

    $resp->assertStatus(200);
    $resp->assertJsonPath('data.returnBook.id', (string) $loan->id);
});
