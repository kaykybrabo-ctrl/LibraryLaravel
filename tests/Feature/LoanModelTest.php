<?php
use App\Models\Loan;
use Illuminate\Support\Carbon;
it('calculates days_remaining for future return date', function () {
    Carbon::setTestNow('2025-01-10');
    $loan = Loan::factory()->create([
        'loan_date' => '2025-01-10',
        'return_date' => '2025-01-12',
        'returned_at' => null,
    ]);
    expect($loan->is_overdue)->toBeFalse();
    expect($loan->days_remaining)->toBe(2);
    expect($loan->status)->toBe('active');
});
it('calculates overdue when return date is past', function () {
    Carbon::setTestNow('2025-01-10');
    $loan = Loan::factory()->create([
        'loan_date' => '2025-01-01',
        'return_date' => '2025-01-08',
        'returned_at' => null,
    ]);
    expect($loan->is_overdue)->toBeTrue();
    expect($loan->days_remaining)->toBe(-2);
    expect($loan->status)->toBe('overdue');
});
it('returns null days_remaining and returned status when loan is returned', function () {
    Carbon::setTestNow('2025-01-10');
    $loan = Loan::factory()->create([
        'loan_date' => '2025-01-01',
        'return_date' => '2025-01-08',
        'returned_at' => '2025-01-09',
    ]);
    expect($loan->is_overdue)->toBeFalse();
    expect($loan->days_remaining)->toBeNull();
    expect($loan->status)->toBe('returned');
});
