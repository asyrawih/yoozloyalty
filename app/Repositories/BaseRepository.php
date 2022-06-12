<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\ {
    Model,
    Collection
};
use App\Contracts\BaseRepositoryInterface;

class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $attributes) : Model
    {
        return $this->model->create($attributes);
    }

    public function all() : Collection
    {
        return $this->model->all();
    }

    public function find($id, $columns = ['*']) : ? Model
    {
        return $this->model->find($id, $columns);
    }

    public function where($column = 'id', $value = null, $option = 'first')
    {
        $query = $this->model->where($column, $value);

        if($option == 'all') {
            return $query->get();
        }

        return $query->first();
    }

    public function update($input, $id)
    {
        $data = $this->model->find($id);

        return $data->update($input);
    }

    public function delete($id)
    {
        $data = $this->model->find($id);

        return $data->delete();
    }

    public function makeModel()
    {
        return $this->model;
    }

}
