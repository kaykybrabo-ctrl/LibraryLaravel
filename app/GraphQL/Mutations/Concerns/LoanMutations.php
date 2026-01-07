<?php
namespace App\GraphQL\Mutations\Concerns;

use App\Exceptions\ClientException;
use App\Http\Requests\LoanRequest;
use App\Jobs\SendBookDueNotification;
use App\Models\Loan;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

trait LoanMutations
{
    public function rentBook($rootValue, array $args): Loan
    {
        $input = $args['input'] ?? [];
        $user = $this->requireUser();
        $isAdmin = (bool) ($user->is_admin ?? false);
        if (Gate::forUser($user)->allows('admin')) {
            throw new AuthorizationException(__('errors.admin_cannot_rent'));
        }
        $data = $this->validatedInput($input, new LoanRequest());
        if ((int) $data['user_id'] !== (int) $user->id) {
            throw new AuthorizationException(__('errors.access_not_allowed'));
        }
        $activeExists = Loan::where('book_id', $data['book_id'])
            ->whereNull('returned_at')
            ->exists();
        if ($activeExists) {
            throw new ClientException(__('errors.book_already_borrowed'), 'book_already_borrowed');
        }
        $loan = Loan::create($data);
        try {
            SendBookDueNotification::dispatch($loan->id);
        } catch (\Throwable $e) {
            Log::warning('Failed to dispatch SendBookDueNotification from rentBook mutation', [
                'loan_id' => (int) $loan->id,
                'user_id' => (int) $user->id,
                'is_admin' => $isAdmin,
                'error' => $e->getMessage(),
            ]);
        }
        return $loan->load(['book.author', 'user']);
    }

    public function returnBook($rootValue, array $args): Loan
    {
        $user = $this->requireUser();
        $isAdmin = (bool) ($user->is_admin ?? false);
        $id = (int) ($args['id'] ?? 0);
        $loan = Loan::findOrFail($id);
        if (!$isAdmin && (int) $loan->user_id !== (int) $user->id) {
            throw new AuthorizationException(__('errors.access_not_allowed'));
        }
        $loan->update(['returned_at' => now()]);
        return $loan->load(['book.author', 'user']);
    }

    public function deleteLoan($rootValue, array $args): array
    {
        $this->requireAdmin();
        $id = (int) ($args['id'] ?? 0);
        Loan::findOrFail($id)->delete();
        return ['message' => __('messages.loan_deleted')];
    }
}
