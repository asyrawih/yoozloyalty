<?php

namespace App\Imports;

use App\Country;
use App\Jobs\DeactivateMerchantPlan;
use App\Jobs\DeactivateMerchantPlanFromAdmin;
use App\User;
use Illuminate\Support\Str;
use App\Models\EmailTemplate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Auth\Events\Registered;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Repositories\NotifPusherRepositories;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class MerchantImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    use Importable;

    private NotifPusherRepositories $notifPusher;

    public function __construct(NotifPusherRepositories $notifPusher)
    {
        $this->notifPusher = $notifPusher;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $admin = auth()->user();

        foreach ($rows as $column) {
            if (! User::query()->where('email', $column['email'])->first()) {
                $country = Country::query()->where('code', $column['country_code'])->first();

                $merchant = new User;

                $merchant->account_id = $admin->account_id;
                $merchant->role = 3;
                $merchant->active = 1;
                $merchant->name = $column['name'];
                $merchant->email = $column['email'];
                $merchant->phone_personal = $column['phone_number'];
                $merchant->date_of_birth = $column['date_of_birth'];
                $merchant->password = bcrypt($column['password']);
                $merchant->language = config('system.default_language');
                $merchant->locale = config('system.default_locale');
                $merchant->timezone = config('system.default_timezone');
                $merchant->currency_code = config('system.default_currency');
                $merchant->verification_code = Str::random(32);
                $merchant->country_id = $country->id;
                $merchant->city = $column['city'];
                $merchant->country_code = "+{$country->phonecode}";
                $merchant->expires = ($column['plan_expired_at'] === 'NULL')
                    ? Carbon::now()->addDays(config('system.trial_days'))
                    : Carbon::parse($column['plan_expired_at']);
                $merchant->created_by = $admin->id;

                if ($column['plan_id'] !== "NULL") {
                    $merchant->plan_id = $column['plan_id'];

                    DeactivateMerchantPlanFromAdmin::dispatch($merchant)
                        ->delay($merchant->expires)
                        ->onQueue('billings');
                }

                $merchant->save();

                event(new Registered($merchant));


                if ($this->notifPusher->isAvailable()) {
                    $this->notifPusher->registerMerchant($merchant);
                }

                EmailTemplate::insertRecord($merchant->id);
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
