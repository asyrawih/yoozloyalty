<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\ {
    Model,
    Collection
};

interface BaseRepositoryInterface
{
	public function create(array $attributes) : Model;

	public function all() : ? Collection;

	public function find($id, $columns) : ? Model;

	public function where($column, $value, $option);

	public function delete($id);

	public function update($input, $id);

	public function makeModel();

}
