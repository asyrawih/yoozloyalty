<?php

namespace App\Jobs;

use App\Customer;
use App\Models\SmsService;
use App\Models\SmsTemplate;
use App\User;
use Brick\PhoneNumber\PhoneNumber;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;

class SendSMSCustomerBirthday implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Customer $customer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $customer_number = PhoneNumber::parse($this->customer->customer_number, $this->customer->country_code);

            $smsTemplate = SmsTemplate::query()
                ->where('name', 'customer_birthday')
                ->where('created_by', $this->customer->created_by)
                ->first();

            $variableTemplate = [
                '{{ website_name }}',
                '{{ name_of_user }}',
            ];

            $variableChange = [
                $this->customer->campaign_text,
                $this->customer->name,
            ];

            $message = str_replace($variableTemplate, $variableChange, $smsTemplate->template);

            $smsService = SmsService::query()
                ->where('country_isd_code', $this->customer->country_isd_code)
                ->where('is_active', 1)
                ->first();

            if ($smsService) {
                $schema = json_decode($smsService->schema, TRUE);

                switch ($smsService->blueprint) {
                    case 'twilio': {
                        $twilioClient = new Client(
                            $schema['account_sid'],
                            $schema['account_auth_token']
                        );

                        $twilioClient->messages->create(
                            $customer_number,
                            [
                                'from' => $schema['sender_number'],
                                'body' => $message,
                            ]
                        );

                        echo 'Twilio SMS sent to ' . $customer_number . PHP_EOL;
                    }break;
                }
            }
        } catch (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }
    }
}
