<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;

class FavoriteController extends Controller
{
    public function showByUser($userId)
    {
        try {
            $fav = Favorite::where('user_id', $userId)->first();
            if (!$fav) {
                return response()->json(null);
            }
            $book = Book::with('author')->find($fav->book_id);
            return response()->json($book);
        } catch (\Throwable $e) {
            \Log::error('favorite.showByUser error: '.$e->getMessage(), ['user' => $userId]);
            return response()->json(['error' => true, 'message' => 'Error fetching favorite'], 500);
        }
    }

    public function upsert(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'book_id' => 'required|exists:books,id',
            ]);
            $user = $request->user();
            if ($user && !empty($user->is_admin) && $user->is_admin) {
                return response()->json(['message' => 'Administradores não podem favoritar livros'], 403);
            }
            if ($user && (int)$user->id !== (int)$validated['user_id']) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            Favorite::updateOrCreate(
                ['user_id' => $validated['user_id']],
                ['book_id' => $validated['book_id']]
            );
            $book = Book::with('author')->find($validated['book_id']);
            return response()->json($book);
        } catch (\Throwable $e) {
            \Log::error('favorite.upsert error: '.$e->getMessage());
            return response()->json(['error' => true, 'message' => 'Error saving favorite'], 422);
        }
    }

    public function destroyByUser($userId)
    {
        try {
            $user = request()->user();
            if ($user && (int)$user->id !== (int)$userId) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            Favorite::where('user_id', $userId)->delete();
            return response()->json(['message' => 'Favorite removed']);
        } catch (\Throwable $e) {
            \Log::error('favorite.destroy error: '.$e->getMessage(), ['user' => $userId]);
            return response()->json(['error' => true, 'message' => 'Error removing favorite'], 500);
        }
    }
}
