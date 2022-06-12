<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\BillingInvoice;
use App\Repositories\Merchant\BillingRepository;
use Exception;
use Illuminate\Http\Request;

class PlanOrderController extends Controller
{
    private BillingRepository $billingRepository;

    public function __construct(BillingRepository $billingRepository)
    {
        $this->billingRepository = $billingRepository;
    }

    public function index(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $page = $request->input('page', 1);
        $sortBy = $request->input('sortBy', []);
        $sortDesc = $request->input('sortDesc', []);

        try {
            return $this->billingRepository->orders(compact(
                'itemsPerPage',
                'page',
                'sortBy',
                'sortDesc'
            ));
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function approved($order)
    {
        try {
            $response = $this->billingRepository->approved($order);

            return response()->json($response);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function rejected($order)
    {
        try {
            $response = $this->billingRepository->rejected($order);

            return response()->json($response);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
