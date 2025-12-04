<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class BookChanged
{
    use Dispatchable;

    public function __construct(public int $bookId)
    {
    }
}
