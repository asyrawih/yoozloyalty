<?php

namespace App\Libraries;

class Formula
{
    const FIXED_RATE = 'F';
    const PERCENT_RATE = 'P';

    /**
     * Formula for redeem customer points as transaction discount
     *
     * @param int $points Customer Points
     * @param float $amount Receipt Amount
     * @param float $conversion Redeem Conversion Rule
     *
     * @return array ['discount', 'amount_left', 'amount_return', 'point_cost']
     */
    public static function discount_transaction_with_points(
        int $points = 0,
        float $amount = 0.00,
        float $conversion = 0.00
    ): array
    {
        // CONVERT receipt amount TO points AS point cost
        $point_cost = ceil($amount / $conversion);

        // IF point cost GREATER THAN customer points
        if ($point_cost > $points) {
            // SET point cost WITH customer points
            $point_cost = $points;
        }

        // GET discount FROM point cost MULTIPLY conversion rule
        $discount = $point_cost * $conversion;

        // GET amount left FROM receipt amount SUBTRACT discount
        $amount_left = $amount - $discount;

        // SET amount return TO 0
        $amount_return = 0;

        // IF discount GREATER THAN receipt amount
        if ($discount > $amount) {
            // SET amount left TO 0
            $amount_left = 0;

            // GET amount return FROM discount SUBTRACT receipt amount
            $amount_return = $discount - $amount;
        }

        return compact(
            'discount',
            'amount_left',
            'amount_return',
            'point_cost'
        );
    }

    /**
     * Formual credit points
     *
     * @param float $amount Receipt Amount
     * @param int $rate Conversion Rate
     * @param string $rate_type Conversion Rate Type
     * @param bool $stepping_mode Stepping Mode
     * @param int $step_amount Step Amount
     *
     * @return float|int
     */
    public static function credit_points(
        float $amount = 0.00,
        int $rate = 0,
        string $rate_type = self::FIXED_RATE,
        bool $stepping_mode = FALSE,
        int $step_amount = 0,
    ): int {
        // stepping_mode equal TRUE
        if ($stepping_mode) {
            // GET step BY amount DIV step amount
            $step = ceil($amount / $step_amount);

            // GET result BY step multiply credit points rate
            return $step * self::credit_points_rate($amount, $rate, $rate_type);
        }

        // GET result BY credit points rate
        return self::credit_points_rate($amount, $rate, $rate_type);
    }

    /**
     * Formula to get credit points rate by amount and rate type
     *
     * @param float $amount Receipt Amount
     * @param int $rate Conversion Rate
     * @param string $rate_type Conversion Rate Type
     *
     * @return float|min
     */
    public static function credit_points_rate(
        float $amount = 0.00,
        int $rate = 0,
        string $rate_type = self::FIXED_RATE
    ): int {
        // IF rate_type equal percent
        if ($rate_type === self::PERCENT_RATE) {
            return (int) ceil($amount * ($rate / 100));
        }

        // return rate value IF rate_type equal fixed
        return $rate;
    }
}
