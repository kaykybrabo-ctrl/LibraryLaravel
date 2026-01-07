<?php
namespace App\GraphQL\Queries\Concerns;

use App\Models\Loan;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

trait LoanQueries
{
    public function loans(): iterable
    {
        $this->requireAdmin();
        return Loan::with(['user', 'book.author'])
            ->orderByDesc('loan_date')
            ->orderByDesc('id')
            ->get();
    }

    public function userLoans($rootValue, array $args): iterable
    {
        $auth = $this->requireUser();
        $userId = (int) $args['user_id'];
        if (!Gate::forUser($auth)->allows('admin') && (int) $auth->id !== $userId) {
            throw new AuthorizationException(__('errors.forbidden'));
        }
        return Loan::where('user_id', $userId)
            ->with('book.author')
            ->orderByDesc('loan_date')
            ->orderByDesc('id')
            ->get();
    }

    public function activeBookIds(): array
    {
        return Loan::whereNull('returned_at')
            ->pluck('book_id')
            ->unique()
            ->values()
            ->all();
    }
}
