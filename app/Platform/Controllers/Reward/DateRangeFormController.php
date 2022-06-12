<?php

namespace Platform\Controllers\Reward;

use App\Http\Controllers\Controller;
use Platform\Models\Reward;
use Illuminate\Http\Request;
use Platform\Models\CampaignReward;

/**
 * @group Reward Date Range Form
 *
 * Endpoints for customer related to reward date range.
 * @package Platform\Controllers\Reward
 */
class DateRangeFormController extends Controller {
    public function getCampaigns(Request $request)
    {
        $reward = Reward::whereUuid(request('uuid', 0))
                        ->with(['campaigns' => function($query) {
                            $query->with(['business' => function($query2) {
                                $query2->with('timezone');
                            }]);
                        }])
                        ->firstOrFail();
        if(!$reward) {
            return response()->json(['msg' => 'Invalid Reward UUID'], 422);
        }

        $campaigns = $reward->campaigns->map(function($campaign) {
            return collect([
                'campaign_id' => $campaign->id,
                'name' => $campaign->name,
                'store' => $campaign->business->name,
                'store_timezone' => $campaign->business->timezone->timezone_name,
                'active_from' => $campaign->pivot->active_from,
                'expires_at' => $campaign->pivot->expires_at,
                'active_monday' => $campaign->pivot->active_monday,
                'active_tuesday' => $campaign->pivot->active_tuesday,
                'active_wednesday' => $campaign->pivot->active_wednesday,
                'active_thursday' => $campaign->pivot->active_thursday,
                'active_friday' => $campaign->pivot->active_friday,
                'active_saturday' => $campaign->pivot->active_saturday,
                'active_sunday' => $campaign->pivot->active_sunday,
                'active_monday_from' => $campaign->pivot->active_monday_from,
                'active_tuesday_from' => $campaign->pivot->active_tuesday_from,
                'active_wednesday_from' => $campaign->pivot->active_wednesday_from,
                'active_thursday_from' => $campaign->pivot->active_thursday_from,
                'active_friday_from' => $campaign->pivot->active_friday_from,
                'active_saturday_from' => $campaign->pivot->active_saturday_from,
                'active_sunday_from' => $campaign->pivot->active_sunday_from,
                'active_monday_to' => $campaign->pivot->active_monday_to,
                'active_tuesday_to' => $campaign->pivot->active_tuesday_to,
                'active_wednesday_to' => $campaign->pivot->active_wednesday_to,
                'active_thursday_to' => $campaign->pivot->active_thursday_to,
                'active_friday_to' => $campaign->pivot->active_friday_to,
                'active_saturday_to' => $campaign->pivot->active_saturday_to,
                'active_sunday_to' => $campaign->pivot->active_sunday_to
            ]);
        });

        return response()->json([
            'reward_id' => $reward->id,
            'campaigns' => $campaigns
        ], 200);
    }

    public function postSaveDateRange(Request $request)
    {
        $this->validate($request, [
            'campaign_id' => 'required|exists:campaign_reward,campaign_id',
            'reward_id' => 'required|exists:campaign_reward,reward_id',
            'active_from' => 'required',
            'expires_at' => 'required',
            'active_monday' => 'required',
            'active_tuesday' => 'required',
            'active_wednesday' => 'required',
            'active_thursday' => 'required',
            'active_friday' => 'required',
            'active_saturday' => 'required',
            'active_sunday' => 'required',
            'active_monday_from' => 'nullable',
            'active_tuesday_from' => 'nullable',
            'active_wednesday_from' => 'nullable',
            'active_thursday_from' => 'nullable',
            'active_friday_from' => 'nullable',
            'active_saturday_from' => 'nullable',
            'active_sunday_from' => 'nullable',
            'active_monday_to' => 'nullable',
            'active_tuesday_to' => 'nullable',
            'active_wednesday_to' => 'nullable',
            'active_thursday_to' => 'nullable',
            'active_friday_to' => 'nullable',
            'active_saturday_to' => 'nullable',
            'active_sunday_to' => 'nullable'
        ]);

        $dateRange = CampaignReward::where('campaign_id', $request->campaign_id)
                                   ->where('reward_id', $request->reward_id)
                                   ->firstOrFail();
        
        if(!$dateRange) {
            return response()->json(['msg' => 'Campaign or Reward not found'], 422);
        }

        $dateRange->active_from = $request->active_from;
        $dateRange->expires_at = $request->expires_at;
        $dateRange->active_monday = $request->active_monday;
        $dateRange->active_tuesday = $request->active_tuesday;
        $dateRange->active_wednesday = $request->active_wednesday;
        $dateRange->active_thursday = $request->active_thursday;
        $dateRange->active_friday = $request->active_friday;
        $dateRange->active_saturday = $request->active_saturday;
        $dateRange->active_sunday = $request->active_sunday;
        if($request->has('active_monday_from')) { $dateRange->active_monday_from = $request->active_monday_from; }
        if($request->has('active_tuesday_from')) { $dateRange->active_tuesday_from = $request->active_tuesday_from; }
        if($request->has('active_wednesday_from')) { $dateRange->active_wednesday_from = $request->active_wednesday_from; }
        if($request->has('active_thursday_from')) { $dateRange->active_thursday_from = $request->active_thursday_from; }
        if($request->has('active_friday_from')) { $dateRange->active_friday_from = $request->active_friday_from; }
        if($request->has('active_saturday_from')) { $dateRange->active_saturday_from = $request->active_saturday_from; }
        if($request->has('active_sunday_from')) { $dateRange->active_sunday_from = $request->active_sunday_from; }
        if($request->has('active_monday_to')) { $dateRange->active_monday_to = $request->active_monday_to; }
        if($request->has('active_tuesday_to')) { $dateRange->active_tuesday_to = $request->active_tuesday_to; }
        if($request->has('active_wednesday_to')) { $dateRange->active_wednesday_to = $request->active_wednesday_to; }
        if($request->has('active_thursday_to')) { $dateRange->active_thursday_to = $request->active_thursday_to; }
        if($request->has('active_friday_to')) { $dateRange->active_friday_to = $request->active_friday_to; }
        if($request->has('active_saturday_to')) { $dateRange->active_saturday_to = $request->active_saturday_to; }
        if($request->has('active_sunday_to')) { $dateRange->active_sunday_to = $request->active_sunday_to; }
        $dateRange->save();

        return response()->json(null, 200);
    }
}