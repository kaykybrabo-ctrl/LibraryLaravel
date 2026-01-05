<?php
namespace App\Actions;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Http\Requests\BorrowBookRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class BorrowBookAction
{
    public function execute(BorrowBookRequest $request, User $user): Loan
    {
        try {
            return DB::transaction(function () use ($request, $user) {
                $validated = $request->validated();
                $book = Book::findOrFail($validated['book_id']);
                if ($book->isBorrowed()) {
                    throw new \Exception('This book is already borrowed');
                }
                $existingLoan = Loan::where('user_id', $user->id)
                    ->where('book_id', $book->id)
                    ->whereNull('returned_at')
                    ->first();
                if ($existingLoan) {
                    throw new \Exception('You have already borrowed this book');
                }
                $loan = Loan::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'loan_date' => now(),
                    'return_date' => $validated['return_date'],
                ]);
                Log::info('Book borrowed successfully', [
                    'loan_id' => $loan->id,
                    'user_id' => $user->id,
                    'book_id' => $book->id
                ]);
                return $loan;
            });
        } catch (\Exception $e) {
            Log::error('Failed to borrow book', [
                'user_id' => $user->id,
                'book_id' => $request->input('book_id'),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
