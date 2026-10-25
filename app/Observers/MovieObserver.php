<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class MovieObserver
{
    /**
     * Handle the Movie "created" event.
     */
    public function created(): void
    {
        $this->flush();
    }

    /**
     * Handle the Movie "updated" event.
     */
    public function updated(): void
    {
        $this->flush();
    }

    /**
     * Handle the Movie "deleted" event.
     */
    public function deleted(): void
    {
        $this->flush();
    }

    /**
     * Flush the cache when a movie is created, updated, deleted, or restored.
     *
     * @return void
     */
    private function flush(): void
    {
        Cache::flush();
    }
}
