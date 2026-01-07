<?php

namespace App\Exceptions;

use Exception;
use GraphQL\Error\ClientAware;
use GraphQL\Error\ProvidesExtensions;

class NotFoundException extends Exception implements ClientAware, ProvidesExtensions
{
    public function __construct(string $message = '', protected ?string $model = null)
    {
        parent::__construct($message !== '' ? $message : __('errors.not_found'));
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'not_found';
    }

    public function getExtensions(): ?array
    {
        if (!$this->model) {
            return null;
        }

        return [
            'model' => $this->model,
        ];
    }
}
