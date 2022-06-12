<?php

namespace App\Http\Controllers\Merchant\Setting;

use App\Http\Controllers\Controller;
use App\Models\BillingInvoice;
use Exception;
use Illuminate\Http\Request;
use App\User;
use App\Repositories\Merchant\PlanChangeRequestRepository;

class PlanChangeRequestHistoryController extends Controller
{
    private PlanChangeRequestRepository $planChangeRequestRepository;

    public function __construct(PlanChangeRequestRepository $planChangeRequestRepository)
    {
        $this->planChangeRequestRepository = $planChangeRequestRepository;
    }

    public function index(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $page = $request->input('page', 1);
        $sortBy = $request->input('sortBy', []);
        $sortDesc = $request->input('sortDesc', []);

        try {
            return $this->planChangeRequestRepository->history(compact(
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

        $invoices = BillingInvoice::all();
        return $invoices->map(function($invoice){
            $timezone = ($invoice->user->timezone?$invoice->user->timezone:'UTC');
            return [
                'order_id' => $invoice->order_id,
                'merchant_name' => $invoice->merchant_name,
                'previous_plan_name' => $invoice->previous_plan_name,
                'plan_name' => $invoice->plan_name,
                'status' => $invoice->status,
                'created_at' => $invoice->created_at->shiftTimezone($timezone)->format('d F Y, H:i'),
                'timezone' => $timezone
            ];
        });
    }
}
