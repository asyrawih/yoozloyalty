<?php

namespace App\Repositories\Admin;

use App\Models\BankAccountType;
use App\Repositories\ApiRepository;

class BankAccountTypeRepository extends ApiRepository
{
    public function __construct(BankAccountType $model)
    {
        parent::__construct($model);
    }

    public function getActiveTypes()
    {
        return $this->model->query()->where('is_active', 1)->orderBy('name')->get();
    }

    public function datatables(int $page = 1, int $pageSize = 10, array $filter = [])
    {
        $collection = $this->model->query()
            ->orderBy('name')
            ->paginate($pageSize, ['*'], 'page', $page);

        return [
            'data' => $collection->items(),
            'meta' => [
                'current_page' => $collection->currentPage(),
                'per_page' => $collection->perPage(),
                'total' => $collection->total(),
            ],
        ];
    }
}
