<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class GenreObserver
{
    /**
     * Handle the Genre "created" event.
     */
    public function created(): void
    {
        $this->flush();
    }

    /**
     * Handle the Genre "updated" event.
     */
    public function updated(): void
    {
        $this->flush();
    }

    /**
     * Handle the Genre "deleted" event.
     */
    public function deleted(): void
    {
        $this->flush();
    }

    /**
     * Handle the Genre "restored" event.
     */
    public function restored(): void
    {
        $this->flush();
    }

    /**
     * Flush the cache when a genre is created, updated, deleted, or restored.
     *
     * @return void
     */
    private function flush(): void
    {
        Cache::flush();
    }
}
