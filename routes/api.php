<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

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
