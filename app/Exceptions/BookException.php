<?php
namespace App\Exceptions;
use Exception;
class BookException extends Exception
{
    public static function notFound(): self
    {
        return new self('Book not found');
    }
    public static function alreadyBorrowed(): self
    {
        return new self('Book is already borrowed');
    }
    public static function cannotBeDeleted(): self
    {
        return new self('Book cannot be deleted due to existing loans');
    }
    public static function invalidPrice(): self
    {
        return new self('Invalid price value');
    }
    public static function creationFailed(string $reason): self
    {
        return new self("Failed to create book: {$reason}");
    }
}
