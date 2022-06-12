<?php

namespace Platform\Controllers\Campaign;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Customer;
use App\Jobs\ProcessMerchantSendMail;
use App\Jobs\ProcessSendMail;
use App\Models\EmailTemplate;
use App\Models\SmtpService;
use Illuminate\Support\Facades\Mail;

/**
 * @group Merchant Register
 *
 */

class VerificationController extends \App\Http\Controllers\Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:customer')->only('resend');
        $this->middleware('throttle:6,1')->only('resend');
    }

    /**
     * Resent the email verification notification
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        $account = app()->make('account');

        if ($request->user()->hasVerifiedEmail()) {
            return response(['message' => 'Already verified']);
        }

        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()
            ->whereUuid(request('uuid', 0))
            ->firstOrFail();

        $emailTemplate = EmailTemplate::where('name', 'customer_registeration')->where('created_by', $campaign->created_by)->first();

        $verification_url = route('customer.verification.verify',[
            'id' => auth('customer')->user()->id,
            'hash' => auth('customer')->user()->verification_code
        ]);

        $cta_button = '<a href="'.$verification_url.'" class="button button-primary" target="_blank">Verify</a>';

        $variableTemplate = [
            '{{ website_name }}',
            '{{ website_url }}',
            '{{ verification_button }}',
            '{{ verification_url }}',
            '{{ name_of_user }}',
            '{{ email_of_user }}'
        ];

        $variableChange = [
            $campaign->name,
            $campaign->url,
            $cta_button,
            $verification_url,
            $request->name,
            $request->email
        ];

        $email = new \stdClass;

        $email->website_name = $campaign->name;
        $email->website_url = $campaign->url;
        $email->from_name = $account->app_mail_name_from;
        $email->from_email = $account->app_mail_address_from;
        $email->to_name = auth('customer')->user()->name;
        $email->to_email = auth('customer')->user()->email;
        $email->subject = str_replace($variableTemplate, $variableChange ,$emailTemplate->subject);
        $email->template = str_replace($variableTemplate, $variableChange ,$emailTemplate->template);

        // Mail::send(new \App\Mail\TemplateMail($email));

        ProcessMerchantSendMail::dispatch(
            $email,
            $campaign->smtp_service_id
        )->onQueue('emails');

        if ($request->wantsJson()) {
            return response(['message' => 'Email Sent']);
        }

        return back()->with('resent', true);
    }

    /**
     * Mark the authenticated user's email address as verified
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response
     * @param \Illuminate\Auth\Access\AuthorizationException
     */

     public function verify(Request $request, $id, $hash)
     {
        $user = Customer::find($request->route('id'));

        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()
            ->where('id', $user->campaign_id)
            ->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            return redirect($campaign->url);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($request->user()));

            return redirect($campaign->url.'/verification/success');
        }

        return redirect($campaign->url);
     }
}
