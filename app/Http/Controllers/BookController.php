<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Services\BookService;
use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;

class BookController extends Controller
{
    public function __construct(private BookService $service) {}

    public function index(Request $request)
    {
        try {
            if ($request->boolean('all')) {
                $books = $this->service->listAll();
                return response()->json($books);
            }
            $perPage = max(1, (int) $request->query('per_page', 10));
            $books = $this->service->paginate($perPage);
            return BookResource::collection($books)->response();
        } catch (\Throwable $e) {
            \Log::error('books.index error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $book = $this->service->find((int)$id);
            return response()->json($book);
        } catch (\Throwable $e) {
            \Log::error('books.show error: '.$e->getMessage(), ['id' => $id]);
            return response()->json(['error' => true, 'message' => $e->getMessage()], 404);
        }
    }

    public function store(BookRequest $request)
    {
        try {
            $book = $this->service->create($request->validated());
            return (new BookResource($book))->response()->setStatusCode(201);
        } catch (\Throwable $e) {
            \Log::error('books.store error: '.$e->getMessage());
            return response()->json(['error' => true, 'message' => $e->getMessage()], 422);
        }
    }

    public function update(BookRequest $request, $id)
    {
        try {
            $book = $this->service->update((int)$id, $request->validated());
            return (new BookResource($book))->response();
        } catch (\Throwable $e) {
            \Log::error('books.update error: '.$e->getMessage(), ['id' => $id]);
            return response()->json(['error' => true, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete((int)$id);
            return response()->json(['message' => 'Book deleted']);
        } catch (\Throwable $e) {
            \Log::error('books.destroy error: '.$e->getMessage(), ['id' => $id]);
            return response()->json(['error' => true, 'message' => $e->getMessage()], 404);
        }
    }
}
