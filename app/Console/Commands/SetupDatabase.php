<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-db {--fresh : Drop all tables and re-run all migrations before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run database migrations and seeders for the application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $fresh = (bool) $this->option('fresh');

        if ($fresh) {
            $this->info('Running migrate:fresh --seed ...');
            Artisan::call('migrate:fresh', ['--force' => true]);
        } else {
            $this->info('Running migrate ...');
            Artisan::call('migrate', ['--force' => true]);
        }

        $this->output->write(Artisan::output());

        $this->info('Running db:seed ...');
        Artisan::call('db:seed', ['--force' => true]);
        $this->output->write(Artisan::output());

        $this->info('Database setup completed successfully.');

        return self::SUCCESS;
    }
}
