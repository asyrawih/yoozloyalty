<?php

namespace App\Repositories\Campaign;

use App\Libraries\Formula;
use Platform\Models\CreditRuleConversion;

class PointsConversionRuleRepository
{
    public function credit_points(
        int $campaign_id,
        float $amount = 0.00,
        string $mode = 'range'
    ) {
        $points = 0;

        if ($amount > 0) {
            $conversionRule = CreditRuleConversion::query()
                ->where('campaign_id', $campaign_id)
                ->where('mode', $mode)
                ->when($mode === 'range', function ($query) use($amount) {
                    $query->where('min_amount', '<=', $amount)
                        ->where('max_amount', '>=', $amount);
                })
                ->when($mode === 'step', function ($query) use($amount) {
                    $query->where('min_amount', '<=', $amount)
                        ->orderByDesc('min_amount');
                })
                ->first();

            if (! $conversionRule) {
                return [
                    'status' => 'error',
                    'points' => $points,
                ];
            }

            $points = Formula::credit_points(
                $amount,
                $conversionRule->rate,
                $conversionRule->type,
                $conversionRule->stepping_mode,
                $conversionRule->step_amount
            );
        }

        return [
            'status' => 'success',
            'points' => $points,
        ];
    }
}
