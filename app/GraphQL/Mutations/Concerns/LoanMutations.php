<?php
namespace App\GraphQL\Mutations\Concerns;

use App\Http\Requests\LoanRequest;
use App\Models\Loan;
use App\Services\LoanService;

trait LoanMutations
{
    public function rentBook($rootValue, array $args): Loan
    {
        $input = $args['input'] ?? [];
        $user = $this->requireUser();

        $data = $this->validatedInput($input, new LoanRequest());

        /** @var LoanService $service */
        $service = app(LoanService::class);

        return $service->rentBook($user, $data);
    }

    public function returnBook($rootValue, array $args): Loan
    {
        $user = $this->requireUser();
        $id = (int) ($args['id'] ?? 0);

        /** @var LoanService $service */
        $service = app(LoanService::class);

        return $service->returnBook($user, $id);
    }

    public function deleteLoan($rootValue, array $args): array
    {
        $this->requireAdmin();
        $id = (int) ($args['id'] ?? 0);

        /** @var LoanService $service */
        $service = app(LoanService::class);

        $service->deleteLoan($id);
        return ['message' => __('messages.loan_deleted')];
    }
}
