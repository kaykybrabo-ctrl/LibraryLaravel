<?php

use App\Models\User;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

it('prevents admin from creating reviews and allows normal user', function () {
    $author = Author::firstOrCreate(['name' => 'Rev Author'], ['bio' => 'Bio', 'photo' => null]);
    $book = Book::firstOrCreate([
        'title' => 'Rev Book',
        'author_id' => $author->id,
    ], [
        'description' => 'Desc',
        'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/sample.jpg',
    ]);

    $admin = User::firstOrCreate(
        ['email' => 'kayky@gmail.com'],
        ['name' => 'Kayky Admin', 'password' => Hash::make('123'), 'is_admin' => true]
    );
    Sanctum::actingAs($admin);
    $adminTry = $this->postJson('/api/reviews', ['book_id' => $book->id, 'rating' => 5]);
    $adminTry->assertStatus(403);

    $user = User::firstOrCreate(
        ['email' => 'kaue@gmail.com'],
        ['name' => 'Kaue User', 'password' => Hash::make('123'), 'is_admin' => false]
    );
    Sanctum::actingAs($user);
    $userTry = $this->postJson('/api/reviews', ['book_id' => $book->id, 'rating' => 5, 'comment' => 'Nice']);
    $userTry->assertOk()->assertJsonPath('data.rating', 5);

    $delete = $this->deleteJson('/api/reviews/'.$userTry->json('data.id'));
    $delete->assertOk();
});

it('validates rating between 1 and 5', function () {
    $author = Author::firstOrCreate(['name' => 'Rev2 Author'], ['bio' => 'Bio', 'photo' => null]);
    $book = Book::firstOrCreate([
        'title' => 'Rev2 Book',
        'author_id' => $author->id,
    ], [
        'description' => 'Desc',
        'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/sample.jpg',
    ]);
    $user = User::firstOrCreate(
        ['email' => 'kaue@gmail.com'],
        ['name' => 'Kaue User', 'password' => Hash::make('123'), 'is_admin' => false]
    );
    Sanctum::actingAs($user);

    $bad = $this->postJson('/api/reviews', ['book_id' => $book->id, 'rating' => 6]);
    $bad->assertStatus(422);
});
