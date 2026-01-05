<?php
namespace App\Exceptions;
use Exception;
class UserException extends Exception
{
    public static function notFound(): self
    {
        return new self('User not found');
    }
    public static function authenticationFailed(): self
    {
        return new self('Authentication failed');
    }
    public static function registrationFailed(string $reason): self
    {
        return new self("Registration failed: {$reason}");
    }
    public static function emailAlreadyExists(): self
    {
        return new self('Email already exists');
    }
    public static function invalidCredentials(): self
    {
        return new self('Invalid credentials');
    }
    public static function accountLocked(): self
    {
        return new self('Account is locked');
    }
    public static function unauthorized(): self
    {
        return new self('Unauthorized access');
    }
}
