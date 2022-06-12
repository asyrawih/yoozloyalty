<?php

namespace App\Imports;

use App\Customer;
use Illuminate\Support\Str;
use Platform\Models\History;
use Platform\Models\Campaign;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Platform\Models\CreditRequest;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\NotifPusherRepositories;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Repositories\Campaign\PointsConversionRuleRepository;
use Illuminate\Support\Facades\DB;

class CreditRequestImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    use Importable;

    private string $uuid;
    private PointsConversionRuleRepository $pointsConversionRule;
    private NotifPusherRepositories $notifPusher;
    private EmailTemplateRepository $emailTemplate;

    const EVENT_NAME = 'Credit Request';

    public function __construct(
        string $uuid,
        PointsConversionRuleRepository $pointsConversionRule,
        NotifPusherRepositories $notifPusher,
        EmailTemplateRepository $emailTemplate
    ) {
        $this->uuid = $uuid;
        $this->pointsConversionRule = $pointsConversionRule;
        $this->notifPusher = $notifPusher;
        $this->emailTemplate = $emailTemplate;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $account = app()->make('account');

        $campaign = Campaign::withoutGlobalScopes()
            ->whereUuid($this->uuid)
            ->first();

        foreach ($collection as $row) {
            if (! CreditRequest::query()->where('campaign_id', $campaign->id)->where('receipt_number', $row['receipt_number'])->first()) {
                $customer = Customer::query()
                    ->where('campaign_id', $campaign->id)
                    ->where(function ($query) use ($row) {
                        $query->where('email', $row['customer_email'])
                            ->orWhere('customer_number', $row['customer_number']);
                    })
                    ->where('active', 1)
                    ->first();

                if (! $customer) {
                    $customer = new Customer;
                    $customer->account_id = $campaign->account_id;
                    $customer->campaign_id = $campaign->id;
                    $customer->created_by = $campaign->created_by;
                    $customer->role = 1;
                    $customer->active = 1;
                    $customer->name = $row['customer_name'];
                    $customer->email = $row['customer_email'];
                    $customer->country_code = $row['country_code'];
                    $customer->country_isd_code = $row['country_isd_code'];
                    $customer->customer_number = $row['customer_number'];
                    $customer->password = bcrypt(Str::random(8));
                    $customer->verification_code = Str::random(32);
                    $customer->language = config('system.default_language');
                    $customer->locale = config('system.default_locale');
                    $customer->timezone = config('system.default_timezone');
                    $customer->save();

                    $token = Str::random(32);

                    DB::table('password_resets')
                        ->where('email', $customer->email)
                        ->delete();

                    DB::table('password_resets')->insert([
                        'email' => $customer->email,
                        'token' => $token,
                        'created_at' => Carbon::now('UTC')
                    ]);

                    $body_top = "Hi {$customer->name}," . PHP_EOL . PHP_EOL;
                    $body_top .= "We got request to create {$campaign->name} password." . PHP_EOL . PHP_EOL;
                    $body_bottom = "If you didn't request this, you can ignore this email or let us know. Your password won't change until you create a new password." . PHP_EOL;

                    $email = new \stdClass;
                    $email->app_name = $campaign->name;
                    $email->app_url = $campaign->url;
                    $email->from_name = $account->app_mail_name_from;
                    $email->from_email = $account->app_mail_address_from;
                    $email->to_name = $customer->name;
                    $email->to_email = $customer->email;
                    $email->subject = "Create a new password";
                    $email->body_top = $body_top;
                    $email->cta_label = "Create a new password";
                    $email->cta_url = "{$campaign->url}/password/create?code={$token}";
                    $email->body_bottom = $body_bottom;

                    Mail::send(new \App\Mail\SendMail($email));
                }

                if ($customer) {
                    // GET credit points
                    $response = $this->pointsConversionRule->credit_points(
                        $campaign->id,
                        floatval($row['receipt_amount']),
                        $campaign->credit_points_mode
                    );

                    if ($response['status'] === 'success') {
                        $points = $response['points'];

                        $creditRequest = new CreditRequest;

                        $creditRequest->customer_id = $customer->id;
                        $creditRequest->campaign_id = $campaign->id;
                        $creditRequest->receipt_number = $row['receipt_number'];
                        $creditRequest->receipt_amount = $row['receipt_amount'];
                        $creditRequest->points = $points;
                        $creditRequest->status = CreditRequest::STATUS_APPROVED;
                        $creditRequest->created_by = $campaign->created_by;
                        $creditRequest->updated_by = $campaign->created_by;
                        $creditRequest->created_at = Carbon::parse($row['created_at']);
                        $creditRequest->updated_at = Carbon::parse($row['created_at']);

                        $creditRequest->save();

                        $history = new History;

                        $history->customer_id = $creditRequest->customer_id;
                        $history->campaign_id = $creditRequest->campaign_id;
                        $history->points = $creditRequest->points;
                        $history->bill_number = $creditRequest->receipt_number;
                        $history->bill_amount = $creditRequest->receipt_amount;
                        $history->event = self::EVENT_NAME;
                        $history->created_by = $campaign->created_by;
                        $history->points_expired_date = Carbon::now()->addDays($campaign->user->points_expiry->points_expiry ?? config('system.default_points_expiry'));
                        $history->created_at = Carbon::parse($row['created_at']);
                        $history->updated_at = Carbon::parse($row['created_at']);

                        $history->save();

                        $this->emailTemplate->customerCreditPoint(
                            $campaign,
                            $customer,
                            $points,
                            self::EVENT_NAME
                        );


                        if ($this->notifPusher->isAvailable()) {
                            $this->notifPusher->pointCredited(
                                $customer,
                                $history->points,
                                self::EVENT_NAME
                            );
                        }
                    }
                }
            }
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 250;
    }
}
