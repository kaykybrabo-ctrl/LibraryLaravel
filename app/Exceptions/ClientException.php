<?php

namespace App\Exceptions;

use Exception;
use GraphQL\Error\ClientAware;
use GraphQL\Error\ProvidesExtensions;

class ClientException extends Exception implements ClientAware, ProvidesExtensions
{
    public function __construct(string $message = '', protected ?string $codeKey = null, protected ?array $extensions = null)
    {
        parent::__construct($message);
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'client';
    }

    public function getExtensions(): ?array
    {
        if ($this->extensions === null && $this->codeKey === null) {
            return null;
        }

        return array_filter([
            'code' => $this->codeKey,
            'meta' => $this->extensions,
        ], static fn ($v) => $v !== null);
    }
}
