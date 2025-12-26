<?php

use App\GraphQL\Mutations\LibraryMutation;
use App\Jobs\SendBookDueNotification;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;

it('creates a loan and dispatches notification job when renting a book', function () {
    Bus::fake();

    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $book = Book::factory()->create();

    Auth::guard('api')->setUser($user);

    $mutation = app(LibraryMutation::class);

    $loan = $mutation->rentBook(null, [
        'input' => [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => now()->toDateString(),
            'return_date' => now()->addDays(7)->toDateString(),
        ],
    ]);

    expect($loan)->toBeInstanceOf(Loan::class);
    expect($loan->user_id)->toBe($user->id);
    expect($loan->book_id)->toBe($book->id);

    Bus::assertDispatched(SendBookDueNotification::class, function ($job) use ($loan) {
        return $job->loanId === $loan->id;
    });
});
