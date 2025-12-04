<?php

namespace App\Listeners;

use App\Events\BookChanged;
use App\Jobs\RebuildBooksCacheJob;

class DispatchRebuildBooksCache
{
    public function handle(BookChanged $event): void
    {
        RebuildBooksCacheJob::dispatch();
    }
}
