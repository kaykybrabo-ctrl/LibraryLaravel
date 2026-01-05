<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$tables = \Illuminate\Support\Facades\Schema::getTableListing();
echo "Tables in database:\n";
echo "==================\n";
foreach ($tables as $table) {
    echo "- $table\n";
}
echo "\nChecking for failed_jobs table: ";
echo \Illuminate\Support\Facades\Schema::hasTable('failed_jobs') ? "EXISTS\n" : "NOT FOUND\n";
echo "\nChecking migrations:\n";
echo "==================\n";
