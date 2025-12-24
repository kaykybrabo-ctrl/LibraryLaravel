<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Models\Book;
use App\Models\Author;
use App\Models\Loan;
use App\Models\Favorite;
use App\Models\Review;
use App\Services\BookService;
use App\Services\AuthorService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class LibraryMutation
{
    public function __construct(private BookService $books, private AuthorService $authors)
    {
    }

    public function register($rootValue, array $args)
    {
        $input = $args['input'] ?? [];

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:3'],
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $data = $validator->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => false,
        ]);

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login($rootValue, array $args)
    {
        $input = $args['input'] ?? [];

        $validator = Validator::make($input, [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $data = $validator->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout()
    {
        try {
            if ($token = JWTAuth::getToken()) {
                JWTAuth::invalidate($token);
            }
        } catch (\Throwable $e) {
        }

        return ['message' => 'Logged out'];
    }

    protected function requireAdmin(): User
    {
        $user = auth('api')->user();

        if (!$user || !$user->is_admin) {
            throw new \Exception('Forbidden');
        }

        return $user;
    }

    public function createBook($rootValue, array $args): Book
    {
        $this->requireAdmin();

        $input = $args['input'] ?? [];

        return $this->books->create($input);
    }

    public function updateBook($rootValue, array $args): Book
    {
        $this->requireAdmin();

        $id = (int) ($args['id'] ?? 0);
        $input = $args['input'] ?? [];

        return $this->books->update($id, $input);
    }

    public function deleteBook($rootValue, array $args): array
    {
        $this->requireAdmin();

        $id = (int) ($args['id'] ?? 0);
        $this->books->delete($id);

        return ['message' => 'Book deleted'];
    }

    public function createAuthor($rootValue, array $args): Author
    {
        $this->requireAdmin();

        $input = $args['input'] ?? [];

        return $this->authors->create($input);
    }

    public function updateAuthor($rootValue, array $args): Author
    {
        $this->requireAdmin();

        $id = (int) ($args['id'] ?? 0);
        $input = $args['input'] ?? [];

        return $this->authors->update($id, $input);
    }

    public function deleteAuthor($rootValue, array $args): array
    {
        $this->requireAdmin();

        $id = (int) ($args['id'] ?? 0);
        $this->authors->delete($id);

        return ['message' => 'Author deleted'];
    }

    public function rentBook($rootValue, array $args): Loan
    {
        $user = auth('api')->user();

        if (!$user) {
            throw new \Exception('Unauthorized');
        }

        if (!empty($user->is_admin)) {
            throw new \Exception('Admins cannot rent books');
        }

        $input = $args['input'] ?? [];

        $validator = Validator::make($input, [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'loan_date' => ['required', 'date'],
            'return_date' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $data = $validator->validated();

        if ((int) $data['user_id'] !== (int) $user->id) {
            throw new \Exception('Invalid user for this loan');
        }

        $activeExists = Loan::where('book_id', $data['book_id'])
            ->whereNull('returned_at')
            ->exists();

        if ($activeExists) {
            throw new \Exception('Book is already rented');
        }

        $loan = Loan::create($data);

        return $loan->load(['book.author', 'user']);
    }

    public function returnBook($rootValue, array $args): Loan
    {
        $user = auth('api')->user();

        if (!$user) {
            throw new \Exception('Unauthorized');
        }

        $id = (int) ($args['id'] ?? 0);
        $loan = Loan::findOrFail($id);

        if (!$user->is_admin && (int) $loan->user_id !== (int) $user->id) {
            throw new \Exception('Forbidden');
        }

        $loan->update(['returned_at' => now()]);

        return $loan->load(['book.author', 'user']);
    }

    public function deleteLoan($rootValue, array $args): array
    {
        $user = auth('api')->user();

        if (!$user || !$user->is_admin) {
            throw new \Exception('Forbidden');
        }

        $id = (int) ($args['id'] ?? 0);

        Loan::findOrFail($id)->delete();

        return ['message' => 'Loan deleted'];
    }

    public function toggleFavorite($rootValue, array $args): Book
    {
        $user = auth('api')->user();

        if (!$user) {
            throw new \Exception('Unauthorized');
        }

        if (!empty($user->is_admin)) {
            throw new \Exception('Admins cannot favorite books');
        }

        $input = $args['input'] ?? [];

        $validator = Validator::make($input, [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'book_id' => ['required', 'integer', 'exists:books,id'],
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $data = $validator->validated();

        if ((int) $data['user_id'] !== (int) $user->id) {
            throw new \Exception('Forbidden');
        }

        $existing = Favorite::where('user_id', $data['user_id'])->first();

        if ($existing && (int) $existing->book_id === (int) $data['book_id']) {
            $existing->delete();
        } else {
            Favorite::updateOrCreate(
                ['user_id' => $data['user_id']],
                ['book_id' => $data['book_id']]
            );
        }

        return Book::with('author')->findOrFail($data['book_id']);
    }

    public function removeFavorite($rootValue, array $args): array
    {
        $user = auth('api')->user();

        if (!$user) {
            throw new \Exception('Unauthorized');
        }

        $userId = (int) ($args['user_id'] ?? 0);

        if ((int) $user->id !== $userId && !$user->is_admin) {
            throw new \Exception('Forbidden');
        }

        Favorite::where('user_id', $userId)->delete();

        return ['message' => 'Favorite removed'];
    }

    public function upsertReview($rootValue, array $args): Review
    {
        $user = auth('api')->user();

        if (!$user) {
            throw new \Exception('Unauthorized');
        }

        if (!empty($user->is_admin)) {
            throw new \Exception('Admins cannot leave reviews');
        }

        $input = $args['input'] ?? [];

        $validator = Validator::make($input, [
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $data = $validator->validated();

        $review = Review::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $data['book_id']],
            ['rating' => $data['rating'], 'comment' => $data['comment'] ?? null]
        );

        return $review->load('user', 'book');
    }

    public function deleteReview($rootValue, array $args): array
    {
        $user = auth('api')->user();

        if (!$user) {
            throw new \Exception('Unauthorized');
        }

        $id = (int) ($args['id'] ?? 0);

        $review = Review::findOrFail($id);

        if ((int) $review->user_id !== (int) $user->id && !$user->is_admin) {
            throw new \Exception('Forbidden');
        }

        $review->delete();

        return ['message' => 'Review deleted'];
    }

    public function updateProfile($rootValue, array $args): User
    {
        $user = auth('api')->user();

        if (!$user) {
            throw new \Exception('Unauthorized');
        }

        $input = $args['input'] ?? [];

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $data = $validator->validated();

        $user->update([
            'name' => $data['name'],
            'photo' => $data['photo'] ?? $user->photo,
        ]);

        return $user->fresh();
    }
}
