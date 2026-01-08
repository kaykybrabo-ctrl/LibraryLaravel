<?php
namespace App\Actions;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthenticationException;

/**
 * Handles user authentication and JWT token generation.
 */
class LoginUserAction
{
    /**
     * Attempt to log a user in using the provided credentials.
     *
     * @return array{user: User, token: string}
     */
    public function execute(LoginRequest $request): array
    {
        try {
            $validated = $request->validated();
            $user = User::where('email', $validated['email'])->first();
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                Log::warning('Login attempt failed', ['email' => $validated['email']]);
                throw new AuthenticationException(__('errors.invalid_credentials'));
            }
            $token = JWTAuth::fromUser($user);
            Log::info('User logged in successfully', ['user_id' => $user->id]);
            return [
                'user' => $user,
                'token' => $token,
            ];
        } catch (AuthenticationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Login failed', [
                'email' => $request->input('email'),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
