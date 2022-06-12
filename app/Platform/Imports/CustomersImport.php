<?php

namespace Platform\Imports;

use App\Customer;
use App\Repositories\EmailTemplateRepository;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Platform\Models\Campaign;
use Platform\Models\History;

class CustomersImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    use Importable;

    private $campaign_id;
    private EmailTemplateRepository $emailTemplate;

    public function __construct($campaign_id, EmailTemplateRepository $emailTemplate)
    {
        $this->campaign_id = $campaign_id;
        $this->emailTemplate = $emailTemplate;
    }

    public function collection(Collection $rows)
    {
        $counter = 1;
        $merchant = User::withoutGlobalScopes()->where('id', auth()->id())->first();
        $campaign = Campaign::withoutGlobalScopes()->where('id', $this->campaign_id)->first();
        $currentCount = Customer::query()->where('campaign_id', $campaign->id)->count();
        $currentLimited = $merchant->getPlanLimitationsAttribute()['customers'];
        $maxCount = ($currentLimited === 'unlimited') ? count($rows) : $currentLimited - $currentCount;

        foreach ($rows as $row) {
            if (! Customer::query()->where('email', $row['email'])->where('campaign_id', $campaign->id)->first()) {
                if ($counter <= $maxCount) {
                    $customer = new Customer;

                    $customer->account_id = $merchant->account_id;
                    $customer->campaign_id = $campaign->id;
                    $customer->role = 1;
                    $customer->name = "{$row['first_name']} {$row['last_name']}";
                    $customer->email = $row['email'];
                    $customer->gender = ($row['gender'] == 'NULL') ? NULL : $row['gender'];
                    $customer->street_name = ($row['street_name'] == 'NULL') ? NULL : $row['street_name'];
                    $customer->city = ($row['city'] == 'NULL') ? NULL : $row['city'];
                    $customer->country = ($row['country'] == 'NULL') ? NULL : $row['country'];
                    $customer->card_number = ($row['card_number'] == 'NULL') ? NULL : $row['card_number'];
                    $customer->card_status = ($row['card_status'] == 'NULL') ? 1 : $row['card_status'];
                    $customer->password = bcrypt($row['password']);
                    $customer->country_isd_code = $row['country_isd_code'];
                    $customer->country_code = $row['country_code'];
                    $customer->customer_number = $row['customer_number'];
                    $customer->verification_code = Str::random(32);
                    $customer->language = config('system.default_language');
                    $customer->locale = config('system.default_locale');
                    $customer->timezone = config('system.default_timezone');
                    $customer->active = 1;
                    $customer->logins = 0;
                    $customer->expired_date = Carbon::now()->addYear()->format('Y-m-d H:i:s');
                    $customer->created_by = $merchant->id;

                    $customer->save();

                    if ((int) $row['balance'] > 0) {
                        $history = new History;
                        $history->campaign_id = $campaign->id;
                        $history->customer_id = $customer->id;
                        $history->points = (int) $row['balance'];
                        $history->event = 'Import Customer';
                        $history->created_by = $merchant->id;
                        $history->points_expired_date = Carbon::now()->addDays($merchant->points_expiry->points_expiry ?? config('system.default_points_expiry'));
                        $history->save();
                    }

                    $this->emailTemplate->sendCustomerRegistration($campaign, $customer);

                    $counter++;
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
