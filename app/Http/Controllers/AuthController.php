<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

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

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'User registered',
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        $hashOk = $user && Hash::check($credentials['password'], $user->password);

        if (! $user || ! $hashOk) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
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
        return JWTAuth::user();
    }

    public function logout(Request $request)
    {
        try {
            if ($token = JWTAuth::getToken()) {
                JWTAuth::invalidate($token);
            }
        } catch (\Throwable $e) {
            
        }

        return response()->json(['message' => 'Logged out']);
    }

    public function jwtLogin(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function jwtMe(Request $request)
    {
        return response()->json($request->user() ?? JWTAuth::user());
    }
}

