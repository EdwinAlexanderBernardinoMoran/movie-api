<?php

namespace App\Repositories\Genre;

use App\Contracts\Genre\GenreRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class GenreRepository extends BaseRepository implements GenreRepositoryInterface
{
    private const SORTABLE = ['name', 'created_at', 'updated_at'];
    /**
     * Create a new class instance.
     */
    public function __construct(Genre $model)
    {
        parent::__construct($model);
    }

    /**
     * Filter genres based on the given criteria.
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $order
     * @return Collection
     */
    public function filter(array $filters, string $sortBy = 'name', string $order = 'asc'): Collection
    {
        $sortBy = in_array($sortBy, self::SORTABLE) ? $sortBy : 'name';
        $order = strtolower($order) === 'desc' ? 'desc' : 'asc';

        return $this->model->query()
            ->when(isset($filters['search']),
                fn($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($filters['search']) . '%'])
            )
            ->when(isset($filters['is_active']),
                fn($q) => $q->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN))
            )
            ->orderBy($sortBy, $order)
            ->get();
    }

    /**
     * Find a genre by its slug or fail.
     *
     * @param string $slug
     * @return Model
     */
    public function findBySlugOrFail(string $slug): Model
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }

    /**
     * Restore a soft-deleted genre by its ID.
     *
     * @param int $id
     * @return Model
     */
    public function restore(int $id): Model
    {
        $genre = $this->model->withTrashed()->findOrFail($id);
        $genre->restore();
        return $genre->refresh();
    }
}

