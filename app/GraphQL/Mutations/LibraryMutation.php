<?php
namespace App\GraphQL\Mutations;
use App\Models\User;
use App\Models\Book;
use App\Models\Author;
use App\Models\Loan;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Mail\ResetPasswordMail;
use App\Services\BookService;
use App\Services\AuthorService;
use App\Jobs\SendBookDueNotification;
use App\Jobs\SendPasswordResetEmail;
use App\Jobs\SendCartEngagementEmail;
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
        return [
            'user' => $user,
            'message' => 'Conta criada com sucesso. Faça login para continuar.',
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
        $token = null;
        try {
            $token = JWTAuth::getToken();
        } catch (\Throwable $e) {
        }

        if (!$token) {
            try {
                $authHeader = (string) request()->header('Authorization', '');
                if (preg_match('/^Bearer\s+(.*)$/i', trim($authHeader), $m)) {
                    $token = $m[1];
                }
            } catch (\Throwable $e) {
            }
        }

        try {
            $user = null;
            try {
                if ($token) {
                    $user = JWTAuth::setToken($token)->authenticate();
                }
            } catch (\Throwable $e) {
            }

            if (!$token) {
                Log::info('Logout called without JWT token; cart engagement email dispatch skipped');
            }

            if ($user && empty($user->is_admin)) {
                $hasCart = CartItem::where('user_id', (int) $user->id)
                    ->where('quantity', '>', 0)
                    ->exists();

                if ($hasCart) {
                    try {
                        SendCartEngagementEmail::dispatch((int) $user->id);
                        Log::info('Dispatched cart engagement email on logout', [
                            'user_id' => (int) $user->id,
                            'email' => (string) $user->email,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('Failed to dispatch cart engagement email on logout', [
                            'user_id' => (int) $user->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        try {
            if ($token) {
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
            throw new \Exception('Access not allowed.');
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

    public function restoreBook($rootValue, array $args): Book
    {
        $this->requireAdmin();
        $id = (int) ($args['id'] ?? 0);
        $book = Book::withTrashed()->findOrFail($id);
        $book->restore();
        return $book->load('author');
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
        if ($user->is_admin) {
            throw new \Exception('Administradores não podem alugar livros.');
        }
        $input = $args['input'] ?? [];
        $validator = Validator::make($input, [
            'user_id' => ['required', 'integer', 'exists:users,id,deleted_at,NULL'],
            'book_id' => ['required', 'integer', 'exists:books,id,deleted_at,NULL'],
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
            throw new \Exception('Unauthorized');
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
            throw new \Exception('Access not allowed.');
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
        if ($user->is_admin) {
            throw new \Exception('Administradores não podem favoritar livros.');
        }
        $input = $args['input'] ?? [];
        $validator = Validator::make($input, [
            'user_id' => ['required', 'integer', 'exists:users,id,deleted_at,NULL'],
            'book_id' => ['required', 'integer', 'exists:books,id,deleted_at,NULL'],
        ]);
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
        $data = $validator->validated();
        if ((int) $data['user_id'] !== (int) $user->id) {
            throw new \Exception('Acesso não permitido.');
        }
        $existing = Favorite::withTrashed()->where('user_id', $data['user_id'])->first();
        if ($existing) {
            if ((int) $existing->book_id === (int) $data['book_id']) {
                if ($existing->trashed()) {
                    $existing->restore();
                } else {
                    $existing->delete();
                }
            } else {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                $existing->book_id = (int) $data['book_id'];
                $existing->save();
            }
        } else {
            Favorite::create([
                'user_id' => (int) $data['user_id'],
                'book_id' => (int) $data['book_id'],
            ]);
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
            'book_id' => ['required', 'integer', 'exists:books,id,deleted_at,NULL'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
        ]);
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
        $data = $validator->validated();
        $review = Review::withTrashed()
            ->where('user_id', $user->id)
            ->where('book_id', (int) $data['book_id'])
            ->first();
        if ($review) {
            if ($review->trashed()) {
                $review->restore();
            }
            $review->rating = (int) $data['rating'];
            $review->comment = $data['comment'] ?? null;
            $review->save();
        } else {
            $review = Review::create([
                'user_id' => (int) $user->id,
                'book_id' => (int) $data['book_id'],
                'rating' => (int) $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);
        }
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
        $uploadName = $safeName . '.' . $extension;
        $resp = Http::attach('file', $binary, $uploadName)
            ->post(
                'https://api.cloudinary.com/v1_1/ddfgsoh5g/image/upload',
                [
                    'upload_preset' => 'pedbook_unsigned',
                    'folder' => $folder,
                ]
            );
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
    public function checkout($rootValue, array $args): Order
    {
        $user = auth('api')->user();
        if (!$user) {
            throw new \Exception('Não autorizado.');
        }
        if (!empty($user->is_admin)) {
            throw new \Exception('Administradores não podem comprar livros.');
        }
        $input = $args['input'] ?? [];
        $validator = Validator::make($input, [
            'items' => ['required', 'array', 'min:1'],
            'items.*.book_id' => ['required', 'integer', 'exists:books,id,deleted_at,NULL'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
        $data = $validator->validated();
        $itemsInput = $data['items'];
        $order = DB::transaction(function () use ($user, $itemsInput) {
            $bookIds = array_column($itemsInput, 'book_id');
            $books = Book::whereIn('id', $bookIds)->get()->keyBy('id');
            $total = 0.0;
            $order = Order::create([
                'user_id' => $user->id,
                'total' => 0,
                'status' => 'paid',
            ]);
            foreach ($itemsInput as $item) {
                $bookId = (int) $item['book_id'];
                $qty = (int) $item['quantity'];
                $book = $books->get($bookId);
                if (!$book) {
                    throw new \Exception('Livro não encontrado.');
                }
                $unit = (float) ($book->price ?? 0);
                if ($unit <= 0) {
                    $unit = 19.90;
                }
                $lineTotal = $unit * $qty;
                $total += $lineTotal;
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $bookId,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                ]);
            }
            $order->total = $total;
            $order->save();
            return $order;
        });
        return $order->load(['items.book.author', 'user']);
    }
    public function upsertCartItem($rootValue, array $args): CartItem
    {
        $user = auth('api')->user();
        if (!$user) {
            throw new \Exception('Unauthorized');
        }
        if (!empty($user->is_admin)) {
            throw new \Exception('Administradores não podem adicionar itens ao carrinho.');
        }
        $validator = Validator::make($args, [
            'book_id' => ['required', 'integer', 'exists:books,id,deleted_at,NULL'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
        $data = $validator->validated();
        $bookId = (int) $data['book_id'];
        $qty = (int) $data['quantity'];
        $existing = CartItem::withTrashed()
            ->where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->first();
        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->quantity = $qty;
            $existing->save();
            return $existing->load('book.author');
        }
        $item = CartItem::create([
            'user_id' => (int) $user->id,
            'book_id' => $bookId,
            'quantity' => $qty,
        ]);
        return $item->load('book.author');
    }
    public function removeCartItem($rootValue, array $args): array
    {
        $user = auth('api')->user();
        if (!$user) {
            throw new \Exception('Unauthorized');
        }
        $validator = Validator::make($args, [
            'book_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
        $bookId = (int) $validator->validated()['book_id'];
        CartItem::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->delete();
        return ['message' => 'Cart item removed'];
    }
    public function clearCart(): array
    {
        $user = auth('api')->user();
        if (!$user) {
            throw new \Exception('Unauthorized');
        }
        CartItem::where('user_id', $user->id)->delete();
        return ['message' => 'Cart cleared'];
    }
    public function requestPasswordReset($rootValue, array $args): array
    {
        $email = (string) ($args['email'] ?? '');
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
        $user = User::where('email', $email)->first();
        if (!$user) {
            return ['message' => 'If an account with this email exists, we will send a reset link.'];
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
        try {
            $resendKey = (string) (
                getenv('RESEND_API_KEY')
                ?: ($_SERVER['RESEND_API_KEY'] ?? '')
                ?: ($_ENV['RESEND_API_KEY'] ?? '')
            );
            Log::info('Password reset email delivery method', [
                'resend_key_present' => !empty($resendKey),
                'resend_key_len' => strlen($resendKey),
                'sapi' => php_sapi_name(),
                'pid' => getmypid(),
            ]);
            if (!empty($resendKey)) {
                $resendFrom = (string) (
                    getenv('RESEND_FROM')
                    ?: ($_SERVER['RESEND_FROM'] ?? '')
                    ?: ($_ENV['RESEND_FROM'] ?? '')
                );
                if (empty($resendFrom)) {
                    $resendFrom = 'PedBook <onboarding@resend.dev>';
                }

                $payload = json_encode([
                    'from' => $resendFrom,
                    'to' => [$user->email],
                    'subject' => 'Redefinição de senha - PedBook',
                    'html' => '<p>Olá,</p><p>Para redefinir sua senha, acesse o link abaixo:</p>'
                        . '<p><a href="' . e($url) . '">Redefinir senha</a></p>'
                        . '<p>Se você não solicitou isso, ignore este e-mail.</p>',
                ]);

                $tmpFile = tempnam(sys_get_temp_dir(), 'resend_');
                file_put_contents($tmpFile, $payload);

                $cmd = 'curl -sS -o - -w "\nHTTP_STATUS:%{http_code}\n"'
                    . ' -X POST https://api.resend.com/emails'
                    . ' -H ' . escapeshellarg('Authorization: Bearer ' . $resendKey)
                    . ' -H ' . escapeshellarg('Content-Type: application/json')
                    . ' --data-binary @' . escapeshellarg($tmpFile)
                    . ' --max-time 20 2>&1';

                $output = [];
                $exitCode = 0;
                exec($cmd, $output, $exitCode);
                @unlink($tmpFile);

                $raw = implode("\n", $output);
                $status = 0;
                $body = $raw;
                if (preg_match('/\nHTTP_STATUS:(\d+)\n?$/', $raw, $m)) {
                    $status = (int) $m[1];
                    $body = preg_replace('/\nHTTP_STATUS:\d+\n?$/', '', $raw);
                }

                if ($exitCode !== 0 || $status < 200 || $status >= 300) {
                    Log::error('Resend API failed to send password reset email', [
                        'email' => $user->email,
                        'from' => $resendFrom,
                        'exit_code' => $exitCode,
                        'http_status' => $status,
                        'response' => $body,
                    ]);
                } else {
                    Log::info('Resend API sent password reset email', [
                        'email' => $user->email,
                        'from' => $resendFrom,
                        'to' => $user->email,
                        'http_status' => $status,
                        'response' => $body,
                    ]);
                }
            } elseif ((string) (getenv('MAILTRAP_SEND_API_ENABLED') ?: ($_SERVER['MAILTRAP_SEND_API_ENABLED'] ?? '') ?: ($_ENV['MAILTRAP_SEND_API_ENABLED'] ?? '')) === 'true'
                && !empty((string) (getenv('MAILTRAP_API_TOKEN') ?: ($_SERVER['MAILTRAP_API_TOKEN'] ?? '') ?: ($_ENV['MAILTRAP_API_TOKEN'] ?? '')))
            ) {
                $mailtrapToken = (string) (
                    getenv('MAILTRAP_API_TOKEN')
                    ?: ($_SERVER['MAILTRAP_API_TOKEN'] ?? '')
                    ?: ($_ENV['MAILTRAP_API_TOKEN'] ?? '')
                );
                $mailtrapFromEmail = (string) (
                    getenv('MAILTRAP_FROM_EMAIL')
                    ?: ($_SERVER['MAILTRAP_FROM_EMAIL'] ?? '')
                    ?: ($_ENV['MAILTRAP_FROM_EMAIL'] ?? '')
                );
                if (empty($mailtrapFromEmail)) {
                    $mailtrapFromEmail = 'hello@demomailtrap.co';
                }
                $mailtrapFromName = (string) (
                    getenv('MAILTRAP_FROM_NAME')
                    ?: ($_SERVER['MAILTRAP_FROM_NAME'] ?? '')
                    ?: ($_ENV['MAILTRAP_FROM_NAME'] ?? '')
                );
                if (empty($mailtrapFromName)) {
                    $mailtrapFromName = 'PedBook';
                }

                $payload = json_encode([
                    'from' => [
                        'email' => $mailtrapFromEmail,
                        'name' => $mailtrapFromName,
                    ],
                    'to' => [
                        [
                            'email' => $user->email,
                        ],
                    ],
                    'subject' => 'Redefinição de senha - PedBook',
                    'html' => '<p>Olá,</p><p>Para redefinir sua senha, acesse o link abaixo:</p>'
                        . '<p><a href="' . e($url) . '">Redefinir senha</a></p>'
                        . '<p>Se você não solicitou isso, ignore este e-mail.</p>',
                ]);

                $tmpFile = tempnam(sys_get_temp_dir(), 'mailtrap_');
                file_put_contents($tmpFile, $payload);

                $cmd = 'curl -sS -o - -w "\nHTTP_STATUS:%{http_code}\n"'
                    . ' -X POST https://send.api.mailtrap.io/api/send'
                    . ' -H ' . escapeshellarg('Authorization: Bearer ' . $mailtrapToken)
                    . ' -H ' . escapeshellarg('Content-Type: application/json')
                    . ' --data-binary @' . escapeshellarg($tmpFile)
                    . ' --max-time 20 2>&1';

                $output = [];
                $exitCode = 0;
                exec($cmd, $output, $exitCode);
                @unlink($tmpFile);

                $raw = implode("\n", $output);
                $status = 0;
                $body = $raw;
                if (preg_match('/\nHTTP_STATUS:(\d+)\n?$/', $raw, $m)) {
                    $status = (int) $m[1];
                    $body = preg_replace('/\nHTTP_STATUS:\d+\n?$/', '', $raw);
                }

                if ($exitCode !== 0 || $status < 200 || $status >= 300) {
                    Log::error('Mailtrap Send API failed to send password reset email', [
                        'email' => $user->email,
                        'exit_code' => $exitCode,
                        'http_status' => $status,
                        'response' => $body,
                    ]);
                } else {
                    Log::info('Mailtrap Send API sent password reset email', [
                        'email' => $user->email,
                        'http_status' => $status,
                        'response' => $body,
                    ]);
                }
            } else {
                Mail::to($user->email)->send(new ResetPasswordMail($user, $url));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send password reset email for ' . $user->email . ': ' . $e->getMessage());
        }
        return ['message' => 'If an account with this email exists, we will send a reset link.'];
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
