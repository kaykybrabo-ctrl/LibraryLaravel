<?php
namespace App\Actions;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
class LoginUserAction
{
    public function execute(LoginRequest $request): array
    {
        try {
            $validated = $request->validated();
            $user = User::where('email', $validated['email'])->first();
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                Log::warning('Login attempt failed', ['email' => $validated['email']]);
                throw new \Exception('Invalid credentials');
            }
            $token = JWTAuth::fromUser($user);
            Log::info('User logged in successfully', ['user_id' => $user->id]);
            return [
                'user' => $user,
                'token' => $token,
            ];
        } catch (\Exception $e) {
            Log::error('Login failed', [
                'email' => $request->input('email'),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
