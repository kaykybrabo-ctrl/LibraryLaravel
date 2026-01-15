<?php

namespace App\Console\Commands;

use App\Jobs\SendBookDueNotification;
use App\Models\Loan;
use Illuminate\Console\Command;

class SendLoanDueNow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan:send-due-now {loanId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispara imediatamente o e-mail de empréstimo próximo do vencimento para o empréstimo informado.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $loanId = (int) $this->argument('loanId');

        $loan = Loan::with(['user'])->find($loanId);

        if (!$loan) {
            $this->error("Empréstimo {$loanId} não encontrado.");
            return self::FAILURE;
        }

        if (!$loan->user || empty($loan->user->email)) {
            $this->error('Usuário do empréstimo não possui e-mail válido.');
            return self::FAILURE;
        }

        SendBookDueNotification::dispatch($loanId);

        $this->info("Job SendBookDueNotification dispatchado para o empréstimo {$loanId}.");
        $this->info('Confira o Horizon (Recent Jobs) e seu e-mail em alguns segundos.');

        return self::SUCCESS;
    }
}
