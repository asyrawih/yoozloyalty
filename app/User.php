<?php

namespace App;

use App\Models\BillingInvoice;
use App\Models\SettingLegal;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Platform\Models\PlanChangeRequest;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use CommerceGuys\Intl\Currency\CurrencyRepository;
use Illuminate\Support\Facades\DB;
use App\Scopes\AccountScope;
use App\Traits\SmsApiTrait;
use Illuminate\Support\Carbon;
use libphonenumber\{
  PhoneNumberUtil,
  PhoneNumberFormat
};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravolt\Avatar\Avatar;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

class User extends Authenticatable implements JWTSubject, HasMedia, MustVerifyEmail
{
    use GeneratesUuid;
    use SoftDeletes;
    use Notifiable;
    use InteractsWithMedia;
    use SmsApiTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','account_id'
    ];

    /**
     * Append programmatically added columns.
     *
     * @var array
     */
    protected $appends = [
        'account_active',
        'avatar',
        'plan_name',
        'industry_name',
        'plan_limitations',
        'expires_at',
        'demo',
        'pending_plan_request',
        'show_extra_actions'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'uuid' => EfficientUuid::class,
        'email_verified_at' => 'datetime',
        'expires' => 'datetime',
        'social' => 'json',
        'settings' => 'json',
        'tags' => 'json',
        'attributes' => 'json',
        'meta' => 'json'
    ];

    /**
     * Create a new factory instance for the User model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
      return UserFactory::new();
    }

    public function registerMediaCollections() : void
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile();

        $this
            ->addMediaCollection('store_logo')
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null) : void
    {
        $this
            ->addMediaConversion('avatar')
            ->width(512)
            ->height(512)
            ->performOnCollections('avatar')
            ->nonQueued();
    }

    public function uuidColumn()
    {
        return 'uuid';
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
      return [];
    }

    public static function boot()
    {
        parent::boot();

        if (auth()->check()) {
            static::addGlobalScope(new AccountScope(auth()->user()));
        }

        // On select
        static::retrieved(function ($model) {});

        // On update
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        // On create
        self::creating(function ($model) {
            if (auth()->check()) {
                $model->account_id = auth()->user()->account_id;
                $model->created_by = auth()->id();
            }
        });

        // Created
        self::created(function ($model) {
            if (auth()->check()) {}

            $model->save();
        });

        // Deleted
        self::deleting(function ($model) {
            if (auth()->check() && ! $model->deleted_at) {
                $model->deleted_by = auth()->id();

                $model->save();
            }
        });

        self::restored(function ($model) {
            $model->deleted_by = null;

            $model->save();
        });
    }

    /**
     * Form for creating a new record, per role
     *
     * @return array
     */
    public static function getCreateForm() {
        // $user = auth()->user();
        $user = self::query()->find(auth()->id());

        $masterAdmin = [
            'tab1' => [
                'text' => __('Subscription'),
                'subs' => [
                    'sub1' => [
                        'items' => [
                            [
                                'type' => 'relation',
                                'relation' => [
                                    'type' => 'belongsTo',
                                    'with' => 'plan',
                                    'pk' => 'id',
                                    'val' => 'name',
                                    'where' => "`role` = 3 AND `currency_code` = '{$user->getCurrency()}' AND `active` = 1",
                                    'orderBy' => 'price',
                                    'order' => 'asc'
                                ],
                                'column' => 'plan_id',
                                'text' => __('Plan'),
                                'required' => false,
                                'validate' => 'nullable'
                            ],
                            [
                                'type' => 'date_time',
                                'default' => '',
                                'format' => 'LLLL',
                                'column' => 'expires',
                                'text' => __('Expiration date'),
                                'validate' => 'nullable',
                                'required' => false,
                                'icon' => 'calendar_today'
                            ],
                        ]
                    ]
                ]
            ],
            'tab2' => [
                'text' => __('Personal'),
                'subs' => [
                    'sub1' => [
                        'items' => [
                            [
                                'type' => 'text',
                                'column' => 'name',
                                'text' => __('Name'),
                                'validate' => 'required|max:32',
                                'required' => true,
                                'autocomplete' => 'name'
                            ],
                            [
                                'type' => 'email',
                                'column' => 'email',
                                'text' => __('E-mail address'),
                                'validate' => 'nullable|email|max:64',
                                'icon' => 'email',
                                'autocomplete' => 'email'
                            ],
                            [
                                'type' => 'phone',
                                'column' => 'phone_personal',
                                'text' => __('Mobile Number'),
                                'autocomplete' => 'tel'
                            ],
                            [
                                'type' => 'date_picker',
                                'column' => 'date_of_birth',
                                'text' => __('Date of Birth')
                            ],
                            [
                                'type' => 'country',
                                'column' => 'country_id',
                                'text' => __('Country')
                            ],
                            [
                                'type' => 'city',
                                'column' => 'city',
                                'text' => __('City')
                            ],
                            [
                                'type' => 'image',
                                'image' => [
                                    'thumb_max_width' => '140px',
                                    'thumb_max_height' => '140px'
                                ],
                                'column' => 'avatar',
                                'text' => __('Avatar'),
                                'validate' => 'nullable|image|max:1000',
                                'vvalidate' => 'image|size:1000',
                                'hint' => __('Maximum file size is 1 Mb'),
                                'icon' => 'attach_file'
                            ],
                            // [
                            //     'type' => 'relation',
                            //     'relation' => [
                            //         'type' => 'belongsTo',
                            //         'permission' => 'all',
                            //         'with' => 'industry',
                            //         'pk' => 'id',
                            //         'val' => 'name',
                            //         'orderBy' => 'name',
                            //         'order' => 'asc'],
                            //         'column' => 'industry_id',
                            //         'text' => __('Industry'),
                            //         'validate' => 'required',
                            //         'required' => true
                            //     ],
                            // [
                            //     'type' => 'currency',
                            //     'prefix' => $user->getCurrencyFormat('symbol'),
                            //     'suffix' => $user->getCurrency(),
                            //     'column' => 'joining_fee',
                            //     'text' => __('Joining Fee'),
                            //     'validate' => "required|decimal:{$user->getCurrencyFormat('fraction_digits')}|min:0|max:1000000",
                            //     'required' => true
                            // ],
                            /*['type' => 'boolean', 'default' => true, 'column' => 'active', 'text' => __('Active'), 'validate' => 'nullable']*/
                        ]
                    ]
                ]
            ],
            'tab3' => [
                'text' => __('Password'),
                'subs' => [
                    'sub1' => [
                        'items' => [
                            [
                                'type' => 'password',
                                'column' => 'password',
                                'text' => __('Password'),
                                'hint_edit' => __('Leave blank to keep current password'),
                                'required_create' => true
                            ],
                            [
                                'type' => 'button_reset_password',
                                'text' => __('Send Reset Password Email'),
                                'edit_only' => true
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $normalAdmin = $masterAdmin;
        $merchant = $masterAdmin;

        return [
            1 => $masterAdmin,
            2 => $normalAdmin,
            3 => $merchant
        ];
    }

    /**
     * Form for importing bulk data, per role
     *
     * @return array
     */
    public static function getImportForm()
    {
        $owner = [
            'tab1' => [
                'text' => __('General'),
                'subs' => [
                    'sub1' => [
                        'items' => [
                            [
                                'type' => 'import',
                                'column' => 'file',
                                'text' => __('Select file'),
                                'validate' => 'required',
                                'required' => true,
                            ],
                        ],
                    ],
                ],
            ],
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
     * Extra columns used in select queries, exposed in json response
     *
     * @return array
     */
    public static function getExtraSelectColumns()
    {
        $masterAdmin = ['uuid', 'plan_id', 'industry_id', 'deleted_at'];
        $normalAdmin = $masterAdmin;
        $merchant = $masterAdmin;

        return [
            1 => $masterAdmin,
            2 => $normalAdmin,
            3 => $merchant
        ];
    }

    /**
     * Extra columns used in select queries, hidden from json response
     *
     * @return array
     */
    public static function getExtraQueryColumns()
    {
        $masterAdmin = ['id', 'created_by'];
        $normalAdmin = $masterAdmin;
        $merchant = $masterAdmin;

        return [
            1 => $masterAdmin,
            2 => $normalAdmin,
            3 => $merchant
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
        $masterAdmin = [
            'select_all' => true,
            'actions' => true,
            'create' => true,
            'import' => true,
            'detail' => [
                'show' => true,
                'type' => 'plan_approval',
                'title' => "Plan change request"
            ]
        ]; //, 'actions_width' => '200px'
        $normalAdmin = $masterAdmin;
        $merchant = $masterAdmin;

        return [
            1 => $masterAdmin,
            2 => $normalAdmin,
            3 => $merchant
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
            'items' => __('Merchants'),
            'edit_item' => __('Edit merchant'),
            'create_item' => __('Create merchant'),
            'import' => __('Import merchant'),
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
        $masterAdmin = [
            'view' => 'all',
            'delete' => 'all',
            'update' => 'all',
            'create' => true,
            'import' => 'all'
        ];

        $normalAdmin = [
            'view' => 'all',
            'delete' => 'all',
            'update' => 'all',
            'create' => true,
            'import' => 'all'
        ];

        $merchant = [
            'view' => 'personal',
            'delete' => 'none',
            'update' => 'personal',
            'create' => false,
        ];

        return [
            1 => $masterAdmin,
            2 => $normalAdmin,
            3 => $merchant
        ];
    }

    /**
     * The headers for the data table, per role
     *
     * @return array
     */
    public static function getHeaders()
    {
        // $user = auth()->user();
        $user = self::query()->find(auth()->id());

        $masterAdmin = [
            [
                'visible' => true,
                'value' => 'avatar',
                'exclude_from_select' => true,
                'width' => '60px',
                'text' => __('Avatar'),
                'align' => 'left',
                'sortable' => false,
            ],
            [
                'visible' => true,
                'value' => 'name',
                'text' => __('Merchant Name'),
                'align' => 'left',
                'sortable' => true,
            ],
            [
                'visible' => true,
                'value' => 'email',
                'text' => __('E-mail'),
                'align' => 'left',
                'sortable' => true,
            ],
            [
                'visible' => true,
                'value' => 'logins',
                'type' => 'number',
                'text' => __('Logins'),
                'align' => 'center',
                'sortable' => true,
            ],
            [
                'visible' => true,
                'value' => 'last_login',
                'type' => 'date_time',
                'format' => 'ago',
                'text' => __('Last login'),
                'align' => 'left',
                'sortable' => true,
                'default_order' => true,
            ],
            [
                'visible' => true,
                'value' => 'plan_name',
                'exclude_from_select' => true,
                'text' => __('Plan'),
                'align' => 'left',
                'sortable' => true,
            ],
            // [
            //     'visible' => true,
            //     'value' => 'industry_name',
            //     'exclude_from_select' => true,
            //     'text' => __('Industry'),
            //     'align' => 'left',
            //     'sortable' => true,
            // ],
            [
                'visible' => true,
                'value' => 'expires',
                'type' => 'date_time',
                'format' => 'lll',
                'text' => __('Expires'),
                'align' => 'left', 'sortable' => true,
            ],
            // [
            //     'visible' => true,
            //     'type' => 'reward_value',
            //     'prefix' => $user->getCurrencyFormat('symbol'),
            //     'suffix' => $user->getCurrency(),
            //     'fraction_digits' => $user->getCurrencyFormat('fraction_digits'),
            //     'value' => 'joining_fee',
            //     'text' => __('Joining Fee'),
            //     'align' => 'left',
            //     'sortable' => true,
            // ],
            /*['visible' => true, 'value' => 'active', 'text' => __('Active'), 'align' => 'center', 'sortable' => true, 'type' => 'boolean'],*/
        ];

        $normalAdmin = $masterAdmin;
        $merchant = $masterAdmin;

        return [
            1 => $masterAdmin,
            2 => $normalAdmin,
            3 => $merchant
        ];
    }

    /**
     * The columns used for searching the table
     *
     * @return array
     */
    public static function getSearchColumns()
    {
        $owner = ['name', 'email'];
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
        $masterAdmin = [
            [
                'text' => __('Edit'),
                'action' => 'edit',
                'icon' => 'edit',
                'color' => 'secondary',
                'dark' => false,
            ],
            [
                'text' => __('Delete'),
                'action' => 'delete',
                'icon' => 'delete',
                'color' => 'secondary',
                'dark' => true,
            ],
            [
                'divider'
            ],
            [
                'text' => __('Log in to account'),
                'action' => 'log_in_as',
                'icon' => 'vpn_key',
                'color' => 'secondary',
                'dark' => true,
            ],
        ];

        $normalAdmin = [
            [
                'text' => __('Edit'),
                'action' => 'edit',
                'icon' => 'edit',
                'color' => 'secondary',
                'dark' => false
            ]
        ];

        $normalAdmin = $masterAdmin;

        $merchant = $masterAdmin;

        return [
            1 => $masterAdmin,
            2 => $normalAdmin,
            3 => $merchant
        ];
    }

    /**
     * Get extra actions based on a condition set in
     * @see User::getShowExtraActionsAttribute()
     *
     * @return array[]
     */
    public static function getExtraActions()
    {
        $masterAdmin = [
            ['divider'],
            [
                'text' => __('Plan Change Request'),
                'action' => 'detail',
                'icon' => 'info',
                'color' => 'secondary',
                'dark' => true,
            ],
            // ['text' => __('Approve'), 'action' => 'approve_plan', 'icon' => 'check', 'color' => 'success', 'dark' => true],
            // ['text' => __('Reject'), 'action' => 'reject_plan', 'icon' => 'close', 'color' => 'red', 'dark' => true],
        ];

        $normalAdmin = $masterAdmin;
        $merchant = $masterAdmin;

        return [
            1 => $masterAdmin,
            2 => $normalAdmin,
            3 => $merchant
        ];
    }

    /**
     * Get what data is shown inside the detail dialog
     */
    public function getDetailData()
    {
        $links = [
            [
                'link' => route('planApprovalRoute'),
                'text' => 'approve',
                'color' => 'success'
            ],
            [
                'link' => route('planApprovalRoute'),
                'text' => 'reject',
                'color' => 'error'
            ]
        ];

        return [
            'plan_change_request' => $this->plan_change_request()->first(),
            'links' => $links
        ];
    }

    /**
     * Get plan.
     *
     * @return string
     */
    public function getPlanNameAttribute()
    {
        if ($this->plan_id !== null) {
            if($this->pending_plan_request !== null){
                if($this->pending_plan_request->type === 'change'){
                    $transKey = "has_plan_change_request";
                } else {
                    $transKey = "has_plan_renew_request";
                }

                return __('app.'.$transKey, [
                    'current_plan' => $this->plan->name,
                    'new_plan' => $this->plan_change_request()->first()->newPlan()->first()->name
                ]);
            }

            return $this->plan->name;
        } else {
            return __('Trial');
        }
    }

     /**
     * Get industry name.
     *
     * @return string
     */
    public function getIndustryNameAttribute()
    {
        if ($this->industry_id !== null) {
            return $this->industry->name;
        } else {
            return __('-');
        }
    }

    /**
     * Get plan limiations.
     *
     * @return string
     */
    public function getPlanLimitationsAttribute()
    {
        if ($this->plan_id !== null) {
            return $this->plan->limitations;
        } else {
            return [
                'customers' => 3,
                'campaigns' => 1,
                'rewards' => 2,
                'businesses' => 1,
                'staff' => 1,
                'segments' => 2
            ];
        }
    }

    /**
     * Is (in) demo account.
     *
     * @return string
     */
    public function getDemoAttribute()
    {
        return env('APP_DEMO', false);
    }

    /**
     * Account is active.
     *
     * @return string
     */
    public function getAccountActiveAttribute()
    {
        if ($this->account !== null && $this->account->expires !== null) {
            return ! $this->account->expires->addDays(config('system.grace_period_days'))->isPast();
        } else {
            return true;
        }
    }

    /**
     * Expiration date in user timezone.
     *
     * @return date
     */
    public function getExpiresAtAttribute()
    {
        if ($this->expires !== null) {
            return $this->expires->timezone($this->getTimezone())->toDateTimeString();
        } else {
            return null;
        }
    }

    /**
     * Get avatar.
     *
     * @return string for use in <img> src
     */
    public function getAvatarAttribute()
    {
        if ($this->getFirstMediaUrl('avatar') !== '') {
            return $this->getFirstMediaUrl('avatar', 'avatar');
        } else {
            // return (string) Avatar::create(strtoupper($this->name))->toBase64();
            $config = config('laravolt.avatar');

            return (string) (new Avatar($config))->create(strtoupper($this->name))->toBase64();
        }
    }

    /**
     * Get avatar.
     *
     * @return string for use in <img> src
     */
    public function getStoreLogoAttribute()
    {
        if ($this->getFirstMediaUrl('store_logo') !== '') {
            return $this->getFirstMediaUrl('store_logo');
        } else {
            return null;
        }
    }

    /**
     * Check if user has a pending plan change request
     *
     * @return BaseModel|\Illuminate\Database\Eloquent\Relations\HasMany|object|null
     */
    public function getPendingPlanRequestAttribute()
    {
        return $this->plan_change_request()->first();
    }

    /**
     * Decide whether to show the extra actions defined in
     * @see User::getExtraActions()
     *
     * @return bool
     */
    public function getShowExtraActionsAttribute()
    {
        return $this->plan_change_request()->first() !== null;
    }

    /**
     * Money formatting
     */
    public function formatMoney($amount, $currency = 'TTD', $formatHtml = false)
    {
        if ($currency == null) $currency = 'TTD';

        $value = Money::{$currency}($amount);

        $currencies = new \Money\Currencies\ISOCurrencies();

        $numberFormatter = new NumberFormatter(
            $this->getLanguage(),
            NumberFormatter::CURRENCY
        );

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        $amount = $moneyFormatter->format($value);

        if ($formatHtml) {
            $amount = explode($numberFormatter->getSymbol(0), $amount);

            $amount = $amount[0] . '<span class="cents">' . $numberFormatter->getSymbol(0) . $amount[1] . '</span>';
        }

        return $amount;
    }

    /**
     * Date / time formatting
     */
    public function formatDate($date, $format = 'date_medium')
    {
        if ($date !== null) {
            switch ($format) {
                case 'date_medium': $date = $date->timezone($this->getTimezone())->format('d-m-y'); break;
                case 'datetime_medium': $date = $date->timezone($this->getTimezone())->format('d-m-y H:i'); break;
                case 'friendly': $date = $date->timezone($this->getTimezone())->diffForHumans(); break;
            }

            return $date;
        } else {
            return null;
        }
    }

    /**
     * Check if user was online recently.
     *
     * @return boolean
     */
    public function getRecentlyOnline($minutes = 10)
    {
        $lastActivity = strtotime(Carbon::now()->subMinutes($minutes));

        $visit = DB::table('sessions')
            ->whereRaw('user_id = ?', [$this->id])
            ->whereRaw('last_activity >= ?', [$lastActivity])
            ->first();

        return ($visit === null) ? false : true;
    }

    /**
     * User language
     */
    public function getLanguage()
    {
        if ($this->language === NULL) {
            return config('system.default_language');
        } else {
            return $this->language;
        }
    }

    /**
     * User locale
     */
    public function getLocale()
    {
        if ($this->locale === NULL) {
            $language = $this->getLanguage();

            // If there is no default for user's language, use global default
            return config(
                'system.language_defaults.' . $language . '.locale',
                config('system.default_locale')
            );
        } else {
            return $this->locale;
      }
    }

    /**
     * User timezone
     */
    public function getTimezone()
    {
        if ($this->timezone === NULL) {
            $language = $this->getLanguage();
            // If there is no default for user's language, use global default
            return config(
                'system.language_defaults.' . $language . '.timezone',
                config('system.default_timezone')
            );
        } else {
            return $this->timezone;
        }
    }

    /**
     * User currency
     */
    public function getCurrency()
    {
        if ($this->currency_code === NULL) {
            $language = $this->getLanguage();
            // If there is no default for user's language, use global default
            return config('system.language_defaults.' . $language . '.currency', config('system.default_currency'));
        } else {
            return $this->currency_code;
        }
    }

    /**
     * User Phone Personal
     */

    public function getPhonePersonalAttribute()
    {
        if ($this->attributes['phone_personal'] && $this->attributes['country_id']) {
            $country = $this->country;

            $phoneNumberUtil = PhoneNumberUtil::getInstance();

            $phoneNumberObject = $phoneNumberUtil
                ->parse($this->attributes['phone_personal'], $country->code);

            return $phoneNumberUtil->format($phoneNumberObject, PhoneNumberFormat::INTERNATIONAL);
        }

        return $this->attributes['phone_personal'];
    }

    /**
     * Currency decimal points
     */
    public function getCurrencyFormat($get = null)
    {
        $currencyRepository = new CurrencyRepository;

        $currency = $currencyRepository->get($this->getCurrency());

        $format = [
            'numeric_code' => $currency->getNumericCode(),
            'fraction_digits' => $currency->getFractionDigits(),
            'name' => $currency->getName(),
            'symbol' => $currency->getSymbol(),
            'locale' => $currency->getLocale()
        ];

        return ($get === null) ? $format : $format[$get];
    }

    /**
     * Admin totals
     */
    public function getAdminTotals()
    {
        // Totals
        $users = $this->users->where('role', 3)->count();

        return [
            'users' => $users
        ];
    }

    /**
     * Admin stats
     */
    public function getAdminStats($statsPeriod = '7days')
    {
        // Totals
        $totals = $this->getAdminTotals();

        // Period
        if ($statsPeriod == '7days') {
            $from = now()->addDays(-7);
            $to = now();
            $fromPrevious = now()->addDays(-15);
            $toPrevious = now()->addDays(-8);
        }

        // User signups for current period
        $period = new \DatePeriod(
            new \DateTime($from),
            new \DateInterval('P1D'),
            new \DateTime($to)
        );

        $range = [];

        foreach($period as $date){
            $range[$date->format("Y-m-d")] = 0;
        }

        $data = $this->users()
            ->select([
                DB::raw('DATE(`created_at`) as `date`'),
                DB::raw('COUNT(id) as `count`')
            ])
            ->where('role', 3)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('date')
            ->get()
            /*
            ->groupBy(function ($val) {
                return Carbon::parse($val->created_at)->format('d');
            })*/
            ->pluck('count', 'date');

        $dbData = [];
        $total = 0;
        if ($data !== null) {
            foreach($data as $date => $count) {
                $dbData[$date] = (int) $count;
                $total += $count;
            }
        }

        $userSignups = array_replace($range, $dbData);
        $userSignupsTotal = $total;

        // Customer signups for previous period
        $period = new \DatePeriod( new \DateTime($fromPrevious), new \DateInterval('P1D'), new \DateTime($toPrevious));
        $data = $this->users()
            ->select([
                DB::raw('COUNT(id) as `count`')
            ])
            ->where('role', 3)
            ->whereBetween('created_at', [$fromPrevious, $toPrevious])
            ->get()
            ->pluck('count');

        $userSignupsTotalPrevious = (isset($data[0])) ? (int) $data[0] : 0;

        $stats = [
            'total' => $totals,
            'payment_provider' => config('general.payment_provider'),
            'payment_test_mode' => config('general.payment_test_mode'),
            'domain' => $this->app_host,
            'account_host' => config('general.cname_domain'),
            'app_name' => $this->app_name,
            'app_contact' => $this->app_contact,
            'app_mail_name_from' => $this->app_mail_name_from,
            'app_mail_address_from' => $this->app_mail_address_from,
            'users' => [
                'signupsCurrentPeriod' => $userSignups,
                'signupsCurrentPeriodTotal' => $userSignupsTotal,
                'signupsPreviousPeriodTotal' => $userSignupsTotalPrevious,
                'signupsChange' => $userSignupsTotal - $userSignupsTotalPrevious
            ]
        ];

        return $stats;
    }

    /**
     * User totals
     */
    public function getUserTotals()
    {
        // Totals
        $totalBusinesses = $this->businesses->count();
        $totalStaff = $this->staff->count();
        $totalRewards = $this->rewards->count();
        $totalCampaigns = $this->campaigns->count();
        $totalCustomers = $this->customers->count();
        $totalSegments = $this->segments->count();

        // Onboarding step
        $onboardingStep = 6;
        if ($totalCustomers == 0) $onboardingStep = 5;
        if ($totalCampaigns == 0) $onboardingStep = 4;
        if ($totalRewards == 0) $onboardingStep = 3;
        if ($totalStaff == 0) $onboardingStep = 2;
        if ($totalBusinesses == 0) $onboardingStep = 1;

        return [
            'onboardingStep' => $onboardingStep,
            'customers' => $totalCustomers,
            'campaigns' => $totalCampaigns,
            'rewards' => $totalRewards,
            'staff' => $totalStaff,
            'businesses' => $totalBusinesses,
            'segments' => $totalSegments,
        ];
    }

    /**
     * User stats
     */
    public function getUserStats($statsPeriod = '7days')
    {
        // Totals
        $totals = $this->getUserTotals();

        // Period
        if ($statsPeriod == '7days') {
            $from = now()->addDays(-7);
            $to = now();
            $fromPrevious = now()->addDays(-15);
            $toPrevious = now()->addDays(-8);
        }

        // Customer signups for current period
        $period = new \DatePeriod( new \DateTime($from), new \DateInterval('P1D'), new \DateTime($to));

        $range = [];
        foreach($period as $date){
            $range[$date->format("Y-m-d")] = 0;
        }

        $data = $this->customers()
            ->select([
                DB::raw('DATE(`created_at`) as `date`'),
                DB::raw('COUNT(id) as `count`')
            ])
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('date')
            ->get()
            /*
            ->groupBy(function ($val) {
                return Carbon::parse($val->created_at)->format('d');
            })*/
            ->pluck('count', 'date');

        $dbData = [];
        $total = 0;
        if ($data !== null) {
            foreach($data as $date => $count) {
                $dbData[$date] = (int) $count;
                $total += $count;
            }
        }

        $customerSignups = array_replace($range, $dbData);
        $customerSignupsTotal = $total;

        // Customer signups for previous period
        $period = new \DatePeriod( new \DateTime($fromPrevious), new \DateInterval('P1D'), new \DateTime($toPrevious));
        $data = $this->customers()
            ->select([
                DB::raw('COUNT(id) as `count`')
            ])
            ->whereBetween('created_at', [$fromPrevious, $toPrevious])
            ->get()
            ->pluck('count');

        $customerSignupsTotalPrevious = (isset($data[0])) ? (int) $data[0] : 0;

        // Earnings
        $earnings = $this->getEarningsForPeriod($statsPeriod);

        // Spendings
        $spendings = $this->getSpendingsForPeriod($statsPeriod);

        // Popular rewards
        $popularRewards = $this->getPopularRewards();

        $stats = [
            'total' => $totals,
            'customers' => [
                'signupsCurrentPeriod' => $customerSignups,
                'signupsCurrentPeriodTotal' => $customerSignupsTotal,
                'signupsPreviousPeriodTotal' => $customerSignupsTotalPrevious,
                'signupsChange' => $customerSignupsTotal - $customerSignupsTotalPrevious
            ],
            'popularRewards' => $popularRewards,
            'earnings' => $earnings,
            'spendings' => $spendings
        ];

        return $stats;
    }

    /**
     * Get popular rewards
     *
     * @return string
     */
    public function getPopularRewards($limit = 3)
    {
        $rewards = $this->rewards()
            ->whereHas('campaigns', function($query) {
                $query->where('campaign_reward.active_from', '<', Carbon::now()->toDateTimeString())
                      ->where('campaign_reward.expires_at', '>', Carbon::now()->toDateTimeString());
            })
            ->where('active', 1)
            ->where('number_of_times_redeemed', '>', 0)
            ->orderBy('number_of_times_redeemed', 'desc')
            ->orderBy('points_cost', 'desc')
            ->limit($limit)
            ->get();

        $rewards = $rewards->map(function ($record) {
            return collect($record)->only('title', 'points', 'number_of_times_redeemed');
        });

        return $rewards->toArray();
    }

    /**
     * Get earning stats for period
     *
     * @return string
     */
    public function getEarningsForPeriod($statsPeriod = '7days')
    {
        // Period
        if ($statsPeriod == '7days') {
            $from = now()->addDays(-7);
            $to = now();
            $fromPrevious = now()->addDays(-15);
            $toPrevious = now()->addDays(-8);
        }

        // Customer signups for current period
        $period = new \DatePeriod( new \DateTime($from), new \DateInterval('P1D'), new \DateTime($to));

        $range = [];
        foreach($period as $date){
            $range[$date->format("Y-m-d")] = 0;
        }

        $data = $this->history()
            ->select([
                DB::raw('DATE(`created_at`) as `date`'),
                DB::raw('SUM(points) as `count`')
            ])
            ->whereBetween('created_at', [$from, $to])
            ->where('points', '>', 0)
            ->groupBy('date')
            ->get()
            /*
            ->groupBy(function ($val) {
                return Carbon::parse($val->created_at)->format('d');
            })*/
            ->pluck('count', 'date');

        $dbData = [];
        $total = 0;
        if ($data !== null) {
            foreach($data as $date => $count) {
                $dbData[$date] = (int) $count;
                $total += $count;
            }
        }

        $earnings = array_replace($range, $dbData);
        $earningsTotal = $total;

        // Customer signups for previous period
        $period = new \DatePeriod( new \DateTime($fromPrevious), new \DateInterval('P1D'), new \DateTime($toPrevious));

        $data = $this->history()
            ->select([
                DB::raw('SUM(points) as `count`')
            ])
            ->whereBetween('created_at', [$fromPrevious, $toPrevious])
            ->where('points', '>', 0)
            ->get()
            ->pluck('count');

        $earningsTotalPrevious = (isset($data[0])) ? (int) $data[0] : 0;

        return [
            'earnings' => $earnings,
            'earningsTotal' => $earningsTotal,
            'earningsTotalPrevious' => $earningsTotalPrevious,
            'earningsChange' => $earningsTotal - $earningsTotalPrevious
        ];
    }

    /**
     * Get spending stats for period
     *
     * @return string
     */
    public function getSpendingsForPeriod($statsPeriod = '7days')
    {
        // Period
        if ($statsPeriod == '7days') {
            $from = now()->addDays(-7);
            $to = now();
            $fromPrevious = now()->addDays(-15);
            $toPrevious = now()->addDays(-8);
        }

        // Customer signups for current period
        $period = new \DatePeriod( new \DateTime($from), new \DateInterval('P1D'), new \DateTime($to));

        $range = [];
        foreach($period as $date){
            $range[$date->format("Y-m-d")] = 0;
        }

        $data = $this->history()
            ->select([
                DB::raw('DATE(`created_at`) as `date`'),
                DB::raw('SUM(points) as `count`')
            ])
            ->whereBetween('created_at', [$from, $to])
            ->where('points', '<', 0)
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');

        $dbData = [];
        $total = 0;
        if ($data !== null) {
            foreach($data as $date => $count) {
            $dbData[$date] = abs($count);
            $total += abs($count);
            }
        }

        $spendings = array_replace($range, $dbData);
        $spendingsTotal = $total;

        // Customer signups for previous period
        $period = new \DatePeriod( new \DateTime($fromPrevious), new \DateInterval('P1D'), new \DateTime($toPrevious));
        $data = $this->history()
            ->select([
                DB::raw('SUM(ABS(points)) as `count`')
            ])
            ->whereBetween('created_at', [$fromPrevious, $toPrevious])
            ->where('points', '<', 0)
            ->get()
            ->pluck('count');

        $spendingsTotalPrevious = (isset($total[0])) ? (int) $total[0] : 0;

        return [
            'spendings' => $spendings,
            'spendingsTotal' => $spendingsTotal,
            'spendingsTotalPrevious' => $spendingsTotalPrevious,
            'spendingsChange' => $spendingsTotal - $spendingsTotalPrevious
        ];
    }

    /**
     * Get Subscription Stat
     */
    public function getSubscriptionStat()
    {
        $invoice = $this->invoice;

        $expired = $this->expires ? $this->expires->isPast() : true;

        $emptyObject = (object) array();

        $stat = [
            'active_plan' => $this->plan ?? $emptyObject,
            'expired' => $expired,
            'subscription_expired_at' => $this->expires ? $this->expires->toDayDateTimeString() : null,
            'status' => ($this->plan) ? 'active' : 'trial',
            'order_plan' => $emptyObject,
            'invoice' => $emptyObject,
            'bank' => $emptyObject,
        ];

        if ($invoice && in_array($invoice->status, ['pending', 'confirm'])) {
            $stat['invoice'] = $invoice;
            $stat['bank'] = $invoice->bank;
            $stat['order_plan'] = $invoice->plan;
            $stat['status'] = $invoice->status;
        }

        return $stat;
    }

    /**
     * Relationships
     * -------------
     */

    public function account()
    {
        return $this->belongsTo(\App\User::class, 'account_id', 'id');
    }

    public function plan()
    {
        return $this->belongsTo(\Platform\Models\Plan::class, 'plan_id', 'id');
    }

    public function invoice()
    {
        return $this->belongsTo(BillingInvoice::class, 'remote_customer_id', 'order_id');
    }

    public function previousInvoice()
    {
        return $this->belongsTo(BillingInvoice::class, 'previous_remote_customer_id', 'order_id');
    }

    public function industry()
    {
        return $this->belongsTo(\Platform\Models\Industry::class, 'industry_id', 'id');
    }

    public function users() {
      return $this->hasMany(\App\User::class, 'created_by', 'id');
    }

    public function businesses()
    {
        return $this->hasMany(\Platform\Models\Business::class, 'created_by', 'id');
    }

    public function staff()
    {
        return $this->hasMany(\App\Staff::class, 'created_by', 'id');
    }

    public function rewards()
    {
        return $this->hasMany(\Platform\Models\Reward::class, 'created_by', 'id');
    }

    public function campaigns()
    {
        return $this->hasMany(\Platform\Models\Campaign::class, 'created_by', 'id');
    }

    public function customers()
    {
        return $this->hasMany(\App\Customer::class, 'created_by', 'id');
    }

    public function segments()
    {
        return $this->hasMany(\Platform\Models\Segment::class, 'created_by', 'id');
    }

    public function history()
    {
        return $this->hasMany(\Platform\Models\History::class, 'created_by', 'id');
    }

    public function plan_change_request()
    {
        return $this->hasMany(\Platform\Models\PlanChangeRequest::class, 'created_by', 'id')->with(['previousPlan', 'newPlan'])->where('status', PlanChangeRequest::PENDING_STATUS);
    }

    public function approved_plan_change_request()
    {
        return $this->hasMany(\Platform\Models\PlanChangeRequest::class, 'approved_by', 'id');
    }

    public function settingLegal(){
        return $this->hasMany(SettingLegal::class, 'user_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function points_expiry()
    {
        return $this->hasOne(\App\Models\PointsExpiry::class, 'merchant_user_id', 'id');
    }
}
