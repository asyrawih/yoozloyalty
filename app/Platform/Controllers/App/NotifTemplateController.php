<?php

namespace Platform\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Customer;
use App\Models\NotifTemplate;

class NotifTemplateController extends Controller
{
    /**
     * 
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Admin
        $admin_merchant_registeration = NotifTemplate::where('name', 'admin_merchant_registeration')->first();

        // Merchant
        $merchant_customer_registeration = NotifTemplate::where('name', 'merchant_customer_registeration')->first();
        $merchant_customer_new_credit_request = NotifTemplate::where('name', 'merchant_customer_new_credit_request')->first();
        $merchant_customer_new_reward_redeem = NotifTemplate::where('name', 'merchant_customer_new_reward_redeem')->first();

        // Customer
        $customer_point_credited = NotifTemplate::where('name', 'customer_point_credited')->first();
        $customer_point_redeemed = NotifTemplate::where('name', 'customer_point_redeemed')->first();
        $customer_welcome_message = NotifTemplate::where('name', 'customer_welcome_message')->first();

        $data = [
            'admin_merchant_registeration' => $admin_merchant_registeration,
            'merchant_customer_registeration' => $merchant_customer_registeration,
            'merchant_customer_new_credit_request' => $merchant_customer_new_credit_request,
            'merchant_customer_new_reward_redeem' => $merchant_customer_new_reward_redeem,
            'customer_point_credited' => $customer_point_credited,
            'customer_point_redeemed' => $customer_point_redeemed,
            'customer_welcome_message' => $customer_welcome_message,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    /**
     * 
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {

        // Validate
        $v = Validator::make($request->all(), [
            'subject' => 'required',
            'template' => 'required',
            'current_password' => 'required|min:8|max:24',
        ]);

        if ($v->fails()) {
            return response()->json([
              'status' => 'error',
              'errors' => $v->errors()
            ], 422);
        }

        // Verify password
        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
            'status' => 'error',
            'errors' => ['current_password' => [trans('app.current_password_incorrect')]]
            ], 422);
        }

        NotifTemplate::whereUuid($request->uuid)->update([
            'subject' => $request->subject,
            'template' => $request->template,
        ]);

        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
