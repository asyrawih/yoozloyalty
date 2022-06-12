<?php

namespace Platform\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Customer;
use App\Http\Controllers;
use Carbon\Carbon;
use Platform\Controllers\Core;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Platform\Models\Campaign;

/**
* @group Campaign / Website
* APIs for getting website information
*/
class CampaignApiController extends Controller
{

    /**
    * Get campaign detail
    * @unauthenticated
    */
    public function getCampaign($slug = null)
    {
        $account = app()->make('account');

        if ($slug == null) {
            return response()->json([
                'status' => 'not found',
            ], 404);
        }

        $slug = explode('/', $slug);
        $slug = $slug[0] ?? null;

        if ($slug == '') $slug = null;

        $campaign = Campaign::withoutGlobalScopes()->where(
            'account_id', $account->id
        )->where(
            'slug', $slug
        )->first();

        if (empty($campaign)) {
            return response()->json([
                'status' => 'not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'website' => [
                'uuid' => $campaign->uuid,
                'slug' => $campaign->slug,
                'name' => $campaign->name,
                'signup_bonus_points' => $campaign->signup_bonus_points,
                'joining_fee' => $campaign->joining_fee,
                'joining_benefits' => $campaign->joining_benefits,
                'how_it_work' => $campaign->how_it_work,
                'validity' => "12 Months",
                'validty_expired' => $campaign->created_at->addYear()->format('Y-m-d H:i:s'),
                'website_detail' => $campaign->website_detail,
                'customer_count' => $campaign->customer_count,
                'phone' => $campaign->business->phone,
                'mobile' => $campaign->business->mobile,
                'website' => $campaign->business->website,
                'street1' => $campaign->business->street1,
                'street2' => $campaign->business->street2,
                'city' => $campaign->business->city,
                'state' => $campaign->business->state,
                'postal_code' => $campaign->business->postal_code,
                'reward' => $campaign->rewards,
                'host' => $campaign->host,
                'logo' => $campaign->business->logo
            ],
        ], 200);
    }

    /**
    * Get campaign detail logged
    */
    public function getCampaignLogged($slug = null)
    {
        $account = app()->make('account');
        //$account = (object) $account->only('app_name', 'app_headline', 'app_scheme', 'app_host', 'language', 'locale');
        if ($slug == null) {
            return response()->json([
                'status' => 'not found',
            ], 404);
        }

        $slug = explode('/', $slug);
        $slug = $slug[0] ?? null;
        if ($slug == '') $slug = null;

        // Get campaign, if exists
        $campaign = Campaign::withoutGlobalScopes()->where(
            'account_id', $account->id
        )->where(
            'slug', $slug
        )->first();

        $loggedIn = Customer::where(
            'email', auth()->user()->email
        )->where(
            'campaign_id', $campaign->id
        )->first();

        return response()->json([
            'status' => 'success',
            'website' => [
                'user_register' => isset($loggedIn) ? true : false
            ],
        ], 200);
    }

    /**
    * @group Customer update expired date
    * APIs for update expired date
    * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
    *
    */
    public function renew(Request $request)
    {
        $v = Validator::make($request->all(), [
            'uuid' => 'required'
        ]);

        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $campaign = Campaign::withoutGlobalScopes()->whereUuid(
            request('uuid', 0)
        )->firstOrFail();

        Customer::where(
            'email', auth()->user()->email
        )->where(
            'campaign_id', $campaign->id
        )->update([
            'expired_date' => Carbon::now()->addYear()->format('Y-m-d H:i:s')
        ]);

        return response()->json([
            'status' => 'success'
        ], 200);
    }
}
