<?php

namespace App\Repositories;

use App\Http\Resources\BankResource;
use App\Models\Bank;
use Illuminate\Support\Facades\Validator;

class BankRepository extends BaseRepository
{
    public function __construct(Bank $model)
    {
        $this->model = $model;
    }

    public function datatables(array $params)
    {
        $banks = $this->model::query()
            ->when(! empty($params['search']), function ($query) use($params) {
                $query->where('bank_name', 'like', "%{$params['search']}%");
            })
            ->paginate($params['itemsPerPage'], ['*'], 'page', $params['page']);

        return BankResource::collection($banks);
    }

    public function store(array $payload)
    {
        $validator = Validator::make($payload, [
            'bank_name' => 'required',
            'branch_name' => 'required',
            'branch_code' => 'required',
            'account_name' => 'required',
            'account_type_id' => 'required',
            'account_number' => 'required',
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $this->model->query()->create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been created.'
        ]);
    }

    public function modify(array $payload, int $id)
    {
        $validator = Validator::make($payload, [
            'bank_name' => 'required',
            'branch_name' => 'required',
            'branch_code' => 'required',
            'account_name' => 'required',
            'account_type' => 'required|in:chequing,savings',
            'account_number' => 'required',
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $this->model->query()
            ->find($id)
            ->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been updated.'
        ]);
    }

    public function destroy(int $id)
    {
        $bank = $this->model->query()->find($id);

        if ($bank) {
            $bank->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been deleted.'
        ]);
    }

    private function checkDoubleBank(string $account_number, string $account_type)
    {
        $bank = $this->model->query()
            ->where('account_number', $account_number)
            ->where('account_type', $account_type)
            ->first();

        if ($bank) {
            return true;
        }

        return false;
    }
}
