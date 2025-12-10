<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\ReviewResource;

class ReviewController extends Controller
{
    public function listByBook($bookId)
    {
        try {
            $reviews = Review::where('book_id', $bookId)->with('user')->orderByDesc('created_at')->get();
            return response()->json(ReviewResource::collection($reviews)->resolve());
        } catch (\Throwable $e) {
            \Log::error('reviews.list error: '.$e->getMessage(), ['book' => $bookId]);
            return response()->json(['error' => true, 'message' => 'Error fetching reviews'], 500);
        }
    }

    public function store(\App\Http\Requests\ReviewRequest $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            if (!empty($user->is_admin) && $user->is_admin) {
                return response()->json(['message' => 'Administradores não podem deixar avaliações'], 403);
            }

            $validated = $request->validated();

            $review = Review::updateOrCreate(
                ['user_id' => $user->id, 'book_id' => $validated['book_id']],
                ['rating' => $validated['rating'], 'comment' => $validated['comment'] ?? null]
            );
            return (new ReviewResource($review->load('user')))->response()->setStatusCode(200);
        } catch (\Throwable $e) {
            \Log::error('reviews.store error: '.$e->getMessage());
            return response()->json(['error' => true, 'message' => 'Error saving review'], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $user = request()->user();
            $review = Review::findOrFail($id);
            if (!$user || ((int)$review->user_id !== (int)$user->id)) {
                return response()->json(['message' => 'Proibido excluir esta avaliação'], 403);
            }
            $review->delete();
            return response()->json(['message' => 'Review deleted']);
        } catch (\Throwable $e) {
            \Log::error('reviews.destroy error: '.$e->getMessage(), ['id' => $id]);
            return response()->json(['error' => true, 'message' => 'Error deleting review'], 404);
        }
    }
}
