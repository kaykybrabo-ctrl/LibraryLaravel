<?php

use App\Models\User;
use App\Models\Author;
use App\Models\Book;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

it('prevents admin from creating reviews and allows normal user', function () {
    $author = Author::factory()->create([
        'name' => 'Rev Author',
        'bio' => 'Bio',
        'photo' => null,
    ]);
    $book = Book::factory()
        ->for($author)
        ->create([
            'title' => 'Rev Book',
            'description' => 'Desc',
            'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/sample.jpg',
        ]);

    $admin = User::factory()->create([
        'name' => 'Kayky',
        'email' => 'kayky@gmail.com',
        'is_admin' => true,
    ]);
    $adminToken = JWTAuth::fromUser($admin);
    $adminHeaders = ['Authorization' => 'Bearer '.$adminToken];
    $adminTry = $this->postJson('/api/reviews', ['book_id' => $book->id, 'rating' => 5], $adminHeaders);
    $adminTry->assertStatus(403);

    $user = User::factory()->create([
        'name' => 'Kaue',
        'email' => 'kaue@gmail.com',
        'is_admin' => false,
    ]);
    $userToken = JWTAuth::fromUser($user);
    $userHeaders = ['Authorization' => 'Bearer '.$userToken];
    $userTry = $this->postJson('/api/reviews', ['book_id' => $book->id, 'rating' => 5, 'comment' => 'Nice'], $userHeaders);
    $userTry->assertOk()->assertJsonPath('data.rating', 5);

    $delete = $this->deleteJson('/api/reviews/'.$userTry->json('data.id'), [], $userHeaders);
    $delete->assertOk();
});

it('validates rating between 1 and 5', function () {
    $author = Author::factory()->create([
        'name' => 'Rev2 Author',
        'bio' => 'Bio',
        'photo' => null,
    ]);
    $book = Book::factory()
        ->for($author)
        ->create([
            'title' => 'Rev2 Book',
            'description' => 'Desc',
            'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/sample.jpg',
        ]);
    $user = User::factory()->create([
        'name' => 'Kaue',
        'email' => 'kaue@gmail.com',
        'is_admin' => false,
    ]);
    $userToken = JWTAuth::fromUser($user);
    $userHeaders = ['Authorization' => 'Bearer '.$userToken];

    $bad = $this->postJson('/api/reviews', ['book_id' => $book->id, 'rating' => 6], $userHeaders);
    $bad->assertStatus(422);
});
