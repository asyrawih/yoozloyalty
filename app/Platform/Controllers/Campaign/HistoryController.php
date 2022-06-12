<?php

namespace Platform\Controllers\Campaign;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Customer;
use App\Http\Controllers;
use Platform\Controllers\Core;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Platform\Models\Campaign;

/**
* @group Customer History
*
* Endpoint related to retrieving customer's redemption history data
* @package Platform\Controllers\Campaign
*/
class HistoryController extends Controller
{
    /*

    |---------------------------------------------------------------------
    | History related functions
    |---------------------------------------------------------------------
    */
    /**
    * Get customer history off 1 website.
    * @queryParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function getHistory(Request $request)
    {

        $campaign = Campaign::withoutGlobalScopes()->whereUuid(
            request('uuid', 0)
        )->first();

        if (!$campaign) {
            return response()->json([
                'error' => 'website not found',
            ], 404);
        }

        $user = Customer::withoutGlobalScopes()->where(
            'email', Auth::user('customer')->email
        )->where(
            'campaign_id', $campaign->id
        )->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.']);
        }

        $locale = request('locale', config('system.default_language'));

        return response()->json($user->getHistoryWithCampaign($locale));
    }

    /**
    * Get customer history off 1 website.
    *
    * @queryParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
    *
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function getHistoryMobile(Request $request)
    {

        $campaign = Campaign::withoutGlobalScopes()->whereUuid(
            request('uuid', 0)
        )->first();

        if (!$campaign) {
            return response()->json([
                'error' => 'website not found',
            ], 404);
        }


        $user = Customer::withoutGlobalScopes()->where(
            'campaign_id', $campaign->id
        )->where(
            'customer_number', Auth::user('customer')->customer_number
        )->where(
            'active', 1
        )->first();

        if (!$user) {
            return response()->json([]);
        }

        $locale = request('locale', config('system.default_language'));

        $data = [];

        foreach ($user->getHistoryWithCampaign($locale) as $key => $value) {
            array_push($data, $value);
        }

        return response()->json(['totalList' => $data]);
    }

    /**
     * Get customer history off all joined website.
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function getHistoryMultiple(Request $request)
    {
        $user = Customer::withoutGlobalScopes()->where('customer_number', Auth::user('customer')->customer_number)->get();
        $data = [];

        $locale = request('locale', config('system.default_language'));
        for ($i=0; $i < count($user); $i++) {
            foreach ($user[$i]->getHistoryWithCampaign($locale) as $key => $value) {
                array_push($data, $value);
            }
        }

        return response()->json(['totalList' => $data]);
    }
}
