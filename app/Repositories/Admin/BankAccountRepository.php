<?php

namespace App\Repositories\Admin;

use App\Models\Bank;
use App\Repositories\ApiRepository;

class BankAccountRepository extends ApiRepository
{
    public function __construct(Bank $model)
    {
        parent::__construct($model);
    }

    public function datatables(int $page = 1, int $pageSize = 10, array $filter = [])
    {
        $collection = $this->model->query()
            ->with('accountType')
            ->orderBy('bank_name')
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
