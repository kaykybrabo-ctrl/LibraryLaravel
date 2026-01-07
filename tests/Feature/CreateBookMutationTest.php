<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class CreateBookMutationTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeaders(User $user): array
    {
        $token = JWTAuth::fromUser($user);

        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_admin_can_create_book_with_existing_author_using_input(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $author = Author::factory()->create();

        $query = '
            mutation CreateBook($input: CreateBookInput!) {
                createBook(input: $input) {
                    id
                    title
                    author {
                        id
                        name
                    }
                }
            }
        ';

        $variables = [
            'input' => [
                'title' => 'GraphQL Book',
                'author_id' => $author->id,
                'description' => 'Desc',
                'photo' => null,
                'price' => 10.5,
            ],
        ];

        $response = $this->postJson(
            '/graphql',
            ['query' => $query, 'variables' => $variables],
            $this->authHeaders($admin),
        );

        $response->assertStatus(200);
        if ($response->json('errors')) {
            $msg = (string) $response->json('errors.0.message');
            $debug = (string) $response->json('errors.0.extensions.debugMessage');
            $this->fail("GraphQL error: {$msg}\nDebug: {$debug}");
        }
        $response->assertJsonPath('data.createBook.title', 'GraphQL Book');
        $response->assertJsonPath('data.createBook.author.id', (string) $author->id);

        $this->assertDatabaseHas('books', [
            'title' => 'GraphQL Book',
            'author_id' => $author->id,
        ]);
    }

    public function test_admin_can_create_book_with_new_author_using_input(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $query = '
            mutation CreateBook($input: CreateBookInput!) {
                createBook(input: $input) {
                    id
                    title
                    author {
                        id
                        name
                    }
                }
            }
        ';

        $variables = [
            'input' => [
                'title' => 'GraphQL Book New Author',
                'author_name' => 'GraphQL New Author',
                'author_bio' => 'Bio',
                'author_photo' => 'photo-url',
                'description' => null,
                'photo' => null,
                'price' => 0,
            ],
        ];

        $response = $this->postJson(
            '/graphql',
            ['query' => $query, 'variables' => $variables],
            $this->authHeaders($admin),
        );

        $response->assertStatus(200);
        if ($response->json('errors')) {
            $msg = (string) $response->json('errors.0.message');
            $debug = (string) $response->json('errors.0.extensions.debugMessage');
            $this->fail("GraphQL error: {$msg}\nDebug: {$debug}");
        }
        $response->assertJsonPath('data.createBook.title', 'GraphQL Book New Author');
        $response->assertJsonPath('data.createBook.author.name', 'GraphQL New Author');

        $this->assertDatabaseHas('authors', [
            'name' => 'GraphQL New Author',
        ]);

        $this->assertDatabaseHas('books', [
            'title' => 'GraphQL Book New Author',
        ]);
    }

    public function test_non_admin_cannot_create_book(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $author = Author::factory()->create();

        $query = '
            mutation CreateBook($input: CreateBookInput!) {
                createBook(input: $input) {
                    id
                }
            }
        ';

        $variables = [
            'input' => [
                'title' => 'Blocked Book',
                'author_id' => $author->id,
            ],
        ];

        $response = $this->postJson(
            '/graphql',
            ['query' => $query, 'variables' => $variables],
            $this->authHeaders($user),
        );

        $response->assertStatus(200);
        $this->assertArrayHasKey('errors', $response->json());

        $msg = (string) $response->json('errors.0.message');
        $this->assertNotSame('', $msg);
        $this->assertIsString($msg);
        $this->assertTrue(in_array($msg, [__('errors.forbidden'), 'This action is unauthorized.'], true));
    }

    public function test_unauthenticated_user_cannot_create_book(): void
    {
        $author = Author::factory()->create();

        $query = '
            mutation CreateBook($input: CreateBookInput!) {
                createBook(input: $input) {
                    id
                }
            }
        ';

        $variables = [
            'input' => [
                'title' => 'No Auth Book',
                'author_id' => $author->id,
            ],
        ];

        $response = $this->postJson('/graphql', ['query' => $query, 'variables' => $variables]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('errors', $response->json());

        $msg = (string) $response->json('errors.0.message');
        $this->assertNotSame('', $msg);
        $this->assertIsString($msg);
        $this->assertTrue(in_array($msg, [__('errors.unauthenticated'), 'Unauthenticated.'], true));
    }
}
