<?php
namespace Tests\Feature;
use App\Models\Book;
use App\Models\User;
use App\Models\Author;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
class BookPolicyTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;
    protected User $regularUser;
    protected Book $book;
    protected Author $author;
    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);
        $this->author = Author::factory()->create();
        $this->book = Book::factory()->create(['author_id' => $this->author->id]);
    }
    public function test_admin_can_create_books()
    {
        $this->assertTrue($this->admin->can('create', Book::class));
    }
    public function test_regular_user_cannot_create_books()
    {
        $this->assertFalse($this->regularUser->can('create', Book::class));
    }
    public function test_admin_can_update_books()
    {
        $this->assertTrue($this->admin->can('update', $this->book));
    }
    public function test_regular_user_cannot_update_books()
    {
        $this->assertFalse($this->regularUser->can('update', $this->book));
    }
    public function test_admin_can_delete_books()
    {
        $this->assertTrue($this->admin->can('delete', $this->book));
    }
    public function test_regular_user_cannot_delete_books()
    {
        $this->assertFalse($this->regularUser->can('delete', $this->book));
    }
    public function test_any_user_can_view_books()
    {
        $this->assertTrue($this->admin->can('view', $this->book));
        $this->assertTrue($this->regularUser->can('view', $this->book));
    }
    public function test_any_user_can_view_any_books()
    {
        $this->assertTrue($this->admin->can('viewAny', Book::class));
        $this->assertTrue($this->regularUser->can('viewAny', Book::class));
    }
    public function test_any_user_can_borrow_books()
    {
        $this->assertTrue($this->admin->can('borrow', $this->book));
        $this->assertTrue($this->regularUser->can('borrow', $this->book));
    }
    public function test_user_can_only_return_their_borrowed_books()
    {
        $this->book->loans()->create([
            'user_id' => $this->regularUser->id,
            'loan_date' => now(),
            'return_date' => now()->addDays(7),
        ]);
        $this->assertTrue($this->regularUser->can('return', $this->book));
        $this->assertFalse($this->admin->can('return', $this->book));
    }
    public function test_user_cannot_return_unborrowed_books()
    {
        $this->assertFalse($this->regularUser->can('return', $this->book));
    }
    public function test_any_user_can_add_books_to_favorites()
    {
        $this->assertTrue($this->admin->can('addToFavorites', $this->book));
        $this->assertTrue($this->regularUser->can('addToFavorites', $this->book));
    }
    public function test_user_can_only_remove_their_favorite_books()
    {
        \App\Models\Favorite::create([
            'user_id' => $this->regularUser->id,
            'book_id' => $this->book->id,
        ]);
        $this->assertTrue($this->regularUser->can('removeFromFavorites', $this->book));
        $this->assertFalse($this->admin->can('removeFromFavorites', $this->book));
    }
    public function test_any_user_can_add_books_to_cart()
    {
        $this->assertTrue($this->admin->can('addToCart', $this->book));
        $this->assertTrue($this->regularUser->can('addToCart', $this->book));
    }
}
