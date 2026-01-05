<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo "Authors: " . \App\Models\Author::count() . PHP_EOL;
echo "Books: " . \App\Models\Book::count() . PHP_EOL;
echo "Users: " . \App\Models\User::count() . PHP_EOL;
echo PHP_EOL . "Sample authors:" . PHP_EOL;
$authors = \App\Models\Author::take(3)->get();
foreach ($authors as $author) {
    echo "- " . $author->name . PHP_EOL;
}
echo PHP_EOL . "Sample books:" . PHP_EOL;
$books = \App\Models\Book::with('author')->take(3)->get();
foreach ($books as $book) {
    echo "- " . $book->title . " (by " . $book->author->name . ")" . PHP_EOL;
}
