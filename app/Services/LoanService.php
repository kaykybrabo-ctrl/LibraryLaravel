<?php
namespace App\Services;

use App\Exceptions\ClientException;
use App\Http\Requests\LoanRequest;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Jobs\SendBookDueNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Domain service for loan operations.
 */
class LoanService
{
    /**
     * Rent a book for the given user using validated LoanRequest data.
     *
     * @param array<string,mixed> $data
     */
    public function rentBook(User $user, array $data): Loan
    {
        $isAdmin = (bool) ($user->is_admin ?? false);
        if ($isAdmin) {
            throw new AuthorizationException(__('errors.admin_cannot_rent'));
        }

        if ((int) $data['user_id'] !== (int) $user->id) {
            throw new AuthorizationException(__('errors.access_not_allowed'));
        }

        /** @var Loan $loan */
        $loan = DB::transaction(function () use ($data, $user) {
            $book = Book::findOrFail((int) $data['book_id']);

            if ($book->isBorrowed()) {
                throw new ClientException(__('errors.book_already_borrowed'), 'book_already_borrowed');
            }

            $loan = Loan::create($data);

            try {
                SendBookDueNotification::dispatch($loan->id);
            } catch (\Throwable $e) {
                Log::warning('Failed to dispatch SendBookDueNotification from LoanService::rentBook', [
                    'loan_id' => (int) $loan->id,
                    'user_id' => (int) $user->id,
                    'is_admin' => $isAdmin = (bool) ($user->is_admin ?? false),
                    'error' => $e->getMessage(),
                ]);
            }

            return $loan;
        });

        return $loan->load(['book.author', 'user']);
    }

    public function returnBook(User $user, int $loanId): Loan
    {
        $isAdmin = (bool) ($user->is_admin ?? false);

        /** @var Loan $loan */
        $loan = Loan::findOrFail($loanId);

        if (!$isAdmin && (int) $loan->user_id !== (int) $user->id) {
            throw new AuthorizationException(__('errors.access_not_allowed'));
        }

        $loan->update(['returned_at' => now()]);

        return $loan->load(['book.author', 'user']);
    }

    public function deleteLoan(int $loanId): void
    {
        Loan::findOrFail($loanId)->delete();
    }
}
