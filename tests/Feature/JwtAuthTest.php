<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('authenticates using JWT and accesses protected route', function () {
    $user = User::factory()->create([
        'email' => 'jwt@example.com',
        'password' => Hash::make('123'),
    ]);

    $login = $this->postJson('/api/jwt/login', [
        'email' => 'jwt@example.com',
        'password' => '123',
    ]);

    $login->assertOk()->assertJsonStructure(['token', 'user']);

    $token = $login->json('token');

    $me = $this->getJson('/api/jwt/me', [
        'Authorization' => 'Bearer '.$token,
    ]);

    $me->assertOk()->assertJsonPath('email', 'jwt@example.com');
});
