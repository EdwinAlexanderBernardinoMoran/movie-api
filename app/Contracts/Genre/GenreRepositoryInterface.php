<?php

namespace App\Contracts\Genre;

use App\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface GenreRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Filter genres based on the given criteria.
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $order
     * @return Collection
     */
    public function filter(array $filters, string $sortBy = 'name', string $order = 'asc'): Collection;

    /**
     * Find a genre by its slug or fail.
     *
     * @param string $slug
     * @return Model
     */
    public function findBySlugOrFail(string $slug): Model;

    /**
     * Restore a soft-deleted genre by its ID.
     *
     * @param int $id
     * @return Model
     */
    public function restore(int $id): Model;
}
