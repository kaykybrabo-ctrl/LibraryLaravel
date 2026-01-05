<?php
namespace App\Exceptions;
use Exception;
class LoanException extends Exception
{
    public static function notFound(): self
    {
        return new self('Loan not found');
    }
    public static function alreadyReturned(): self
    {
        return new self('Loan has already been returned');
    }
    public static function overdue(): self
    {
        return new self('Loan is overdue and cannot be renewed');
    }
    public static function maxLoansExceeded(): self
    {
        return new self('Maximum number of loans exceeded');
    }
    public static function invalidReturnDate(): self
    {
        return new self('Invalid return date');
    }
    public static function creationFailed(string $reason): self
    {
        return new self("Failed to create loan: {$reason}");
    }
}
