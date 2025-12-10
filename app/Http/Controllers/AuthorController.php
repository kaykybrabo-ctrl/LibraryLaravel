<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\AuthorService;
use App\Http\Requests\AuthorRequest;
use App\Http\Resources\AuthorResource;

class AuthorController extends Controller
{
    public function __construct(private AuthorService $service) {}
    public function index(Request $request)
    {
        try {
            $authors = $this->service->listAll();
            return response()->json($authors);
        } catch (\Throwable $e) {
            \Log::error('authors.index error: '.$e->getMessage());
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $author = $this->service->find((int)$id);
            return response()->json($author);
        } catch (\Throwable $e) {
            \Log::error('authors.show error: '.$e->getMessage(), ['id' => $id]);
            return response()->json(['error' => true, 'message' => $e->getMessage()], 404);
        }
    }

    public function store(AuthorRequest $request)
    {
        try {
            $author = $this->service->create($request->validated());
            return (new AuthorResource($author))->response()->setStatusCode(201);
        } catch (\Throwable $e) {
            \Log::error('authors.store error: '.$e->getMessage());
            return response()->json(['error' => true, 'message' => $e->getMessage()], 422);
        }
    }

    public function update(AuthorRequest $request, $id)
    {
        try {
            $author = $this->service->update((int)$id, $request->validated());
            return (new AuthorResource($author))->response();
        } catch (\Throwable $e) {
            \Log::error('authors.update error: '.$e->getMessage(), ['id' => $id]);
            return response()->json(['error' => true, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete((int)$id);
            return response()->json(['message' => 'Author deleted']);
        } catch (\Throwable $e) {
            \Log::error('authors.destroy error: '.$e->getMessage(), ['id' => $id]);
            return response()->json(['error' => true, 'message' => $e->getMessage()], 404);
        }
    }
}
