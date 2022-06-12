<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\PlanStoreRequest;
use App\Http\Resources\PlanResource;
use CommerceGuys\Intl\Currency\CurrencyRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Platform\Models\Plan;

class PlanController extends Controller
{
    private CurrencyRepository $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function index(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $page = $request->input('page', 1);
        $sortBy = $request->input('sortBy', []);
        $sortDesc = $request->input('sortDesc', []);
        $search = $request->input('search', null);

        $plans = Plan::query()
            ->where('active', 1)
            ->when(! empty($search), function ($query) use($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when(
                (
                    count($sortBy) === 1 &&
                    count($sortDesc) === 1 &&
                    $sortBy[0] === 'price_formatted' &&
                    $sortDesc[0]
                ),
                fn($query) => $query->orderBy('price', 'desc'),
                fn($query) => $query->orderBy('price', 'asc')
            )
            ->paginate($itemsPerPage, ['*'], 'page', $page);

        return PlanResource::collection($plans);
    }

    public function initialize(Request $request)
    {
        $locale = $request->query('locale', config('system.default_language'));

        app()->setlocale($locale);

        $currencies = $this->currencyRepository->getAll();

        $currency_codes = [];
        $currency_symbols = [];

        foreach ($currencies as $currency_code => $currency) {
            $currency_codes[$currency_code] = "{$currency->getName()} ({$currency_code})";

            $currency_symbols[$currency_code] = $currency->getSymbol();
        }

        return response()->json(compact(
            'currency_codes',
            'currency_symbols'
        ));
    }

    public function store(PlanStoreRequest $request)
    {
        try {
            $plan = new \Platform\Models\Plan;
            $plan->name = $request->name;
            $plan->currency_code = $request->currency_code;
            $plan->role = 3;
            $plan->price = (int) $request->price * 100;
            $plan->billing_interval = 'month';
            $plan->limitations = [
                'customers' => $this->setUnlimited($request->is_unlimited_customers, $request->limitations_customers),
                'campaigns' => $request->limitations_campaigns,
                'rewards' => $this->setUnlimited($request->is_unlimited_rewards, $request->limitations_rewards),
                'businesses' => $this->setUnlimited($request->is_unlimited_businesses, $request->limitations_businesses),
                'staff' => $this->setUnlimited($request->is_unlimited_staff, $request->limitations_staff),
                'segments' => $this->setUnlimited($request->is_unlimited_segments, $request->limitations_segments),
            ];
            $plan->product_id_paddle = $request->product_id_paddle;
            $plan->product_id_2checkout = $request->product_id_2checkout;
            $plan->product_id_stripe = $request->product_id_stripe;
            $plan->product_id_paypal = $request->product_id_paypal;
            $plan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data has been created.'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function show(int $id)
    {
        return new PlanResource(Plan::query()->find($id));
    }

    public function update(PlanStoreRequest $request, int $id)
    {
        try {
            $plan = Plan::query()->findOrFail($id);

            $plan->name = $request->name;
            $plan->currency_code = $request->currency_code;
            $plan->price = (int) $request->price * 100;
            $plan->limitations = [
                'customers' => $this->setUnlimited($request->is_unlimited_customers, $request->limitations_customers),
                'campaigns' => $request->limitations_campaigns,
                'rewards' => $this->setUnlimited($request->is_unlimited_rewards, $request->limitations_rewards),
                'businesses' => $this->setUnlimited($request->is_unlimited_businesses, $request->limitations_businesses),
                'staff' => $this->setUnlimited($request->is_unlimited_staff, $request->limitations_staff),
                'segments' => $this->setUnlimited($request->is_unlimited_segments, $request->limitations_segments),
            ];
            $plan->product_id_paddle = $request->product_id_paddle;
            $plan->product_id_2checkout = $request->product_id_2checkout;
            $plan->product_id_stripe = $request->product_id_stripe;
            $plan->product_id_paypal = $request->product_id_paypal;
            $plan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data has been updated.'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function destroy(int $id)
    {
        try {
            $plan = Plan::query()->findOrFail($id);

            if ($plan->user_count > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to delete data, because there are still users in the plan period.'
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $plan->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data has been deleted.'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function massdelete(Request $request)
    {
        DB::beginTransaction();

        try {
            foreach ($request->plans as $uuid) {
                $plan = Plan::withoutGlobalScopes()->whereUuid($uuid)->first();

                if ($plan->user_count > 0) {
                    throw new Exception('Unable to delete data, because there are still users in the plan period.');
                }

                $plan->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'All selected plan has been deleted.'
            ]);

        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    private function setUnlimited($isUnlimited, $default)
    {
        if (boolval($isUnlimited)) {
            return 'unlimited';
        }

        return intval($default);
    }
}
