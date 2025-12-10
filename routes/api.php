<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;

Route::get('/home', function () {
    $html = file_get_contents(__DIR__ . '/../public/home.html');
    return response($html, 200)
        ->header('Content-Type', 'text/html; charset=utf-8')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
});
Route::get('/app', function () {
    return response()->file(__DIR__ . '/../public/app-v2.html', [
        'Content-Type' => 'text/html; charset=utf-8',
        'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
        'Pragma' => 'no-cache',
    ]);
});


Route::get('/img', function (Request $request) {
    $url = $request->query('url');
    if (!$url) {
        return response('Missing url', 400);
    }
    if (!str_starts_with($url, 'https://res.cloudinary.com/ddfgsoh5g/')) {
        return response('Forbidden', 403);
    }
    try {
        $maxBytes = 10 * 1024 * 1024; 
        $resp = Http::withHeaders([
            'Accept' => 'image/*',
            'User-Agent' => 'PedBook/1.0'
        ])->timeout(7)->get($url);
        if (!$resp->ok()) {
            return response('Not found', 404);
        }
        $len = (int) ($resp->header('Content-Length') ?? 0);
        if ($len > 0 && $len > $maxBytes) {
            return response('Too Large', 413);
        }
        $type = $resp->header('Content-Type') ?? 'image/jpeg';
        return response($resp->body(), 200)
            ->header('Content-Type', $type)
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Cache-Control', 'public, max-age=31536000');
    } catch (\Throwable $e) {
        \Log::error('img proxy error: '.$e->getMessage(), ['url' => $url]);
        return response('Error', 500);
    }
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/jwt/login', [AuthController::class, 'jwtLogin']);

Route::get('/authors', [AuthorController::class, 'index']);
Route::get('/authors/{id}', [AuthorController::class, 'show']);
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);
Route::get('/reviews/book/{bookId}', [ReviewController::class, 'listByBook']);


Route::middleware('jwt.auth')->group(function () {
    
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    
    Route::get('/users', [AuthController::class, 'getUsers']);
    Route::put('/users/{id}', [AuthController::class, 'updateProfile']);

    
    Route::get('/favorites/user/{userId}', [FavoriteController::class, 'showByUser']);
    Route::post('/favorites', [FavoriteController::class, 'upsert']);
    Route::delete('/favorites/user/{userId}', [FavoriteController::class, 'destroyByUser']);

    
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    
    Route::get('/loans', [LoanController::class, 'index']);
    Route::get('/loans/user/{userId}', [LoanController::class, 'userLoans']);
    Route::get('/loans/active-book-ids', [LoanController::class, 'activeBookIds']);
    Route::post('/loans', [LoanController::class, 'store']);
    Route::put('/loans/{id}/return', [LoanController::class, 'returnBook']);
    Route::delete('/loans/{id}', [LoanController::class, 'destroy']);

    
    Route::middleware('can:admin')->group(function () {
        
        Route::post('/authors', [AuthorController::class, 'store']);
        Route::put('/authors/{id}', [AuthorController::class, 'update']);
        Route::delete('/authors/{id}', [AuthorController::class, 'destroy']);

        
        Route::post('/books', [BookController::class, 'store']);
        Route::put('/books/{id}', [BookController::class, 'update']);
        Route::delete('/books/{id}', [BookController::class, 'destroy']);
    });
});

Route::middleware('jwt.auth')->group(function () {
    Route::get('/jwt/me', [AuthController::class, 'jwtMe']);
});
