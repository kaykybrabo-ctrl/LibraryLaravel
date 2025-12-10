<?php

use App\Models\User;
use App\Models\Author;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

it('admin can create, update and delete books', function () {
    $admin = User::firstOrCreate(
        ['email' => 'kayky@gmail.com'],
        ['name' => 'Kayky', 'password' => Hash::make('123'), 'is_admin' => true]
    );
    $token = JWTAuth::fromUser($admin);
    $headers = ['Authorization' => 'Bearer '.$token];

    $author = Author::firstOrCreate(
        ['name' => 'Guilherme Biondo'],
        ['bio' => 'Bio', 'photo' => null]
    );

    $create = $this->postJson('/api/books', [
        'title' => 'Test Book',
        'description' => 'Desc',
        'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/sample.jpg',
        'author_id' => $author->id,
    ], $headers);
    $create->assertStatus(201)->assertJsonPath('data.title', 'Test Book');

    $bookId = $create->json('data.id');

    $update = $this->putJson('/api/books/'.$bookId, [
        'title' => 'Test Book Updated',
        'description' => 'Desc 2',
        'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/sample2.jpg',
        'author_id' => $author->id,
    ], $headers);
    $update->assertOk()->assertJsonPath('data.title', 'Test Book Updated');

    $delete = $this->deleteJson('/api/books/'.$bookId, [], $headers);
    $delete->assertOk();
});
