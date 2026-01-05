<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo "Sample books with Portuguese descriptions:\n" . str_repeat("=", 50) . "\n";
$books = \App\Models\Book::with('author')->take(10)->get();
foreach ($books as $book) {
    echo "\n📖 " . $book->title . "\n";
    echo "   Author: " . $book->author->name . "\n";
    echo "   Description: " . $book->description . "\n";
    echo "   Price: R$ " . number_format($book->price, 2, ',', '.') . "\n";
    echo str_repeat("-", 40) . "\n";
}
