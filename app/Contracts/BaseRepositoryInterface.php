<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    /*
     * Get all records.
     *
     * @return Collection
     */
    public function all(): Collection;

    /*
     * Find a record by its ID or fail.
     *
     * @param int $id
     * @return Model
     */
    public function findOrFail(int $id): Model;

    /*
     * Create a new record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /*
     * Update an existing record.
     *
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function update(Model $model, array $data): Model;

    /*
     * Delete a record.
     *
     * @param Model $model
     * @return void
     */
    public function delete(Model $model): void;
}
