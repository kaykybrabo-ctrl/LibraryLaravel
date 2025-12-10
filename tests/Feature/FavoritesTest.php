<?php

use App\Models\User;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

it('sets, reads and clears favorite book for a user', function () {
    $user = User::factory()->create([
        'email' => 'kaue+jwt-fav@example.com',
        'password' => Hash::make('123'),
        'is_admin' => false,
    ]);
    $token = JWTAuth::fromUser($user);
    $headers = ['Authorization' => 'Bearer '.$token];

    $author = Author::factory()->create([
        'name' => 'Fav Author',
        'bio' => 'Bio',
        'photo' => null,
    ]);

    $bookModel = Book::factory()
        ->for($author)
        ->create([
            'title' => 'Fav Book',
            'description' => 'Desc',
            'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/sample.jpg',
        ]);

    $payload = [
        'user_id' => $user->id,
        'book_id' => $bookModel->id,
    ];

    $set = $this->postJson('/api/favorites', $payload, $headers);
    $set->assertOk()->assertJsonPath('id', $bookModel->id);

    $get = $this->getJson("/api/favorites/user/{$user->id}", $headers);
    $get->assertOk()->assertJsonPath('id', $bookModel->id);

    $del = $this->deleteJson("/api/favorites/user/{$user->id}", [], $headers);
    $del->assertOk();
});

