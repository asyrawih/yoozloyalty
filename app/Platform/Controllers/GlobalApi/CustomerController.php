<?php
namespace Platform\Controllers\GlobalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Customer;
use Platform\Models\Campaign;

/**
 * @group Global
 *
 * Endpoint for retrieving customer by phone number
 */
class CustomerController extends Controller {

    /**
     * Get customer by phone number
     * @urlParam phone_number integer required The number of the customer.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function phoneNumber($phone_number){
        $data = Customer::where('customer_number', $phone_number)->first();
        if ($data === null) {            
            return response()->json(['status' => 'error', "msg" => 'Customer not found' ], 404);
        }                    
        $campaign = Campaign::find($data->campaign_id);

        $datas = [
            'customer' => $data->toArray(),
            'campaign' => $campaign->toArray(),            
        ];        

        unset($datas['campaign']['customers']);
        return response()->json([
            'data' => $datas
        ], 200);
    }
}
