<?php

namespace App\Repositories\Staff;

use App\Customer;
use App\Repositories\ApiRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CustomerRepository extends ApiRepository
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function checkCustomerNumber(string $customerNumber, int $campaign): bool
    {
        $customer = $this->model->query()
            ->where('customer_number', $customerNumber)
            ->where('campaign_id', $campaign)->first();

        if ($customer) {
            return TRUE;
        }

        return FALSE;
    }

    public function getByUuid(string $uuid): ?Model
    {
        return $this->model->query()->with(['history'])->where('uuid', $uuid)->first();
    }

    public function getByCampaign(int $campaign, int $page = 1, int $perPage = 10, string $search = ''): array
    {
        $customers = $this->model->query()
            ->where('campaign_id', $campaign)
            ->when($search, function ($query) use($search) {
                return $query->where('customer_number', 'like', "%{$search}%");
            })
            ->paginate(
                $perPage,
                ['id', 'campaign_id', 'name', 'email', 'customer_number', 'card_number', 'last_login', 'uuid', 'country_code'],
                'page',
                $page
            );

        $timezone = auth()->user('staff')->timezone ?? 'UTC';

        $items = collect($customers->items())->map(function ($item) use($timezone) {
            $data = array(
                'uuid' => $item['uuid'],
                'name' => $item['name'],
                'email' => $item['email'],
                'card' => $item['card'],
                'number' => $item['number'],
                'last_login' => $item['last_login']
            );

            if ($data['last_login']) {
				$data['last_login'] = $data['last_login']->timezone($timezone);
			}

            return $data;
        });

        return [
            'data' => $items->toArray(),
            'meta' => [
                'current_page' => $customers->currentPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
            ],
        ];
    }

    public function store(array $attributes): Model
    {
        $user = new $this->model;

        if (isset($attributes['avatar_media_changed']) && $attributes['avatar_media_changed']) {
			$avatar = $attributes['avatar'];

            if ($avatar) {
                $user
					->addMedia($avatar)
					->sanitizingFileName(fn($fileName) => strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName)))
					->toMediaCollection('avatar');
            }
		}

        $user->account_id = $attributes['account_id'];
        $user->campaign_id = $attributes['campaign_id'];
        $user->created_by = $attributes['created_by'];
        $user->role = 1;
        $user->active = (bool) $attributes['active'];
        $user->country_code = $attributes['country_code'];
        $user->country_isd_code = $attributes['country_isd_code'];
        $user->customer_number = $attributes['customer_number'];
        $user->card_number = $attributes['card_number'];
        $user->name = $attributes['name'];
        $user->email = $attributes['email'];
        $user->password = bcrypt($attributes['password']);
        $user->language = $attributes['language'];
        $user->locale = $attributes['locale'];
        $user->timezone = $attributes['timezone'];
        $user->signup_ip_address = $attributes['ip_address'];
        $user->verification_code = $attributes['verification_code'];
        $user->expired_date = Carbon::now()->addYear()->format('Y-m-d H:i:s');
        $user->save();

        return $user;
    }
}
