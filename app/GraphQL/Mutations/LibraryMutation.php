<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Models\Book;
use App\Models\Author;
use App\Models\Loan;
use App\Models\Favorite;
use App\Models\Review;
use App\Mail\ResetPasswordMail;
use App\Services\BookService;
use App\Services\AuthorService;
use App\Jobs\SendBookDueNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
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
            throw new \Exception('Credenciais inválidas.');
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
            throw new \Exception('Acesso não permitido.');
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
            throw new \Exception('Não autorizado.');
        }

        if (!empty($user->is_admin)) {
            throw new \Exception('Administradores não podem alugar livros.');
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
            throw new \Exception('Usuário inválido para este empréstimo.');
        }

        $activeExists = Loan::where('book_id', $data['book_id'])
            ->whereNull('returned_at')
            ->exists();

        if ($activeExists) {
            throw new \Exception('Livro já está alugado.');
        }

        $loan = Loan::create($data);

        try {
            SendBookDueNotification::dispatch($loan->id);
        } catch (\Throwable $e) {
        }

        return $loan->load(['book.author', 'user']);
    }

    public function returnBook($rootValue, array $args): Loan
    {
        $user = auth('api')->user();

        if (!$user) {
            throw new \Exception('Não autorizado.');
        }

        $id = (int) ($args['id'] ?? 0);
        $loan = Loan::findOrFail($id);

        if (!$user->is_admin && (int) $loan->user_id !== (int) $user->id) {
            throw new \Exception('Acesso não permitido.');
        }

        $loan->update(['returned_at' => now()]);

        return $loan->load(['book.author', 'user']);
    }

    public function deleteLoan($rootValue, array $args): array
    {
        $user = auth('api')->user();

        if (!$user || !$user->is_admin) {
            throw new \Exception('Acesso não permitido.');
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
            throw new \Exception('Administradores não podem favoritar livros.');
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
            throw new \Exception('Acesso não permitido.');
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
            throw new \Exception('Acesso não permitido.');
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
            throw new \Exception('Administradores não podem deixar avaliações.');
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
            throw new \Exception('Acesso não permitido.');
        }

        $review->delete();

        return ['message' => 'Review deleted'];
    }

    public function uploadImage($rootValue, array $args): string
    {
        $user = auth('api')->user();

        if (!$user) {
            throw new \Exception('Unauthorized');
        }

        $target = strtolower((string) ($args['target'] ?? ''));
        $filename = (string) ($args['filename'] ?? '');
        $fileData = (string) ($args['fileData'] ?? '');

        if ($target === '' || $filename === '' || $fileData === '') {
            throw new \Exception('Dados da imagem ausentes.');
        }

        if (!in_array($target, ['book', 'author', 'profile'], true)) {
            throw new \Exception('Destino de upload inválido.');
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if ($ext === '' || !in_array($ext, $allowed, true)) {
            throw new \Exception('Apenas arquivos de imagem são permitidos.');
        }

        $base64 = $fileData;
        $mime = null;

        if (str_starts_with($fileData, 'data:image/')) {
            $commaPos = strpos($fileData, ',');
            if ($commaPos === false) {
                throw new \Exception('Dados de imagem inválidos.');
            }

            $header = substr($fileData, 0, $commaPos);
            $base64 = substr($fileData, $commaPos + 1);

            if (preg_match('#^data:(image/[^;]+);base64$#', $header, $m)) {
                $mime = $m[1];
            }
        }

        if ($mime === null) {
            $extForMime = $ext === 'jpg' ? 'jpeg' : $ext;
            $mime = 'image/' . $extForMime;
        }

        $binary = base64_decode($base64, true);

        if ($binary === false) {
            throw new \Exception('Dados de imagem em base64 inválidos.');
        }

        $folder = $target === 'book' ? 'pedbook/books' : 'pedbook/profiles';

        $nameOnly = pathinfo($filename, PATHINFO_FILENAME);
        if ($nameOnly === '') {
            $nameOnly = 'image';
        }
        $safeName = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $nameOnly);
        if ($safeName === '' || $safeName === '_') {
            $safeName = 'image';
        }
        $uploadName = $safeName . '.' . $ext;

        try {
            $resp = Http::attach('file', $binary, $uploadName)
                ->post(
                    'https://api.cloudinary.com/v1_1/ddfgsoh5g/image/upload',
                    [
                        'upload_preset' => 'pedbook_unsigned',
                        'folder' => $folder,
                    ]
                );
        } catch (\Throwable $e) {
            throw new \Exception('Falha ao enviar a imagem para o Cloudinary.');
        }

        if (!$resp->ok()) {
            $json = $resp->json();
            $msg = is_array($json) && isset($json['error']['message'])
                ? $json['error']['message']
                : 'Falha no upload.';

            throw new \Exception('Erro do Cloudinary: ' . $msg);
        }

        $json = $resp->json();
        $publicId = is_array($json) ? ($json['public_id'] ?? null) : null;

        if (!$publicId) {
            throw new \Exception('Resposta inválida do Cloudinary.');
        }

        return (string) $publicId;
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

    public function requestPasswordReset($rootValue, array $args): array
    {
        $email = (string) ($args['email'] ?? '');

        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return ['message' => 'Se existir uma conta com este e-mail, enviaremos um link de redefinição.'];
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $baseUrl = config('app.url', 'http://localhost:8080');
        $url = rtrim($baseUrl, '/') . '/reset-password?token=' . urlencode($token) . '&email=' . urlencode($user->email);

        Log::info('Password reset link for ' . $user->email . ': ' . $url);

        Mail::to($user->email)->send(new ResetPasswordMail($user, $url));

        return ['message' => 'Se existir uma conta com este e-mail, enviaremos um link de redefinição.'];
    }

    public function resetPassword($rootValue, array $args): array
    {
        $input = $args['input'] ?? [];

        $validator = Validator::make($input, [
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:3'],
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $data = $validator->validated();

        $record = DB::table('password_reset_tokens')->where('email', $data['email'])->first();

        if (!$record) {
            throw new \Exception('Token inválido ou expirado.');
        }

        if (!Hash::check($data['token'], $record->token)) {
            throw new \Exception('Token inválido ou expirado.');
        }

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            throw new \Exception('Usuário não encontrado.');
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        return ['message' => 'Senha redefinida com sucesso.'];
    }
}
