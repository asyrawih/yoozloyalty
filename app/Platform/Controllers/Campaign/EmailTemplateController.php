<?php

namespace Platform\Controllers\Campaign;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Customer;
use App\Models\EmailTemplate;

/**
 * @group Email Template
 *
 * Endpoints to get a user's email template.
 * @package Platform\Controllers\Campaign
 */
class EmailTemplateController extends Controller
{
    /**
     * Fetch all user's email template
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $customer_forgot_password = EmailTemplate::where('created_by', auth()->user()->id)->where('name', 'customer_forgot_password')->first();
        $staff_forgot_password = EmailTemplate::where('created_by', auth()->user()->id)->where('name', 'staff_forgot_password')->first();
        $customer_registeration = EmailTemplate::where('created_by', auth()->user()->id)->where('name', 'customer_registeration')->first();
        $staff_registeration = EmailTemplate::where('created_by', auth()->user()->id)->where('name', 'staff_registeration')->first();
        $customer_credit_point = EmailTemplate::where('created_by', auth()->user()->id)->where('name', 'customer_credit_point')->first();
        $customer_redeem_point = EmailTemplate::where('created_by', auth()->user()->id)->where('name', 'customer_redeem_point')->first();
        $customer_otp_confirmation = EmailTemplate::where('created_by', auth()->user()->id)->where('name', 'customer_otp')->first();
        $customer_coupun_delivery = EmailTemplate::where('created_by', auth()->user()->id)->where('name', 'customer_coupun_delivery')->first();

        $data = [
            'customer_forgot_password' => [
                'uuid' => $customer_forgot_password->uuid,
                'subject' => $customer_forgot_password->subject,
                'template' => $customer_forgot_password->template,
            ],
            'customer_registeration' => [
                'uuid' => $customer_registeration->uuid,
                'subject' => $customer_registeration->subject,
                'template' => $customer_registeration->template,
            ],
            'customer_credit_point' => [
                'uuid' => $customer_credit_point->uuid,
                'subject' => $customer_credit_point->subject,
                'template' => $customer_credit_point->template,
            ],
            'customer_redeem_point' => [
                'uuid' => $customer_redeem_point->uuid,
                'subject' => $customer_redeem_point->subject,
                'template' => $customer_redeem_point->template,
            ],
            'otp_confirmation' => [
                'uuid' => $customer_otp_confirmation->uuid,
                'subject' => $customer_otp_confirmation->subject,
                'template' => $customer_otp_confirmation->template,
            ],
            'customer_coupun_delivery' => [
                'uuid' => $customer_coupun_delivery->uuid,
                'subject' => $customer_coupun_delivery->subject,
                'template' => $customer_coupun_delivery->template,
            ],
            'staff_forgot_password' => [
                'uuid' => $staff_forgot_password->uuid,
                'subject' => $staff_forgot_password->subject,
                'template' => $staff_forgot_password->template,
            ],
            'staff_registeration' => [
                'uuid' => $staff_registeration->uuid,
                'subject' => $staff_registeration->subject,
                'template' => $staff_registeration->template,
            ],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    /**
     * Update user's email template
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

        EmailTemplate::whereUuid($request->uuid)->update([
            'subject' => $request->subject,
            'template' => $request->template,
        ]);

        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
