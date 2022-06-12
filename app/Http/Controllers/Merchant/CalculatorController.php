<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Merchant\Calculator\CreditPointsRequest;
use App\Libraries\Formula;

class CalculatorController extends Controller
{
    public function credit_points(CreditPointsRequest $request)
    {
        $amount = floatval($request->amount);

        $rules = collect($request->rules);

        $points = 0;

        if ($amount > 0) {
            $rule = $rules
                ->when($request->mode === 'range', function ($collection) use($amount) {
                    return $collection->where('min_amount', '<=', $amount)
                            ->where('max_amount', '>=', $amount);
                })->when($request->mode === 'step', function ($collection) use($amount) {
                    return $collection->where('min_amount', '<=', $amount)
                            ->sortByDesc('min_amount');
                })->first();

            if (! $rule) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please contact website administrator to setup the Conversion Rule.',
                    'errors' => [
                        'amount' => 'Please add Conversion Rule for this amout.',
                    ]
                ], 422);
            }

            $points = Formula::credit_points(
                $amount,
                $rule['rate'],
                $rule['type'],
                $rule['stepping_mode'],
                $rule['step_amount']
            );
        }

        return response()->json([
            'status' => 'success',
            'points' => $points,
        ]);
    }
}
