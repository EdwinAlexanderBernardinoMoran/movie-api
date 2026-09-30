<?php

namespace App\Repositories\Eloquent;

use App\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected Model $model)
    {
        //
    }

    /*
     * Get all records.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /*
     * Find a record by its ID or fail.
     *
     * @param int $id
     * @return Model
     */
    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /*
     * Create a new record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /*
     * Update an existing record.
     *
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model->refresh();
    }

    /*
     * Delete a record.
     *
     * @param Model $model
     * @return void
     */
    public function delete(Model $model): void
    {
        $model->delete();
    }
}
