<?php
namespace Platform\Controllers\GlobalApi;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Platform\Models\Industry;
use Platform\Models\Campaign;

/**
 * @group Global
 * @unauthenticated
 * Endpoint for retrieving the list of industry
 * @package Platform\Controllers\Campaign
 */
class IndustryController extends Controller {

    /**
     * Get list of industry
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = Industry::all();

        return response()->json(compact('data'));
    }

    /**
     * Get list of website by selected industry
     * @urlParam id string required id of industry
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function website($id)
    {
        $account = app()->make('account');

        $industry = Industry::with(['business'])->find($id);

        $data = array();

        foreach ($industry->business as $key => $value) {
            $campaigns = \Platform\Models\Campaign::withoutGlobalScopes()
                ->where('account_id', $account->id)
                ->where('created_by', $value->created_by)
                ->get();

            for ($i = 0; $i < count($campaigns) ; $i++) {
                array_push($data, array(
                    'uuid' => $campaigns[$i]->uuid,
                    'slug' => $campaigns[$i]->slug,
                    'name' => $campaigns[$i]->name,
                    'signup_bonus_points' => $campaigns[$i]->signup_bonus_points,
                    'joining_fee' => $campaigns[$i]->joining_fee,
                    'joining_benefits' => $campaigns[$i]->joining_benefits,
                    'how_it_work' => $campaigns[$i]->how_it_work,
                    'website_detail' => $campaigns[$i]->website_detail,
                    'customer_count' => $campaigns[$i]->customer_count,
                    'phone' => $campaigns[$i]->business->phone,
                    'mobile' => $campaigns[$i]->business->mobile,
                    'website' => $campaigns[$i]->business->website,
                    'street1' => $campaigns[$i]->business->street1,
                    'street2' => $campaigns[$i]->business->street2,
                    'city' => $campaigns[$i]->business->city,
                    'state' => $campaigns[$i]->business->state,
                    'postal_code' => $campaigns[$i]->business->postal_code,
                    'reward' => $campaigns[$i]->rewards,
                    'logo' => $campaigns[$i]->business->logo,
                ));
            }

        }

        return response()->json(compact('data'));
    }
}
