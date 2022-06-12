<?php

namespace Platform\Models;

use Money\Money;
use Money\Currency;
use NumberFormatter;
use Money\Currencies\ISOCurrencies;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\PlanFactory;

use App\ {
  Staff,
  Customer
};
use Platform\Models\ {
  Reward,
  Campaign,
  Segment,
  Business
};
use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Database\Eloquent\Model;
use Money\Formatter\IntlMoneyFormatter;
use Dyrynda\Database\Support\GeneratesUuid;

class Plan extends Model
{
    use GeneratesUuid;
    use HasFactory;

    const DEFAULT_CURRENCY = 'TTD';
    const DEFAULT_BILLING_INTERVAL = 'month';

    protected $table = 'plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'name',
      'limitations'
    ];

    /**
     * Append programmatically added columns.
     *
     * @var array
     */
    protected $appends = [
        'user_count',
        'price_formatted'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Field mutators.
     *
     * @var array
     */
    protected $casts = [
        'uuid' => EfficientUuid::class,
        'limitations' => 'json',
        'meta' => 'json'
    ];

    /**
     * Create a new factory instance for the Plan model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
      return PlanFactory::new();
    }

    /**
     * Date/time fields that can be used with Carbon.
     *
     * @return array
     */
    public function getDates()
    {
        return [];
    }

    public static function boot()
    {
        parent::boot();

        // On update
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->user()->id;
            }
        });

        // On create
        self::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->user()->id;
            }
        });
    }

    /**
     * Form for creating a new record, per role
     *
     * @return array
     */
    public static function getCreateForm()
    {
        $product_id = null;

        if (config('general.payment_provider') == 'stripe') {
            $product_id = ['type' => 'text', 'column' => 'product_id_stripe', 'text' => __('Stripe subscription ID'), 'validate' => 'nullable|max:128', 'required' => false];
        }

        if (config('general.payment_provider') == 'paddle') {
            $product_id = ['type' => 'text', 'column' => 'product_id_paddle', 'text' => __('Paddle subscription ID'), 'validate' => 'nullable|max:128', 'required' => false];
        }

        if (config('general.payment_provider') == '2checkout') {
            $product_id = ['type' => 'text', 'column' => 'product_id_2checkout', 'text' => __('2Checkout subscription ID'), 'validate' => 'nullable|max:128', 'required' => false];
        }

        if (config('general.payment_provider') == 'paypal') {
            $product_id = ['type' => 'text', 'column' => 'product_id_paypal', 'text' => __('Paypal subscription ID'), 'validate' => 'nullable|max:128', 'required' => false];
        }

        $owner = [
            'tab1' => [
                'text' => __('General'),
                'subs' => [
                    'sub1' => [
                        'items' => [
                            ['type' => 'text', 'column' => 'name', 'text' => __('Plan Name'), 'validate' => 'required|max:32', 'required' => true],
                            ['type' => 'currency', 'prefix' => auth()->user()->getCurrencyFormat('symbol'), 'suffix' => auth()->user()->getCurrency(), 'column' => 'price', 'text' => __('Monthly Price'), 'validate' => 'required|decimal:' . auth()->user()->getCurrencyFormat('fraction_digits') . '|min:0|max:1000000', 'required' => true]
                        ]
                    ]
                ]
            ],
            'tab2' => [
                'text' => __('Limitations'),
                'subs' => [
                    'sub1' => [
                        'items' => [
                            [
                                'type' => 'number',
                                'column' => 'limitations->customers',
                                'text' => __('Customers'),
                                'validate' => 'required|integer|min:0|max:1000000',
                                'vvalidate' => 'required|numeric|min_value:0|max_value:1000000',
                                'required' => true
                            ],
                            [
                                'type' => 'number',
                                'column' => 'limitations->campaigns',
                                'text' => __('Website'),
                                'validate' => 'required|integer|min:0|max:10000',
                                'vvalidate' => 'required|numeric|min_value:0|max_value:10000',
                                'required' => true
                            ],
                            [
                                'type' => 'number',
                                'column' => 'limitations->rewards',
                                'text' => __('Reward Offer'),
                                'validate' => 'required|integer|min:0|max:10000',
                                'vvalidate' => 'required|numeric|min_value:0|max_value:10000',
                                'required' => true
                            ],
                            [
                                'type' => 'number',
                                'column' => 'limitations->businesses',
                                'text' => __('Stores'),
                                'validate' => 'required|integer|min:0|max:10000',
                                'vvalidate' => 'required|numeric|min_value:0|max_value:10000',
                                'required' => true
                            ],
                            [
                                'type' => 'number',
                                'column' => 'limitations->staff',
                                'text' => __('Staff'),
                                'validate' => 'required|integer|min:0|max:10000',
                                'vvalidate' => 'required|numeric|min_value:0|max_value:10000',
                                'required' => true
                            ],
                            [
                                'type' => 'number',
                                'column' => 'limitations->segments',
                                'text' => __('Segments'),
                                'validate' => 'required|integer|min:0|max:10000',
                                'vvalidate' => 'required|numeric|min_value:0|max_value:10000',
                                'required' => true
                            ],
                        ]
                    ]
                ]
            ]
        ];

        if ($product_id !== null) array_push($owner['tab1']['subs']['sub1']['items'], $product_id);

        $reseller = $owner;
        $user = $owner;

        return [
            1 => $owner,
            2 => $reseller,
            3 => $user
        ];
    }

    /**
     * Extra columns used in select queries, exposed in json response
     *
     * @return array
     */
    public static function getExtraSelectColumns()
    {
        $owner = ['uuid'];
        $reseller = $owner;
        $user = $owner;

        return [
            1 => $owner,
            2 => $reseller,
            3 => $user
        ];
    }

    /**
     * Extra columns used in select queries, hidden from json response
     *
     * @return array
     */
    public static function getExtraQueryColumns()
    {
        $owner = ['id', 'created_by'];
        $reseller = $owner;
        $user = $owner;

        return [
            1 => $owner,
            2 => $reseller,
            3 => $user
        ];
    }

    /**
     * Generic settings
     *
     * actions: add actions column (true / false)
     *
     * @return array
     */
    public static function getSettings()
    {
        $owner = [
            'select_all' => true,
            'actions' => true,
            'create' => true,
            'actions_width' => '90px'
        ];
        $reseller = $owner;
        $user = $owner;

        return [
            1 => $owner,
            2 => $reseller,
            3 => $user
        ];
    }

    /**
     * Language variables
     *
     * @return array
     */
    public static function getTranslations()
    {
        return [
            'items' => __('Plans'),
            'edit_item' => __('Edit plan'),
            'create_item' => __('Create plan'),
        ];
    }

    /**
     * Define per role if and what they can see
     *
     * all: all records from all accounts
     * account: all records from the current account
     * personal: only records the current user has created
     * created_by: only records created by the user id defined like created_by:1
     * none: this role has no permission
     *
     * @return array
     */
    public static function getPermissions()
    {
        $owner = ['view' => 'all', 'delete' => 'all', 'update' => 'all', 'create' => true];
        $reseller = $owner;
        $user = ['view' => 'personal', 'delete' => 'personal', 'update' => 'personal', 'create' => true];

        return [
            1 => $owner,
            2 => $reseller,
            3 => $user
        ];
    }

    /**
     * The headers for the data table, per role
     *
     * @return array
     */
    public static function getHeaders()
    {
        $owner = [
            ['visible' => true, 'value' => 'name', 'text' => __('Plan Name'), 'align' => 'left', 'sortable' => false],
            ['visible' => true, 'value' => 'price', 'type' => 'currency', 'text' => __('Monthly Price'), 'align' => 'right', 'sortable' => true, 'default_order' => true],
            ['visible' => true, 'value' => 'limitations->customers', 'json' => 'limitations->customers', 'type' => 'number', 'text' => __('Customers'), 'align' => 'right', 'sortable' => false],
            ['visible' => true, 'value' => 'limitations->campaigns', 'json' => 'limitations->campaigns', 'type' => 'number', 'text' => __('Website'), 'align' => 'right', 'sortable' => false],
            ['visible' => true, 'value' => 'limitations->rewards', 'json' => 'limitations->rewards', 'type' => 'number', 'text' => __('Reward Offer'), 'align' => 'right', 'sortable' => false],
            ['visible' => true, 'value' => 'limitations->businesses', 'json' => 'limitations->businesses', 'type' => 'number', 'text' => __('Stores'), 'align' => 'right', 'sortable' => false],
            ['visible' => true, 'value' => 'limitations->staff', 'json' => 'limitations->staff', 'type' => 'number', 'text' => __('Staff'), 'align' => 'right', 'sortable' => false],
            ['visible' => true, 'value' => 'limitations->segments', 'json' => 'limitations->segments', 'type' => 'number', 'text' => __('Segments'), 'align' => 'right', 'sortable' => false],
            ['visible' => true, 'value' => 'user_count', 'type' => 'number', 'exclude_from_select' => true, 'text' => __('Merchants'), 'align' => 'right', 'sortable' => false]
        ];
        $reseller = $owner;
        $user = $owner;

        return [
            1 => $owner,
            2 => $reseller,
            3 => $user
        ];
    }

    /**
     * The columns used for searching the table
     *
     * @return array
     */
    public static function getSearchColumns()
    {
        $owner = ['name'];
        $reseller = $owner;
        $user = $owner;

        return [
            1 => $owner,
            2 => $reseller,
            3 => $user
        ];
    }

    /**
     * Available actions for data table row, per role
     *
     * @return array
     */
    public static function getActions()
    {
        $owner = [
            ['text' => __('Edit'), 'action' => 'edit', 'icon' => 'edit', 'color' => 'secondary', 'dark' => false],
            /*['divider'],*/
            ['text' => __('Delete'), 'action' => 'delete', 'icon' => 'delete', 'color' => 'secondary', 'dark' => true]
        ];

        $reseller = $owner;
        $user = $owner;

        return [
            1 => $owner,
            2 => $reseller,
            3 => $user
        ];
    }

    /**
     * Get user count for plan.
     *
     * @return string
     */
    public function getUserCountAttribute()
    {
        $users = $this->users()->get();

        return $users->count();
    }

    /**
     * Get Price Formatted
     */
    public function getPriceFormattedAttribute()
    {
        $money = new Money(
            $this->attributes['price'],
            new Currency($this->attributes['currency_code'] ?? self::DEFAULT_CURRENCY)
        );

        $currencies = new ISOCurrencies();

        $numberFormatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        return "{$moneyFormatter->format($money)}";
    }

    /**
     * Get plans for billing
     */
    public static function getPlansForBilling($current = null, string $currency_code = 'TTD')
    {
        $plans = self::query()
            ->where('role', 3)
            ->where('currency_code', $currency_code)
            ->where('active', 1)
            ->orderBy('price', 'asc')
            ->get();

        return $plans->map(function ($plan) use($current) {
            $limitations_customers = is_string($plan->limitations['customers'])
                ? ucfirst($plan->limitations['customers'])
                : $plan->limitations['customers'];

            $limitations_rewards = is_string($plan->limitations['rewards'])
                ? ucfirst($plan->limitations['rewards'])
                : $plan->limitations['rewards'];

            return [
                'id'              => $plan->id,
                'name'            => $plan->name,
                'price'           => $plan->price / 100,
                'currency'        => $plan->currency_code,
                'price_formatted' => $plan->price_formatted,
                'interval'        => 'mo',
                'customers'       => $limitations_customers,
                'campaigns'       => $plan->limitations['campaigns'],
                'rewards'         => $limitations_rewards,
                'businesses'      => $plan->limitations['businesses'],
                'staff'           => $plan->limitations['staff'],
                'segments'        => $plan->limitations['segments'],
                'upgradeable'     => ($current && $current->price < $plan->price),
            ];
        })->toArray();
    }

    /**
     * Get plans for site display
     *
     * @return array
     */
    public static function getPlansForSite(string $currency_code = self::DEFAULT_CURRENCY)
    {
        $plans = self::query()
            ->where('role', 3)
            ->where('currency_code', env('DEFAULT_CURRENCY', $currency_code))
            ->where('active', 1)
            ->orderBy('price', 'asc')
            ->get();

        return $plans->map(function ($plan) {
            $limitations_customers = is_string($plan->limitations['customers'])
                ? ucfirst($plan->limitations['customers'])
                : $plan->limitations['customers'];

            $limitations_rewards = is_string($plan->limitations['rewards'])
                ? ucfirst($plan->limitations['rewards'])
                : $plan->limitations['rewards'];

            $limitations_businesses = is_string($plan->limitations['businesses'])
                ? ucfirst($plan->limitations['businesses'])
                : $plan->limitations['businesses'];

            $limitations_staff = is_string($plan->limitations['staff'])
                ? ucfirst($plan->limitations['staff'])
                : $plan->limitations['staff'];

            return [
                'price' => $plan->price_formatted,
                'customers' => $limitations_customers,
                'campaigns' => $plan->limitations['campaigns'],
                'rewards' => $limitations_rewards,
                'businesses' => $limitations_businesses,
                'staff' => $limitations_staff,
                'segments' => $plan->limitations['segments'],
            ];
        })->toArray();
    }

    /**
     * Relationships
     * -------------
     */
    public function users()
    {
        return $this->hasMany(\App\User::class, 'plan_id', 'id');
    }

    public static function checkingUserPlanLimitations($user, $model)
    {
        $data = self::find($user->plan_id);

        if ($data) {
            if ($model == 'Platform\Models\Reward') {
                $reward = Reward::where('created_by', $user->id)->count();

                if ($reward >= intval($data->limitations['rewards'])) {
                    return [
                        'error' => true,
                        'message' => 'You have limit excedeed to create a reward.'
                    ];
                }
            }

            if ($model == 'App\Staff') {
                $staff = Staff::where('created_by', $user->id)->count();

                if ($staff >= intval($data->limitations['staff'])) {
                    return [
                        'error' => true,
                        'message' => 'You have limit excedeed to create a staff.'
                    ];
                }
            }

            if ($model == 'Platform\Models\Campaign') {
                $campaign = Campaign::where('created_by', $user->id)->count();

                if ($campaign >= intval($data->limitations['campaigns'])) {
                    return [
                        'error' => true,
                        'message' => 'You have limit excedeed to create a campaign.'
                    ];
                }
            }

            if ($model == 'Platform\Models\Segment') {
                $segment = Segment::where('created_by', $user->id)->count();

                if ($segment >= intval($data->limitations['segments'])) {
                    return [
                        'error' => true,
                        'message' => 'You have limit excedeed to create a segment.'
                    ];
                }
            }

            if ($model == 'App\Customer') {
                $customer = Customer::where('created_by', $user->id)->count();

                if($customer >= intval($data->limitations['customers'])) {
                    return [
                        'error' => true,
                        'message' => 'You have limit excedeed to create a customer.'
                    ];
                }
            }

            if ($model == 'Platform\Models\Business') {
                $business = Business::where('created_by', $user->id)->count();

                if($business >= intval($data->limitations['businesses'])) {
                    return [
                        'error' => true,
                        'message' => 'You have limit excedeed to create a website.'
                    ];
                }
            }

            return [
                'error' => false,
                'message' => 'success.'
            ];
        }

        return [
            'error' => true,
            'message' => 'you haven\'t chosen a plan.'
        ];
    }
}
