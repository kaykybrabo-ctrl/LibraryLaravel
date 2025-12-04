<?php

use App\Models\User;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

it('sets, reads and clears favorite book for a user', function () {
    $user = User::firstOrCreate(
        ['email' => 'kaue@gmail.com'],
        ['name' => 'Kaue User', 'password' => Hash::make('123'), 'is_admin' => false]
    );
    Sanctum::actingAs($user);

    $author = Author::firstOrCreate(['name' => 'Fav Author'], ['bio' => 'Bio', 'photo' => null]);
    $bookModel = Book::firstOrCreate([
        'title' => 'Fav Book',
        'author_id' => $author->id,
    ], [
        'description' => 'Desc',
        'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/sample.jpg',
    ]);

    $payload = [
        'user_id' => $user->id,
        'book_id' => $bookModel->id,
    ];

    $set = $this->postJson('/api/favorites', $payload);
    $set->assertOk();

    $get = $this->getJson("/api/favorites/user/{$user->id}");
    $get->assertOk()->assertJsonPath('data.id', $bookModel->id);

    $del = $this->deleteJson("/api/favorites/user/{$user->id}");
    $del->assertOk();
});
