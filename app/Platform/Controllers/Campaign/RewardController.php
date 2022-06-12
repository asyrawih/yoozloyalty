<?php

namespace Platform\Controllers\Campaign;

use App\Customer;
use Platform\Models\Code;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Platform\Models\History;
use App\Models\EmailTemplate;
use Platform\Models\CoupunUsed;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessMerchantSendMail;
use App\Repositories\NotifPusherRepositories;
use Carbon\Carbon;

/**
 * @group Customer Reward
 *
 * Endpoints for customer related to reward redemption procedures.
 * @package Platform\Controllers\Campaign
 */
class RewardController extends Controller {

    private $notifPusher;

    public function __construct(NotifPusherRepositories $notifPusher)
    {
        $this->notifPusher = $notifPusher;
    }
    /*
     |--------------------------------------------------------------------------
     | Reward related functions
     |--------------------------------------------------------------------------
     */

    /**
     * Get code used to redeem a rewards with a link (e.g. QR code).
     * @queryParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @queryParam reward string required uuid of reward. Example: 703fcac9-9ffa-4527-9b3d-c9549e02a353
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postGetRedeemRewardToken(Request $request)
    {
        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();
        $reward = \Platform\Models\Reward::whereUuid(request('reward', 0))->firstOrFail();

        // First, delete existing codes
        Code::query()
            ->where('type', 'reward_token')
            ->where('customer_id', auth('customer')->id())
            ->delete();

        // Add generated code to codes table, expire in 1 hour
        $token = $this->getUniqueCode('reward_token', $campaign->id);
        $expires_at = Carbon::now()->addHours(1);

        $code = new Code;
        $code->campaign_id = $campaign->id;
        $code->customer_id = auth('customer')->id();
        $code->reward_id = $reward->id;
        $code->type = 'reward_token';
        $code->code = $token;
        $code->expires_at = $expires_at;
        $code->created_by = $campaign->created_by;

        $code->save();

        return response()->json([
            'status' => 'success',
            'token' => $token
        ]);
    }

    /**
     * Make sure code is unique
     *
     * @return boolean
     */
    public function getUniqueCode($type, $campaign_id)
    {
        if ($type == 'reward_token') {
            $token = Str::random(8);
        }

        $code = Code::query()
            ->where('campaign_id', $campaign_id)
            ->where('type', $type)
            ->where('code', $token)
            ->first();

        if ($code === null) {
            return $token;
        } else {
            return $this->getUniqueCode($type, $campaign_id);
        }
    }

    /**
     * Merchant verifies generated code
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam code string required Code.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postVerifyMerchantCode(Request $request)
    {
        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()
            ->whereUuid(request('uuid', 0))
            ->firstOrFail();

        // Set language locale
        $locale = request('locale', config('system.default_language'));

        app()->setLocale($locale);

        // Find code
        $code = Code::query()
            ->where('code', $request->code)
            ->where('campaign_id', $campaign->id)
            ->where('type', 'merchant')
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $code) {
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'code' => trans('campaign.code_invalid_or_expired')
                ]
            ], 422);
        }

        // Code is correct, let merchant choose reward and segments
        $rewards = $campaign->getActiveRewards();

        $segments = $campaign->business->segments->pluck('name', 'id');

        return response()->json([
            'status' => 'success',
            'segments' => $segments,
            'rewards' => $rewards
        ]);
    }

    /**
     * Initially merhant code was correct, double check code and process reward and segments
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam code string required Code.
     * @bodyParam reward string required uuid of reward. Example: 703fcac9-9ffa-4527-9b3d-c9549e02a353
     * @bodyParam segments integer[]. id of segments Example : [1, 2]
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postProcessMerchantEntry(Request $request)
    {
        $account = app()->make('account');

        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()
            ->whereUuid(request('uuid', 0))
            ->firstOrFail();

        $code = $request->code;
        $reward = $request->reward;
        $segments = $request->segments;

        $reward = \Platform\Models\Reward::whereUuid(request('reward', 0))->firstOrFail();

        $auth_customer = $request->user('customer');

        // Find customer by number
        $customer = Customer::query()
            ->where('campaign_id', $campaign->id)
            ->where('customer_number', $auth_customer->customer_number)
            ->where('active', 1)
            ->firstOrFail();

        // Set language locale
        $locale = request('locale', config('system.default_language'));

        app()->setLocale($locale);

        if ($code !== 'mobile') {
            // Find code
            $code = Code::query()
                ->where('code', $code)->where('campaign_id', $campaign->id)
                ->where('type', 'merchant')
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if ($code === null) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'code' => trans('campaign.code_invalid_or_expired')
                    ]
                ], 422);
            }

            $event = 'Redeemed by merchant';
        } else {
            $event = 'Redeemed by customer';
            $code = new \stdClass;
            $code->staff_id = null;
            $code->staff_name = null;
            $code->staff_email = null;
        }

        // Verify if customer has enough points
        $currentPoint = $customer->points;

        if ($reward->points_cost > $currentPoint) {
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'reward' => 'Not enough points to redeem this offer.'
                ]
            ], 422);
        }

        $reward_used_by_customer = History::query()
            ->where('customer_id', $customer->id)
            ->where('campaign_id', $campaign->id)
            ->where('reward_id', $reward->id)
            ->count();

        if (! $reward->multiple_time) {
            if ($reward_used_by_customer >= 1) {
                return response()->json([
                    'status' => 'error',
                    'errors' => array(
                        'reward' => 'You have already availed this reward. Please explore other rewards.'
                    ),
                ], 422);
            }
        }

        if ($reward->delivery_by_coupun) {
            if (! $reward->coupunCodeisActive()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'reward' => 'No coupon available. Please contact store administrator.'
                    ]
                ], 422);
            };
        }

        // Code is correct, process points and segments
        $history = new History;

        $history->customer_id = $customer->id;
        $history->campaign_id = $campaign->id;
        $history->staff_id = $code->staff_id;
        $history->staff_name = $code->staff_name;
        $history->staff_email = $code->staff_email;
        $history->redemption_id = $history->generateRedemptionId();
        $history->reward_id = $reward->id;
        $history->reward_title = $reward->title;
        $history->points = -$reward->points_cost;
        $history->event = $event;
        $history->created_by = $campaign->created_by;

        $history->save();

        $this->assignPointsUsage($reward->points_cost, $customer->id);

        $emailTemplate = EmailTemplate::query()
            ->where('name', 'customer_redeem_point')
            ->where('created_by', $campaign->created_by)
            ->first();

        $variableTemplate = [
            '{{ website_name }}',
            '{{ website_url }}',
            '{{ reward }}',
            '{{ current_point }}',
            '{{ name_of_user }}',
            '{{ email_of_user }}'
        ];

        $variableChange = [
            $campaign->name,
            $campaign->url,
            $reward->title,
            $customer->points,
            $request->name,
            $request->email
        ];

        $email = new \stdClass;

        $email->website_name = $campaign->name;
        $email->website_url = $campaign->url;
        $email->from_name = $account->app_mail_name_from;
        $email->from_email = $account->app_mail_address_from;

        $email->to_name = $customer->name;
        $email->to_email = $customer->email;
        $email->subject = str_replace($variableTemplate, $variableChange ,$emailTemplate->subject);
        $email->template = str_replace($variableTemplate, $variableChange ,$emailTemplate->template);

        ProcessMerchantSendMail::dispatch(
            $email,
            $campaign->smtp_service_id
        )->onQueue('emails');

        $coupun = null;

        if ($reward->delivery_by_coupun) {
            $coupun = \Platform\Models\CoupunCode::query()
                ->where('reward_id', $reward->id)
                ->where('status', 0)
                ->first();

            $coupun->status = 1;

            $coupun->save();

            $coupunUsed = new CoupunUsed;
            $coupunUsed->reward_id = $reward->id;
            $coupunUsed->coupun_code_id = $coupun->id;
            $coupunUsed->redemption_id = $history->redemption_id;
            $coupunUsed->save();

            $emailTemplate = EmailTemplate::where('name', 'customer_coupun_delivery')->where('created_by', $campaign->created_by)->first();

            $variableTemplate = [
                '{{ website_name }}',
                '{{ website_url }}',
                '{{ coupun_name }}',
                '{{ coupun_code }}',
                '{{ name_of_user }}',
                '{{ email_of_user }}'
            ];

            $variableChange = [
                $campaign->name,
                $campaign->url,
                $coupun->name,
                $coupun->code,
                $request->name,
                $request->email
            ];

            $email = new \stdClass;

            $email->website_name = $campaign->name;
            $email->website_url = $campaign->url;
            $email->from_name = $account->app_mail_name_from;
            $email->from_email = $account->app_mail_address_from;

            $email->to_name = $customer->name;
            $email->to_email = $customer->email;
            $email->subject = str_replace($variableTemplate, $variableChange ,$emailTemplate->subject);
            $email->template = str_replace($variableTemplate, $variableChange ,$emailTemplate->template);

            ProcessMerchantSendMail::dispatch(
                $email,
                $campaign->smtp_service_id
            )->onQueue('emails');
        }

        // Segments
        if (is_array($segments) && count($segments) > 0) {
            $history->segments()->sync($segments);
        }

        // Increment reward redemptions
        $reward->increment('number_of_times_redeemed');


        if ($this->notifPusher->isAvailable()) {
            $this->notifPusher->redeemReward($customer, $reward);
            $this->notifPusher->pointRedeemed($customer, $reward->points_cost, $reward->title);
        }

        return response()->json([
            'status' => 'success',
            'reward' => $reward->title_with_points,
            'points' => $customer->points,
            'coupun' => $coupun,
        ]);
    }

    public function getActiveRewards(Request $request)
    {
		$campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();

		return response()->json([
			'rewards' => $campaign->getActiveRewards((int) $request->customer_type)->map(function($reward) {

				$activeFrom = Carbon::createFromFormat('Y-m-d H:i:s', $reward->pivot->active_from);
				$expiresAt = Carbon::createFromFormat('Y-m-d H:i:s', $reward->pivot->expires_at);
				$expiresInMonths = $expiresAt->diffInMonths(Carbon::now());

				return collect([
                    'uuid' => $reward->uuid,
                    'title' => $reward->title,
                    'description' => $reward->description,
					'active_from' => $activeFrom,
					'points' => $reward->points_cost,
                    'reward_value' => $reward->reward_value,
                    'multiple_time' => $reward->multiple_time,
					'expiresInMonths' => $expiresInMonths,
					'expires_at' => $expiresAt,
					'images' => [
						['href' => $reward->main_image, 'thumb' => $reward->main_image_thumb],
						['href' => $reward->image1, 'thumb' => $reward->image1_thumb],
						['href' => $reward->image2, 'thumb' => $reward->image2_thumb],
						['href' => $reward->image3, 'thumb' => $reward->image3_thumb],
						['href' => $reward->image4, 'thumb' => $reward->image4_thumb]
					],
				]);
			})
		]);
    }

    private function assignPointsUsage($points_cost, $customer_id)
    {
        $remaining_cost = $points_cost;

        $histories = History::query()->whereRaw('(points - points_usage) > 0')
            ->where('reward_id', NULL)
            ->where('customer_id', $customer_id)
            ->where('points_expired_date', '>', Carbon::now())
            ->orderBy('points_expired_date', 'ASC')
            ->get();

        foreach($histories as $history) {
            if ($remaining_cost <= 0) {
                break;
            }

            $remaining_points = $history->points - $history->points_usage;

            if ($remaining_points > 0) {
                // If there is still remaining points
                if ($remaining_points < $remaining_cost) {
                    History::query()
                        ->where('id', $history->id)
                        ->update([
                            'points_usage' => $remaining_points + $history->points_usage
                        ]);

                    $remaining_cost -= $remaining_points;
                } else {
                    History::query()
                        ->where('id', $history->id)
                        ->update([
                            'points_usage' => $remaining_cost + $history->points_usage
                        ]);

                    $remaining_cost = 0;
                }
            }
        }
    }
}
