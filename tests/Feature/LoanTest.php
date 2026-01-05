<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
class LoanTest extends TestCase
{
    use RefreshDatabase;
    protected User $user;
    protected User $admin;
    protected Book $book;

    protected function authHeaders(User $user): array
    {
        $token = JWTAuth::fromUser($user);
        return ['Authorization' => 'Bearer ' . $token];
    }
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin' => false]);
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->book = Book::factory()->create();
    }
    public function test_user_can_borrow_book()
    {
        $query = '
            mutation RentBook($input: RentBookInput!) {
                rentBook(input: $input) {
                    id
                    loan_date
                    return_date
                    book {
                        id
                        title
                    }
                    user {
                        id
                        name
                    }
                }
            }
        ';
        $variables = [
            'input' => [
                'user_id' => $this->user->id,
                'book_id' => $this->book->id,
                'loan_date' => now()->toDateString(),
                'return_date' => now()->addDays(7)->toDateString(),
            ],
        ];
        $response = $this->postJson('/graphql', [
                'query' => $query,
                'variables' => $variables
            ], $this->authHeaders($this->user));
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['rentBook' => ['id']]]);
        $this->assertDatabaseHas('loans', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
        ]);
    }
    public function test_user_cannot_borrow_same_book_twice()
    {
        Loan::factory()->create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'returned_at' => null
        ]);
        $query = '
            mutation RentBook($input: RentBookInput!) {
                rentBook(input: $input) {
                    id
                }
            }
        ';
        $variables = [
            'input' => [
                'user_id' => $this->user->id,
                'book_id' => $this->book->id,
                'loan_date' => now()->toDateString(),
                'return_date' => now()->addDays(7)->toDateString(),
            ],
        ];
        $response = $this->postJson('/graphql', [
                'query' => $query,
                'variables' => $variables
            ], $this->authHeaders($this->user));
        $response->assertStatus(200);
        $this->assertArrayHasKey('errors', $response->json());
        $debug = (string) $response->json('errors.0.extensions.debugMessage');
        if ($debug !== '') {
            $this->assertStringContainsString('Livro já está alugado', $debug);
        }
    }
    public function test_user_cannot_borrow_already_borrowed_book()
    {
        Loan::factory()->create([
            'book_id' => $this->book->id,
            'returned_at' => null
        ]);
        $query = '
            mutation RentBook($input: RentBookInput!) {
                rentBook(input: $input) {
                    id
                }
            }
        ';
        $variables = [
            'input' => [
                'user_id' => $this->user->id,
                'book_id' => $this->book->id,
                'loan_date' => now()->toDateString(),
                'return_date' => now()->addDays(7)->toDateString(),
            ],
        ];
        $response = $this->postJson('/graphql', [
                'query' => $query,
                'variables' => $variables
            ], $this->authHeaders($this->user));
        $response->assertStatus(200);
        $this->assertArrayHasKey('errors', $response->json());
        $debug = (string) $response->json('errors.0.extensions.debugMessage');
        if ($debug !== '') {
            $this->assertStringContainsString('Livro já está alugado', $debug);
        }
    }
    public function test_user_can_view_own_loans()
    {
        Loan::factory()->count(3)->create(['user_id' => $this->user->id]);
        Loan::factory()->count(2)->create();
        $query = '
            query UserLoans($userId: ID!) {
                userLoans(user_id: $userId) {
                    id
                    loan_date
                    return_date
                    book {
                        id
                        title
                    }
                }
            }
        ';
        $response = $this->postJson('/graphql', [
            'query' => $query,
            'variables' => ['userId' => $this->user->id],
        ], $this->authHeaders($this->user));
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data.userLoans');
    }
    public function test_admin_can_view_all_loans()
    {
        Loan::factory()->count(5)->create();
        $query = '
            query {
                loans {
                    id
                    loan_date
                    return_date
                    book {
                        id
                        title
                    }
                    user {
                        id
                        name
                    }
                }
            }
        ';
        $response = $this->postJson('/graphql', ['query' => $query], $this->authHeaders($this->admin));
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data.loans');
    }
    public function test_unauthenticated_user_cannot_access_loans()
    {
        $query = '
            query {
                loans {
                    id
                }
            }
        ';
        $response = $this->postJson('/graphql', ['query' => $query]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('errors', $response->json());
        $msg = (string) $response->json('errors.0.message');
        $this->assertNotSame('', $msg);
    }
}
