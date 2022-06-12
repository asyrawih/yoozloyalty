<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User;
use App\Models\PointsExpiry;
use Platform\Models\History;
use App\Customer;

class PointsExpirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create points expiry for merchants
        $merchants = User::select('id')
            ->whereRaw('id <> account_id')
            ->doesntHave('points_expiry')
            ->get();

        foreach($merchants as $merchant) {
            PointsExpiry::factory()->create([
                'merchant_user_id' => $merchant->id,
                'points_expiry' => 30
            ]);
        }

        // Check for unbalance points usage history against redeemed points history
        $customers = Customer::whereHas('history')
            ->with(['history' => function($query){
                $query->orderBy('points_expired_date')->orderBy('created_at');
            }])
            ->get()
            ->map(function($c){
                $points_redeemed = $c->history->where('points', '<', 0)->sum('points');

                $points_usage = $c->history->where('points', '>', 0)->sum('points_usage');

                if ($points_redeemed + $points_usage < 0) {
                    // If points redeemed is not balanced with points usage
                    $unbalanced_redeemed_points = (-1) * ($points_redeemed + $points_usage);

                    foreach($c->history->where('points', '>', 0) as $history) {
                        if ($unbalanced_redeemed_points <= 0) {
                            break;
                        }

                        $remaining_points = $history->points - $history->points_usage;
                        if (
                            $remaining_points > 0 &&
                            (
                                $history->points_expired_date == null ||
                                $history->points_expired_date > now()
                            )
                        ) {
                            // If there is still remaining points
                            if($remaining_points <= $unbalanced_redeemed_points) {
                                History::where('id', $history->id)->update([
                                    'points_usage' => $remaining_points + $history->points_usage
                                ]);

                                $unbalanced_redeemed_points -= $remaining_points;
                            } else {
                                // $remaining_points -= $unbalanced_redeemed_points;
                                History::where('id', $history->id)->update([
                                    'points_usage' => $unbalanced_redeemed_points + $history->points_usage
                                ]);

                                $unbalanced_redeemed_points = 0;
                            }
                        }
                    }
                }
            });
        /* Update expired date in history table according to merchant's points_expiry value
         * Only for positive (earned) points
         */
        History::where('points', '>', 0)->get()->map(function($hist){
            $expired_in_days = $hist->campaign->user->points_expiry->points_expiry;

            $points_expired_date = $hist->created_at->addDays($expired_in_days);

            if ($hist->points_expired_date == null) {
                History::where('id', $hist->id)->update(['points_expired_date' => $points_expired_date]);
            } else if (
                $hist->points_expired_date > now() && // Points still aplicable (not expired)
                $points_expired_date > $hist->points_expired_date // New expired date is greater than the current expired date
            ) {
                History::where('id', $hist->id)->update(['points_expired_date' => $points_expired_date]);
            } else {
                // Do nothing
                // Probably points are not applicable OR new expired date is less than current expired date
            }
        });
    }
}
