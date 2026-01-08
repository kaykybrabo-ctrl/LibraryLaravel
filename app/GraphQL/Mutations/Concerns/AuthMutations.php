<?php
namespace App\GraphQL\Mutations\Concerns;

use App\Exceptions\ClientException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RequestPasswordResetRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\AuthService;

trait AuthMutations
{
    public function register($rootValue, array $args): array
    {
        $input = $args['input'] ?? [];
        $data = $this->validatedInput($input, new RegisterRequest());

        /** @var AuthService $service */
        $service = app(AuthService::class);

        $user = $service->register($data);
        return [
            'user' => $user,
            'message' => __('messages.account_created_login'),
        ];
    }

    public function login($rootValue, array $args): array
    {
        $input = $args['input'] ?? [];
        $data = $this->validatedInput($input, new LoginRequest());

        /** @var AuthService $service */
        $service = app(AuthService::class);

        return $service->login($data);
    }

    public function logout(): array
    {
        /** @var AuthService $service */
        $service = app(AuthService::class);

        return $service->logout();
    }

    public function requestPasswordReset($rootValue, array $args): array
    {
        $validated = $this->validatedInput($args, new RequestPasswordResetRequest());
        $email = (string) $validated['email'];

        /** @var AuthService $service */
        $service = app(AuthService::class);

        return $service->requestPasswordReset($email);
    }

    public function resetPassword($rootValue, array $args): array
    {
        $input = $args['input'] ?? [];
        $data = $this->validatedInput($input, new ResetPasswordRequest());

        /** @var AuthService $service */
        $service = app(AuthService::class);

        return $service->resetPassword($data);
    }
}
