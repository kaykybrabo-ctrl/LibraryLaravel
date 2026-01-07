<?php
namespace Tests\Feature;
use App\Models\Book;
use App\Models\User;
use App\Models\Author;
use App\Actions\CreateBookAction;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
class BookActionsTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;
    protected Author $author;
    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->author = Author::factory()->create();
    }
    public function test_create_book_action_success()
    {
        $action = new CreateBookAction();
        $validatedData = [
            'title' => 'Test Book',
            'author_id' => $this->author->id,
            'description' => 'Test description',
            'price' => 29.90,
        ];
        $book = $action->execute($validatedData);
        $this->assertInstanceOf(Book::class, $book);
        $this->assertEquals('Test Book', $book->title);
        $this->assertEquals($this->author->id, $book->author_id);
        $this->assertEquals('Test description', $book->description);
        $this->assertEquals(29.90, $book->price);
        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'author_id' => $this->author->id,
        ]);
    }
    public function test_create_book_action_with_new_author()
    {
        $action = new CreateBookAction();
        $validatedData = [
            'title' => 'Test Book with New Author',
            'author_id' => null,
            'new_author_name' => 'New Test Author',
            'description' => 'Test description',
            'price' => 39.90,
        ];
        $book = $action->execute($validatedData);
        $this->assertInstanceOf(Book::class, $book);
        $this->assertEquals('Test Book with New Author', $book->title);
        $this->assertEquals('New Test Author', $book->author->name);
        $this->assertEquals('Test description', $book->description);
        $this->assertEquals(39.90, $book->price);
        $this->assertDatabaseHas('books', [
            'title' => 'Test Book with New Author',
        ]);
        $this->assertDatabaseHas('authors', [
            'name' => 'New Test Author',
        ]);
    }
    public function test_create_book_action_validation_rules()
    {
        $request = new \App\Http\Requests\CreateBookRequest();
        $rules = $request->rules();
        $validator = \Illuminate\Support\Facades\Validator::make([
            'title' => 'Valid Title',
            'author_id' => $this->author->id,
            'description' => 'Valid description',
            'price' => 29.90,
        ], $rules);
        $this->assertFalse($validator->fails());
        $validator = \Illuminate\Support\Facades\Validator::make([
            'title' => '',
            'author_id' => 999,
        ], $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
        $this->assertArrayHasKey('author_id', $validator->errors()->toArray());
    }
    public function test_create_book_action_handles_database_errors()
    {
        app()->setLocale('en');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(__('errors.create_book_failed'));
        $action = new CreateBookAction();
        $invalidData = [
            'title' => 'Test Book',
            'author_id' => 999999,
        ];
        $action->execute($invalidData);
    }
}
