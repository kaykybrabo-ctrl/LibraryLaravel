<?php
namespace App\Exceptions;
use Exception;
class AuthorException extends Exception
{
    public static function notFound(): self
    {
        return new self('Author not found');
    }
    public static function hasBooks(): self
    {
        return new self('Author cannot be deleted while having associated books');
    }
    public static function nameAlreadyExists(): self
    {
        return new self('Author name already exists');
    }
    public static function creationFailed(string $reason): self
    {
        return new self("Failed to create author: {$reason}");
    }
    public static function updateFailed(string $reason): self
    {
        return new self("Failed to update author: {$reason}");
    }
}
