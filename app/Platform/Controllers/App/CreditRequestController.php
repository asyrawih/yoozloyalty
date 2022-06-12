<?php

namespace Platform\Controllers\App;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Platform\Models\History;
use App\Models\EmailTemplate;
use Platform\Models\CreditRequest;
use App\Jobs\ProcessMerchantSendMail;

class CreditRequestController extends \App\Http\Controllers\Controller
{
    public function index(Request $request){
        $data = CreditRequest::whereUuid($request->uuid)->first();

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    public function update(Request $request)
    {
        $data = CreditRequest::whereUuid($request->uuid)->update([
            'status' => $request->status
        ]);

        $data = CreditRequest::whereUuid($request->uuid)->firstOrFail();

        if ($data->status === 'approved') {
                $account = app()->make('account');

                $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->where('id', $data->campaign_id)->firstOrFail();

                $history = new History;
                $history->customer_id = $data->customer_id;
                $history->campaign_id = $data->campaign_id;
                $history->points = $data->points;
                $history->event = 'Credit Request';
                $history->created_by = $data->created_by;
                $history->points_expired_date = Carbon::now()->addDays($data->campaign->user->points_expiry->points_expiry ?? config('system.default_points_expiry'));

                $history->save();

                $emailTemplate = EmailTemplate::where('name', 'customer_credit_point')->where('created_by', $campaign->created_by)->first();

                $variableTemplate = [
                    '{{ website_name }}',
                    '{{ website_url }}',
                    '{{ point_got }}',
                    '{{ current_point }}',
                    '{{ event }}',
                    '{{ name_of_user }}',
                    '{{ email_of_user }}'
                ];

                $variableChange = [
                    $campaign->name,
                    $campaign->url,
                    $data->points,
                    $data->customer->points,
                    'Credit Request',
                    $request->name,
                    $request->email
                ];

                $email = new \stdClass;

                $email->website_name = $campaign->name;
                $email->website_url = $campaign->url;
                // $email->from_name = $campaign->business->name;
                // $email->from_email = ($campaign->business->email != null) ? $campaign->business->email : config('general.mail_address_from');
                $email->from_name = $account->app_mail_name_from;
                $email->from_email = $account->app_mail_address_from;

                $email->to_name = $data->customer->name;
                $email->to_email = $data->customer->email;
                $email->subject = str_replace($variableTemplate, $variableChange ,$emailTemplate->subject);
                $email->template = str_replace($variableTemplate, $variableChange ,$emailTemplate->template);

                ProcessMerchantSendMail::dispatch(
                    $email,
                    $campaign->smtp_service_id
                )->onQueue('emails');
        }

        return response()->json(['status' => 'success', 'msg' => 'Status Updated'], 200);
    }
}
