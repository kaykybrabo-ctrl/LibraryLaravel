<?php

use App\Models\User;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

it('creates a loan, prevents duplicate, and allows return', function () {
    $user = User::firstOrCreate(
        ['email' => 'kaue@gmail.com'],
        ['name' => 'Kaue', 'password' => Hash::make('123'), 'is_admin' => false]
    );
    $token = JWTAuth::fromUser($user);
    $headers = ['Authorization' => 'Bearer '.$token];

    $author = Author::firstOrCreate(['name' => 'Loan Author'], ['bio' => 'Bio', 'photo' => null]);
    $book = Book::firstOrCreate([
        'title' => 'Loan Book',
        'author_id' => $author->id,
    ], [
        'description' => 'Desc',
        'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/sample.jpg',
    ]);

    $payload = [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'loan_date' => now()->toDateString(),
        'return_date' => now()->addDays(7)->toDateString(),
    ];

    $create = $this->postJson('/api/loans', $payload, $headers);
    $create->assertStatus(201)->assertJsonStructure(['data' => ['id','book' => ['id','title']]]);

    $loanId = $create->json('data.id');

    $duplicate = $this->postJson('/api/loans', $payload, $headers);
    $duplicate->assertStatus(422);

    $return = $this->putJson("/api/loans/{$loanId}/return", [], $headers);
    $return->assertStatus(200)->assertJsonPath('data.id', $loanId);

    $again = $this->postJson('/api/loans', $payload, $headers);
    $again->assertStatus(201);
});
