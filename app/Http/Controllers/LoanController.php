<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use App\Http\Resources\LoanResource;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with(['user', 'book.author'])->get();
        return response()->json($loans);
    }

    public function userLoans($userId)
    {
        $loans = Loan::where('user_id', $userId)->with('book.author')->get();
        return response()->json($loans);
    }

    public function activeBookIds()
    {
        $ids = Loan::whereNull('returned_at')->pluck('book_id')->unique()->values();
        return response()->json($ids);
    }

    public function store(\App\Http\Requests\LoanRequest $request)
    {
        $validated = $request->validated();

        $auth = $request->user();
        if ($auth) {
            if (!empty($auth->is_admin) && $auth->is_admin) {
                return response()->json(['message' => 'Admins não podem alugar livros'], 403);
            }
            if ((int)$validated['user_id'] !== (int)$auth->id) {
                return response()->json(['message' => 'Usuário inválido para este empréstimo'], 403);
            }
        }

        $activeExists = Loan::where('book_id', $validated['book_id'])
            ->whereNull('returned_at')
            ->exists();
        if ($activeExists) {
            return response()->json(['message' => 'Livro já está alugado'], 422);
        }

        $loan = Loan::create($validated);
        return (new LoanResource($loan->load(['book.author'])))->response()->setStatusCode(201);
    }

    public function returnBook($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update(['returned_at' => now()]);
        return (new LoanResource($loan->load('book.author')))->response();
    }

    public function destroy($id)
    {
        Loan::findOrFail($id)->delete();
        return response()->json(['message' => 'Loan deleted']);
    }
}
