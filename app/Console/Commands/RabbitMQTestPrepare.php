<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\CartItem;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Console\Command;

class RabbitMQTestPrepare extends Command
{
    protected $signature = 'rabbitmq:test-prepare';

    protected $description = 'Prepare test data for RabbitMQ demo (loan due tomorrow + cart item).';

    public function handle(): int
    {
        $user = User::where('is_admin', false)->orderBy('id')->first();
        $book = Book::orderBy('id')->first();

        if (!$user || !$book) {
            $this->error('Missing user or book to prepare test data');
            return self::FAILURE;
        }

        $loan = Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => now()->toDateString(),
            'return_date' => now()->addDay()->toDateString(),
            'returned_at' => null,
        ]);

        CartItem::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            ['quantity' => 1]
        );

        $this->info('Prepared test data');
        $this->line('loan_id=' . $loan->id);
        $this->line('user_id=' . $user->id);
        $this->line('book_id=' . $book->id);

        return self::SUCCESS;
    }
}
