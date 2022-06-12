<?php

namespace App\Repositories\Staff;

use App\Repositories\ApiRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Platform\Models\CreditRequest;

class CreditRequestRepository extends ApiRepository
{
    public function __construct(CreditRequest $model)
    {
        parent::__construct($model);
    }

    public function datatables(int $campaign_id, int $page = 1, int $perPage = 10, array $filters = [])
    {
        $collection = $this->model->query()
            ->where('campaign_id', $campaign_id)
            ->when($filters['status'], fn($query, $status) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(
                $perPage,
                ['*'],
                'page',
                $page
            );

        return [
            'data' => $collection->items(),
            'meta' => [
                'current_page' => $collection->currentPage(),
                'per_page' => $collection->perPage(),
                'total' => $collection->total(),
            ],
        ];
    }

    public function bulkUpdate(array $selected, string $status)
    {
        DB::beginTransaction();

        try {
            $this->model->query()
                ->where('status', CreditRequest::STATUS_PENDING)
                ->whereIn('uuid', $selected)
                ->update(['status' => $status]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Credit Requests {$status} successfully",
                'data' => NULL,
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'data' => NULL,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function bulkDelete(array $selected)
    {
        DB::beginTransaction();

        try {
            $this->model->whereIn('uuid', $selected)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Credit Requests deleted successfully',
                'data' => NULL,
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'data' => NULL,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
