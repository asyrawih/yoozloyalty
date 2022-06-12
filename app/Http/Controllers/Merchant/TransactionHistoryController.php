<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Imports\TransactionHistoryImport;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Platform\Models\Campaign;
use Platform\Models\History;

class TransactionHistoryController extends Controller
{
    public function campaigns()
    {
        $campaigns = Campaign::query()->where('created_by', auth()->id())
            ->orderBy('name')
            ->get()
            ->map(function ($campaign) {
                return [
                    'value' => $campaign->uuid,
                    'text' => $campaign->name,
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => NULL,
            'data' => $campaigns->toArray(),
        ]);
    }

    public function index()
    {
        $page = request('page', 1);
        $perPage = request('perPage', 10);

        $collection = History::query()
            ->with(['customer', 'campaign'])
            ->where('created_by', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(
                $perPage,
                ['*'],
                'page',
                $page
            );

        return response()->json([
            'status' => 'success',
            'message' => NULL,
            'data' => $collection->items(),
            'meta' => [
                'current_page' => $collection->currentPage(),
                'per_page' => $collection->perPage(),
                'total' => $collection->total(),
            ],
        ]);
    }

    public function import(Request $request)
    {
        try {
            $file = $request->file('file');

            Excel::import(new TransactionHistoryImport(
                $request->uuid,
            ), $file);

            return response()->json([
                'status' => 'success',
                'message' => 'Import file has been uploaded.'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
