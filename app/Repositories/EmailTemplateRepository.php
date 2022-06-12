<?php

namespace App\Repositories;

use App\Customer;
use App\Models\EmailTemplate;
use Platform\Models\Campaign;
use App\Jobs\ProcessMerchantSendMail;

class EmailTemplateRepository extends ApiRepository
{
    public function __construct(EmailTemplate $model)
    {
        parent::__construct($model);
    }

    public function customerCreditPoint(Campaign $campaign, Customer $customer, int $points = 0, string $event = 'Credited by mobile app')
    {
        $account = app()->make('account');

        $emailTemplate = $this->model->query()
            ->where('name', 'customer_credit_point')
            ->where('created_by', $campaign->created_by)
            ->first();

        $login_url = $campaign->url . '/login';

        $cta_button = '<a href="' . $login_url . '" class="button button-primary" target="_blank">Login</a>';

        $variableTemplate = [
            '{{ website_name }}',
            '{{ website_url }}',
            '{{ login_button }}',
            '{{ login_url }}',
            '{{ point_got }}',
            '{{ current_point }}',
            '{{ event }}',
            '{{ name_of_user }}',
            '{{ email_of_user }}'
        ];

        $variableChange = [
            $campaign->name,
            $campaign->url,
            $cta_button,
            $login_url,
            $points,
            $customer->points,
            $event,
            $customer->name,
            $customer->email
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

    public function sendCustomerRegistration(Campaign $campaign, Customer $customer)
    {
        $account = app()->make('account');

        $emailTemplate = $this->model->query()
            ->where('name', 'customer_registeration')
            ->where('created_by', $campaign->created_by)
            ->first();

        $verification_url = route('customer.verification.verify',[
            'id' => $customer->id,
            'hash' => $customer->verification_code
        ]);

        $cta_button = '<a href="' . $verification_url . '" class="button button-primary" target="_blank">Verify</a>';

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
            $customer->name,
            $customer->email
        ];

        $email = new \stdClass;

        $email->website_name = $campaign->name;
        $email->website_url = $campaign->url;
        $email->from_name = $account->app_mail_name_from;
        $email->from_email = $account->app_mail_address_from;

        $email->to_name = $customer->name;
        $email->to_email = $customer->email;
        $email->subject = str_replace($variableTemplate, $variableChange, $emailTemplate->subject);
        $email->template = str_replace($variableTemplate, $variableChange, $emailTemplate->template);

        ProcessMerchantSendMail::dispatch(
            $email,
            $campaign->smtp_service_id
        )->onQueue('emails');
    }
}
