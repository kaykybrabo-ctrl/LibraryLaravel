<?php
namespace Database\Factories;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class LoanFactory extends Factory
{
    protected $model = Loan::class;
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', 'now');
        $end = (clone $start)->modify('+7 days');
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'loan_date' => $start->format('Y-m-d'),
            'return_date' => $end->format('Y-m-d'),
            'returned_at' => null,
        ];
    }
}
