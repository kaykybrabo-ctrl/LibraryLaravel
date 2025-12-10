<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('logs in an existing user', function () {
    $user = User::firstOrCreate(
        ['email' => 'kaue@gmail.com'],
        ['name' => '', 'password' => Hash::make('123'), 'is_admin' => false]
    );

    $resp = $this->postJson('/api/login', [
        'email' => 'kaue@gmail.com',
        'password' => '123',
    ]);

    $resp->assertStatus(200)
        ->assertJsonStructure(['user' => ['id','name','email'], 'token']);
});

it('registers a new user', function () {
    $email = 'test'.uniqid().'@mail.com';

    $resp = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => $email,
        'password' => 'secret123',
    ]);

    $resp->assertStatus(200)
        ->assertJsonStructure(['user' => ['id','name','email'], 'token']);
});
