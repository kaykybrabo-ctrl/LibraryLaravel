<?php
namespace Tests\Feature;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_can_register()
    {
        $query = '
            mutation Register($input: RegisterInput!) {
                register(input: $input) {
                    user {
                        id
                        name
                        email
                    }
                    message
                }
            }
        ';
        $variables = [
            'input' => [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
            ]
        ];
        $response = $this->postJson('/graphql', [
            'query' => $query,
            'variables' => $variables
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['register' => ['user' => ['id', 'name', 'email']]]]);
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
    }
    public function test_user_cannot_register_with_invalid_data()
    {
        $query = '
            mutation Register($input: RegisterInput!) {
                register(input: $input) {
                    message
                }
            }
        ';
        $variables = [
            'input' => [
                'name' => '',
                'email' => 'invalid-email',
                'password' => '123',
                'password_confirmation' => '456'
            ]
        ];
        $response = $this->postJson('/graphql', [
            'query' => $query,
            'variables' => $variables
        ]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('errors', $response->json());
    }
    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);
        $query = '
            mutation Login($input: LoginInput!) {
                login(input: $input) {
                    token
                    user {
                        id
                        name
                        email
                    }
                }
            }
        ';
        $variables = [
            'input' => [
                'email' => $user->email,
                'password' => 'password123'
            ]
        ];
        $response = $this->postJson('/graphql', [
            'query' => $query,
            'variables' => $variables
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['login' => ['token', 'user' => ['id', 'name', 'email']]]]);
    }
    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);
        $query = '
            mutation Login($input: LoginInput!) {
                login(input: $input) {
                    message
                }
            }
        ';
        $variables = [
            'input' => [
                'email' => $user->email,
                'password' => 'wrongpassword'
            ]
        ];
        $response = $this->postJson('/graphql', [
            'query' => $query,
            'variables' => $variables
        ]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('errors', $response->json());
    }
    public function test_unauthenticated_user_cannot_access_protected_queries()
    {
        $query = '
            query {
                me {
                    id
                    name
                    email
                }
            }
        ';
        $response = $this->postJson('/graphql', ['query' => $query]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('errors', $response->json());
    }
}
