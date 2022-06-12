<?php
namespace Platform\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Platform\Models\Business;
use Platform\Models\Campaign;

/**
 * @group Global
 *
 * This api can be reached using staff or customer token
 * @package Platform\Controllers\Campaign
 */
class StoreController extends Controller {

    /**
     * Get available stores
     * @queryParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
        $campaign = Campaign::whereUuid($request->input('uuid'))->first();
        if(!$campaign){
            return response()->json(['msg' => "Campaign not found"]);
        }

        $user = $campaign->user()->first();
        if(!$user){
            return response()->json(['msg' => "User not found"]);
        }

        $stores = $user->businesses()->get();
        if(!$stores){
            return response()->json(['msg' => "Stores not found"]);
        }

        return response()->json(['data' => $stores], 200);
    }
}
