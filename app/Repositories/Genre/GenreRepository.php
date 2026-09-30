<?php

namespace App\Repositories\Genre;

use App\Contracts\Genre\GenreRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use App\Models\Genre;

class GenreRepository extends BaseRepository implements GenreRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(Genre $model)
    {
        parent::__construct($model);
    }
}

