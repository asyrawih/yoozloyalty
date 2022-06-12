<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Repositories\Campaign\CampaignRepository;
use App\Repositories\Staff\CreditRequestRepository;
use Illuminate\Http\Request;
use Platform\Models\CreditRequest as ModelsCreditRequest;

class CreditRequest extends Controller
{
    private CampaignRepository $campaign;
    private CreditRequestRepository $creditRequest;

    public function __construct(
        CampaignRepository $campaign,
        CreditRequestRepository $creditRequest
    ) {
        $this->campaign = $campaign;
        $this->creditRequest = $creditRequest;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $page =  $request->query('page', 1);
        $perPage = $request->query('perPage', 10);
        $filters = $request->query('filters', ['status' => '']);

        $collection = ModelsCreditRequest::query()
            ->where('created_by', auth()->id())
            ->when($filters['status'], fn($query, $status) => $query->where('status', $status))
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
}
