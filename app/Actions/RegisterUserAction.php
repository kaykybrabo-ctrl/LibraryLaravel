<?php
namespace App\Actions;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
class RegisterUserAction
{
    public function execute(RegisterRequest $request): User
    {
        try {
            $validated = $request->validated();
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            Log::info('User registered successfully', ['user_id' => $user->id]);
            return $user;
        } catch (\Throwable $e) {
            Log::error('Failed to register user', [
                'email' => $request->input('email'),
                'error' => $e->getMessage()
            ]);
            throw new \RuntimeException(__('errors.register_failed'), 0, $e);
        }
    }
}
