<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ApiRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*'], string $sortBy = 'id', bool $sortDesc = false): Collection
    {
        return $this->model->orderBy($sortBy, $sortDesc ? 'desc' : 'asc')->get($columns);
    }

    public function create(array $attributes): ?Model
    {
        $model = $this->model->query()->create($attributes);

        return $model->fresh();
    }

    public function read(
        int|string $id,
        array $columns = ['*'],
        array $relations = [],
        array $appends = [],
    ): ?Model
    {
        return $this->model->query()
            ->with($relations)
            ->findOrFail($id, $columns)
            ->append($appends);
    }

    public function update(int|string $id, array $input): bool
    {
        return $this->read($id)->update($input);
    }

    public function delete(int|string $id): bool
    {
        return $this->read($id)->delete();
    }
}
