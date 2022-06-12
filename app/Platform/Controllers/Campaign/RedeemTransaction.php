<?php

namespace Platform\Controllers\Campaign;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Platform\Models\RedeemTransactionRule;

/**
 * @group Email Template
 *
 * Endpoints to get a user's email template.
 * @package Platform\Controllers\Campaign
 */
class RedeemTransaction extends Controller
{
    /**
     * Fetch all user's email template
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $rule = RedeemTransactionRule::query()
            ->where('user_id', auth()->id())
            ->first();

        if (! $rule) {
            $rule = new RedeemTransactionRule;
            $rule->value = 0;
            $rule->save();
        }

        return response()->json([
            'status' => 'success',
            'data' => $rule
        ]);
    }

    /**
     * Update user's email template
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {

        // Validate
        $validate = Validator::make($request->only(['value', 'minimum_points', 'maximum_redeem']), [
            'value' => 'required',
            'minimum_points' => 'required',
            'maximum_redeem' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
              'status' => 'error',
              'errors' => $validate->errors()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        RedeemTransactionRule::query()->whereUuid($request->uuid)->update([
            'value' => $request->value,
            'minimum_points' => $request->minimum_points,
            'maximum_redeem' => $request->maximum_redeem,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
