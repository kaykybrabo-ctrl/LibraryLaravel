<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        $hasReturnedAtIndex = false;
        $hasBookReturnedAtIndex = false;
        $hasUserReturnedAtIndex = false;

        if ($driver === 'sqlite') {
            $indexes = $connection->select("PRAGMA index_list('loans')");
            foreach ($indexes as $index) {
                $name = $index->name ?? null;
                if ($name === 'loans_returned_at_index') {
                    $hasReturnedAtIndex = true;
                } elseif ($name === 'loans_book_id_returned_at_index') {
                    $hasBookReturnedAtIndex = true;
                } elseif ($name === 'loans_user_id_returned_at_index') {
                    $hasUserReturnedAtIndex = true;
                }
            }
        } elseif ($driver === 'mysql') {
            $database = $connection->getDatabaseName();
            $indexes = $connection->select(
                'SELECT INDEX_NAME as name FROM information_schema.statistics WHERE table_schema = ? AND table_name = ?',
                [$database, 'loans']
            );

            foreach ($indexes as $index) {
                $name = $index->name ?? null;
                if ($name === 'loans_returned_at_index') {
                    $hasReturnedAtIndex = true;
                } elseif ($name === 'loans_book_id_returned_at_index') {
                    $hasBookReturnedAtIndex = true;
                } elseif ($name === 'loans_user_id_returned_at_index') {
                    $hasUserReturnedAtIndex = true;
                }
            }
        }

        Schema::table('loans', function (Blueprint $table) use (
            $hasReturnedAtIndex,
            $hasBookReturnedAtIndex,
            $hasUserReturnedAtIndex
        ) {
            if (!$hasReturnedAtIndex) {
                $table->index('returned_at');
            }
            if (!$hasBookReturnedAtIndex) {
                $table->index(['book_id', 'returned_at']);
            }
            if (!$hasUserReturnedAtIndex) {
                $table->index(['user_id', 'returned_at']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropIndex(['returned_at']);
            $table->dropIndex(['book_id', 'returned_at']);
            $table->dropIndex(['user_id', 'returned_at']);
        });
    }
};
