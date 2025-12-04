<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => false,
        ]);

        $token = $user->createToken('app')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token, 'message' => 'User registered']);
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('app')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function getUsers(Request $request)
    {
        $perPage = max(1, (int) $request->query('per_page', 10));
        return User::orderBy('id')->paginate($perPage);
    }

    public function updateProfile(UpdateProfileRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $validated = $request->validated();

        $user->update($validated);
        return $user;
    }

    public function me(Request $request)
    {
        return $request->user();
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        if ($token) {
            $token->delete();
        }
        return response()->json(['message' => 'Logged out']);
    }
}

