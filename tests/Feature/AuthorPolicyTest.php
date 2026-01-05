<?php
namespace Tests\Feature;
use App\Models\Author;
use App\Models\User;
use App\Models\Book;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
class AuthorPolicyTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;
    protected User $regularUser;
    protected Author $authorWithBooks;
    protected Author $authorWithoutBooks;
    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);
        $this->authorWithBooks = Author::factory()->create();
        Book::factory()->create(['author_id' => $this->authorWithBooks->id]);
        $this->authorWithoutBooks = Author::factory()->create();
    }
    public function test_admin_can_create_authors()
    {
        $this->assertTrue($this->admin->can('create', Author::class));
    }
    public function test_regular_user_cannot_create_authors()
    {
        $this->assertFalse($this->regularUser->can('create', Author::class));
    }
    public function test_admin_can_update_authors()
    {
        $this->assertTrue($this->admin->can('update', $this->authorWithBooks));
        $this->assertTrue($this->admin->can('update', $this->authorWithoutBooks));
    }
    public function test_regular_user_cannot_update_authors()
    {
        $this->assertFalse($this->regularUser->can('update', $this->authorWithBooks));
        $this->assertFalse($this->regularUser->can('update', $this->authorWithoutBooks));
    }
    public function test_admin_can_delete_author_without_books()
    {
        $this->assertTrue($this->admin->can('delete', $this->authorWithoutBooks));
    }
    public function test_admin_cannot_delete_author_with_books()
    {
        $this->assertFalse($this->admin->can('delete', $this->authorWithBooks));
    }
    public function test_regular_user_cannot_delete_authors()
    {
        $this->assertFalse($this->regularUser->can('delete', $this->authorWithBooks));
        $this->assertFalse($this->regularUser->can('delete', $this->authorWithoutBooks));
    }
    public function test_any_user_can_view_authors()
    {
        $this->assertTrue($this->admin->can('view', $this->authorWithBooks));
        $this->assertTrue($this->regularUser->can('view', $this->authorWithBooks));
        $this->assertTrue($this->admin->can('view', $this->authorWithoutBooks));
        $this->assertTrue($this->regularUser->can('view', $this->authorWithoutBooks));
    }
    public function test_any_user_can_view_any_authors()
    {
        $this->assertTrue($this->admin->can('viewAny', Author::class));
        $this->assertTrue($this->regularUser->can('viewAny', Author::class));
    }
}
