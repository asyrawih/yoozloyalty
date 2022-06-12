<?php

namespace Platform\Controllers\Campaign;

use Exception;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Platform\Models\CreditRequest;
use App\Repositories\Campaign\PointsConversionRuleRepository;
use Illuminate\Support\Carbon;
use Platform\Models\History;

use Platform\Models\Campaign;

/**
 * @group Customer Request Credit
 * APIs for customer request credit
 *
 */
class CreditRequestController extends \App\Http\Controllers\Controller
{
    private PointsConversionRuleRepository $pointsConversionRule;


    public function __construct(
        PointsConversionRuleRepository $pointsConversionRule,
    ) {
        $this->pointsConversionRule = $pointsConversionRule;
    }

    public function getAllCreditRequestsByCustomerId()
    {
        $campaign = Campaign::withoutGlobalScopes()->whereUuid(
            request('uuid', 0)
        )->firstOrFail();

        $auth_customer = auth()->user();

        $customer = Customer::query()
            ->where('campaign_id', $campaign->id)
            ->where('customer_number', $auth_customer->customer_number)
            ->where('active', 1)
            ->firstOrFail();

        $page = request('page', 1);
        $perPage = request('perPage', 10);

        $collection = CreditRequest::query()
            ->where('campaign_id', $campaign->id)
            ->where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'message' => null,
            'data' => $collection->items(),
            'meta' => [
                'current_page' => $collection->currentPage(),
                'per_page' => $collection->perPage(),
                'total' => $collection->total(),
            ],
        ]);
    }

    /**
     * Customer request credit
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam bill_amount integer required points of request.
     * @bodyParam bill_number integer required points of request.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        try {

            $campaign = Campaign::withoutGlobalScopes()->whereUuid(
                request('uuid', 0)
            )->firstOrFail();

            $auth_customer = $request->user('customer');

            // Find customer by number
            $customer = Customer::query()
                ->where('campaign_id', $campaign->id)
                ->where('customer_number', $auth_customer->customer_number)
                ->where('active', 1)
                ->firstOrFail();

            // Check if bill number is exist
            $checkReceiptNumber = CreditRequest::query()->where(
                'receipt_number', $request->bill_number
            )->first();

            if ($checkReceiptNumber) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'bill_number' => 'Bill number already used.'
                    ]
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $countHistoryToday = History::query()
                ->where('campaign_id', $campaign->id)
                ->where('customer_id', $customer->id)
                ->where('points', '>' , 0)
                ->where('event', '!=', 'Sign up bonus')
                ->whereDate('created_at', Carbon::today())
                ->count();

            if ($countHistoryToday >= $campaign->credit_transaction_limit) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'code' => 'The customer has reached the credit transaction limit.'
                    ]
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            // GET credit points
            $response = $this->pointsConversionRule->credit_points(
                $campaign->id,
                floatval($request->bill_amount),
                $campaign->credit_points_mode
            );

            if ($response['status'] === 'error') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please contact website administrator to setup the Conversion Rule.',
                    'errors' => [
                        'bill_amount' => 'Please contact website administrator to setup the Conversion Rule.',
                    ]
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $points = $response['points'];

            $creditRequest = new CreditRequest;

            $creditRequest->customer_id = $customer->id;
            $creditRequest->campaign_id = $campaign->id;
            $creditRequest->receipt_number = $request->bill_number;
            $creditRequest->receipt_amount = $request->bill_amount;
            $creditRequest->points = $points;
            $creditRequest->status = CreditRequest::STATUS_PENDING;
            $creditRequest->created_by = $campaign->created_by;
            $creditRequest->updated_by = $campaign->created_by;

            $creditRequest->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Merchant will Approve your Credit Request.',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
