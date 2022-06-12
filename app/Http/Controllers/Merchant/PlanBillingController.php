<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Merchant\BillingCheckoutRequest;
use App\Http\Requests\Merchant\BillingConfirmRequest;
use App\Models\ {
    Bank,
    PaymentMethod
};
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Merchant\BillingRepository;
use Exception;
use Illuminate\Http\JsonResponse;

class PlanBillingController extends Controller
{
    const allowedImageFileExtension = ['pdf'];

    private array $settings;
    private BillingRepository $billingRepository;

    public function __construct(BillingRepository $billingRepository)
    {
        $this->settings = json_decode(file_get_contents(storage_path('json/payment-methods/settings.json')), true);

        $this->billingRepository = $billingRepository;
    }

    /**
     * @return JsonResponse
     */
    public function initialize(): JsonResponse
    {
        $user = User::query()->find(Auth::id());
        try {
            $payment_methods = PaymentMethod::select('name as text', 'blueprint as value', 'blueprint')
                ->where('is_active', 1)
                ->where(function($query) use ($user) {
                    $query->whereNull('currency')->orWhere('currency', 'LIKE', "%$user->currency_code%");
                })
                ->get()
                ->toArray();
            $cheques = [];
            $savings = [];

            foreach ($payment_methods as $pm) {
                if ($pm['blueprint'] == 'cheque')
                    $cheques = Bank::query()
                        ->whereActive()
                        ->get()
                        ->map(fn($item) => [
                            'text' => $item->bank_name,
                            'value' => $item->id
                        ])->toArray();
                

                if ($pm['blueprint'] == 'bank_transfer')
                    $savings = Bank::query()
                        ->whereActive()
                        ->get()
                        ->map(fn($item) => [
                            'text' => $item->bank_name,
                            'value' => $item->id
                        ])->toArray();                
            }

            return response()->json(compact('payment_methods', 'cheques', 'savings'));
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'payment_methods' => [],
                'cheques' => [],
                'savings' => []
            ]);
        }
    }

    public function plans()
    {
        try {
            return response()->json($this->billingRepository->plans());
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function stat()
    {
        try {
            return response()->json($this->billingRepository->stat());
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function checkout(BillingCheckoutRequest $request)
    {
        try {
            $response = $this->billingRepository->checkout(
                $request->plan_id,
                $request->payment_method,
                $request->bank_id,
                $request->amount,
                $request->currency,
                $request->action
            );

            return response()->json($response);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function confirm(BillingConfirmRequest $request)
    {
        try {
            $receipt = null;

            if ($request->hasFile('receipt')) {
                $receipt = $request->file('receipt');

                $extension = $receipt->extension();

                if (! in_array($extension, self::allowedImageFileExtension)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File not allowed to upload.',
                        'data' => null
                    ], 422);
                }
            }

            $response = $this->billingRepository->confirm(
                $request->order_id,
                $request->paid_at,
                $request->merchant_identifier,
                $request->merchant_bank_name,
                $request->amount_paid,
                $receipt
            );

            return response()->json($response);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function cancel($order)
    {
        try {
            $response = $this->billingRepository->canceled($order);

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
