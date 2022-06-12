<?php

namespace App\Http\Controllers\Merchant;

use Exception;
use App\Customer;
use Illuminate\Http\Request;
use Platform\Models\History;
use Platform\Models\Campaign;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Platform\Models\CreditRequest;
use App\Http\Controllers\Controller;
use App\Imports\CreditRequestImport;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\NotifPusherRepositories;
use App\Repositories\Staff\CreditRequestRepository;
use App\Repositories\Campaign\PointsConversionRuleRepository;
use Maatwebsite\Excel\Facades\Excel;

class CreditRequestController extends Controller
{
    private CreditRequestRepository $creditRequest;
    private PointsConversionRuleRepository $pointsConversionRule;
    private EmailTemplateRepository $emailTemplate;
    private NotifPusherRepositories $notifPusher;

    const EVENT_NAME = 'Credit Request';

    public function __construct(
        CreditRequestRepository $creditRequest,
        PointsConversionRuleRepository $pointsConversionRule,
        EmailTemplateRepository $emailTemplate,
        NotifPusherRepositories $notifPusher
    ) {
        $this->creditRequest = $creditRequest;
        $this->pointsConversionRule = $pointsConversionRule;
        $this->emailTemplate = $emailTemplate;
        $this->notifPusher = $notifPusher;
    }

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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = request('page', 1);
        $perPage = request('perPage', 10);
        $filters = request('filters', []);

        $collection = CreditRequest::query()
            ->with(['customer', 'campaign'])
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $customerNumber = str_replace("-", "", $request->number);

            $campaign = Campaign::withoutGlobalScopes()
                ->whereUuid($request->uuid)
                ->firstOrFail();

            $customer = Customer::query()
                ->where('campaign_id', $campaign->id)
                ->where('customer_number', $customerNumber)
                ->where('active', 1)
                ->firstOrFail();

            // Check if bill number is exist
            $checkReceiptNumber = CreditRequest::query()
                ->where('receipt_number', $request->receipt_number)
                ->first();

            if ($checkReceiptNumber) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'receipt_number' => [
                            'Receipt number already used.',
                        ],
                    ],
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
                    'message' => NULL,
                    'errors' => [
                        'number' => [
                            'The customer has reached the credit transaction limit.',
                        ],
                    ],
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            // GET credit points
            $response = $this->pointsConversionRule->credit_points(
                $campaign->id,
                floatval($request->receipt_amount),
                $campaign->credit_points_mode
            );

            if ($response['status'] === 'error') {
                return response()->json([
                    'status' => 'error',
                    'message' => NULL,
                    'errors' => [
                        'receipt_amount' => [
                            'Please contact website administrator to setup the Conversion Rule.'
                        ],
                    ]
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $points = $response['points'];

            DB::beginTransaction();

            $creditRequest = new CreditRequest;

            $creditRequest->customer_id = $customer->id;
            $creditRequest->campaign_id = $campaign->id;
            $creditRequest->receipt_number = $request->receipt_number;
            $creditRequest->receipt_amount = $request->receipt_amount;
            $creditRequest->points = $points;
            $creditRequest->status = CreditRequest::STATUS_APPROVED;
            $creditRequest->created_by = $campaign->created_by;
            $creditRequest->updated_by = $campaign->created_by;

            $creditRequest->save();

            $history = new History;

            $history->customer_id = $creditRequest->customer_id;
            $history->campaign_id = $creditRequest->campaign_id;
            $history->points = $creditRequest->points;
            $history->bill_number = $creditRequest->receipt_number;
            $history->bill_amount = $creditRequest->receipt_amount;
            $history->event = self::EVENT_NAME;
            $history->created_by = $campaign->created_by;
            $history->points_expired_date = Carbon::now()->addDays($campaign->user->points_expiry->points_expiry ?? config('system.default_points_expiry'));

            $history->save();

            $this->emailTemplate->customerCreditPoint(
                $campaign,
                $customer,
                $points,
                self::EVENT_NAME
            );


            if ($this->notifPusher->isAvailable()) {
                $this->notifPusher->pointCredited(
                    $customer,
                    $history->points,
                    self::EVENT_NAME
                );
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Credit request has been created.',
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $status = CreditRequest::STATUS_REJECTED;

            $creditRequest = $this->creditRequest->read($id, ['*'], ['campaign', 'customer']);

            if ($request->status === CreditRequest::STATUS_APPROVED) {
                $status = CreditRequest::STATUS_APPROVED;

                $campaign = $creditRequest->campaign;
                $customer = $creditRequest->customer;

                $history = new History;
                $history->customer_id = $customer->id;
                $history->campaign_id = $campaign->id;
                $history->points = $creditRequest->points;
                $history->event = self::EVENT_NAME;
                $history->bill_number = $creditRequest->receipt_number;
                $history->bill_amount = $creditRequest->receipt_amount;
                $history->created_by = $campaign->created_by;
                $history->points_expired_date = Carbon::now()->addDays($campaign->user->points_expiry->points_expiry ?? config('system.default_points_expiry'));

                $history->save();

                $this->emailTemplate->customerCreditPoint(
                    $campaign,
                    $customer,
                    $creditRequest->points,
                    self::EVENT_NAME
                );


                if ($this->notifPusher->isAvailable()) {
                    $this->notifPusher->pointCredited(
                        $customer,
                        $history->points,
                        self::EVENT_NAME
                    );
                }
            }

            $creditRequest->status = $status;

            $creditRequest->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Credit request has been {$status}.",
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $this->creditRequest->delete($id);

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
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function import(Request $request)
    {
        try {
            $file = $request->file('file');

            Excel::import(new CreditRequestImport(
                $request->uuid,
                $this->pointsConversionRule,
                $this->notifPusher,
                $this->emailTemplate
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

    public function bulkActions(Request $request)
    {
        $action = $request->action;
        $selected = $request->selected;

        switch ($action) {
            case 'deleted':
                return $this->creditRequest->bulkDelete($selected);
                break;
            case 'approved':
                return $this->creditRequest->bulkUpdate($selected, $action);
                break;
            case 'rejected':
                return $this->creditRequest->bulkUpdate($selected, $action);
                break;
            default:
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid action.',
                ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
