<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateFromSqlite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-from-sqlite';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy data from the old SQLite database file into the current MySQL database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting migration of data from SQLite to MySQL...');

        $sqlite = DB::connection('sqlite');
        $mysql = DB::connection('mysql');

        // Discover tables in the SQLite database
        $tables = $sqlite->select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'");

        if (empty($tables)) {
            $this->warn('No tables found in SQLite database.');
            return self::SUCCESS;
        }

        // Disable foreign key checks in MySQL during bulk insert
        $mysql->statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $row) {
            $table = $row->name ?? null;
            if (!$table) {
                continue;
            }

            if (!Schema::connection('mysql')->hasTable($table)) {
                $this->warn("[skip] Table '{$table}' does not exist in MySQL, skipping.");
                continue;
            }

            $count = $mysql->table($table)->count();
            if ($count > 0) {
                $this->warn("[skip] Table '{$table}' in MySQL is not empty ({$count} rows), skipping to avoid duplicates.");
                continue;
            }

            $this->info("[table] Migrating '{$table}'...");

            $rows = $sqlite->table($table)->get();
            if ($rows->isEmpty()) {
                $this->line("  - No rows to migrate for '{$table}'.");
                continue;
            }

            $inserted = 0;

            foreach ($rows as $record) {
                $mysql->table($table)->insert((array) $record);
                $inserted++;
            }

            $this->line("  - Inserted {$inserted} row(s) into '{$table}'.");
        }

        $mysql->statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('SQLite to MySQL data migration completed.');

        return self::SUCCESS;
    }
}
