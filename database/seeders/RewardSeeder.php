<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rewards = \Platform\Models\Reward::all();

        foreach ($rewards as $reward_offer) {
            $reward = \Platform\Models\Reward::find($reward_offer->id);
            if($reward->active_from == null) {
                $reward->active_from = $reward->created_at;
            }
            if($reward->expires_at == null) {
                $reward->expires_at = $reward->created_at->addMonths(18);
            }
            $reward->save();
        }
    }
}
