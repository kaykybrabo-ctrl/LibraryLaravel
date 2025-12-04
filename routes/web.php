<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->file(__DIR__ . '/../public/home.html', [
        'Content-Type' => 'text/html; charset=utf-8',
        'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
});

Route::get('/login', function () {
    return response()->file(__DIR__ . '/../public/app-v2.html', [
        'Content-Type' => 'text/html; charset=utf-8',
        'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
});

Route::get('/register', function () {
    return response()->file(__DIR__ . '/../public/app-v2.html', [
        'Content-Type' => 'text/html; charset=utf-8',
        'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
});


Route::get('/app', function () {
    return response()->file(__DIR__ . '/../public/app-v2.html', [
        'Content-Type' => 'text/html; charset=utf-8',
        'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
});
