<?php
namespace App\Actions;
use App\Models\Book;
use App\Models\Author;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Handles the transactional creation of a new book and, optionally, its author.
 */
class CreateBookAction
{
    /**
     * Execute the book creation flow.
     *
     * @param array<string,mixed> $validatedData
     */
    public function execute(array $validatedData): Book
    {
        try {
            return DB::transaction(function () use ($validatedData) {
                $authorId = $validatedData['author_id'];
                if (isset($validatedData['new_author_name']) && !$authorId) {
                    Log::info('Creating new author', ['name' => $validatedData['new_author_name']]);
                    $author = Author::create([
                        'name' => $validatedData['new_author_name'],
                        'bio' => 'Author created with book',
                        'photo' => 'pedbook/profiles/default-user',
                    ]);
                    $authorId = $author->id;
                    Log::info('New author created', ['author_id' => $authorId]);
                }
                Log::info('Creating book', [
                    'title' => $validatedData['title'],
                    'author_id' => $authorId,
                ]);
                $bookData = [
                    'title' => $validatedData['title'],
                    'author_id' => $authorId,
                    'description' => $validatedData['description'] ?? 'Book description',
                    'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/default-book',
                    'price' => $validatedData['price'] ?? 0.00,
                ];
                $book = Book::create($bookData);
                Log::info('Book created successfully', ['book_id' => $book->id]);
                return $book;
            });
        } catch (\Throwable $e) {
            Log::error('Failed to create book', [
                'title' => $validatedData['title'] ?? 'unknown',
                'author_id' => $validatedData['author_id'] ?? 'none',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException(__('errors.create_book_failed'), 0, $e);
        }
    }
}
