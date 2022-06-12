<?php

namespace App;

use Money\Money;
use App\Scopes\AccountScope;
use Spatie\MediaLibrary\HasMedia;
use Money\Formatter\IntlMoneyFormatter;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Dyrynda\Database\Support\GeneratesUuid;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravolt\Avatar\Avatar;

class Customer extends Authenticatable implements JWTSubject, HasMedia, MustVerifyEmail
{
    use GeneratesUuid;
    use Notifiable;
    use InteractsWithMedia;

    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'country_code',
        'customer_number',
        'country_isd_code',
        'expired_date',
        'account_id'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $appends = [
        'account_active',
        'avatar',
        'points',
        'campaign_text',
        'number',
        'events',
        'card',
        'customer_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'history',
        'campaign',
        'events',
        'password',
        'remember_token',
        'account',
        'global_points',
        'account_id',
        'remote_customer_id',
        'previous_remote_customer_id',
        'domain',
        'company',
        'company_slug',
        'files_dir',
        'verification_code',
        'token',
        'login_code',
        'login_code_valid_until',
        'signup_ip_address',
        'logins',
        'last_login',
        'last_login_ip_address',
        'expires',
        'lead_source',
        'lead_source_module',
        'lead_status',
        'lead_type',
        'created_by',
        'updated_by',
        'created_at',
        'salutation',
        'first_name',
        'last_name',
        'job_title',
        'phone',
        'mobile',
        'website',
        'fax',
        'postal_code',
        'zoom',
        'lat',
        'lng',
        'primary_account',
        'email_verified_at',
        'hourly_rate',
        'notes',
        'additional_fields',
        'settings',
        'tags',
        'attributes',
        'meta',
        'minus_sign',
        'account_active',
        'bank',
        'bank_id',
        'currency_code',
        'ecode_swift',
        'first_day_of_week',
        'iban',
        'id_number',
        'media',
        'shipping_city',
        'shipping_country_code',
        'shipping_lat',
        'shipping_lng',
        'shipping_postal_code',
        'shipping_state',
        'shipping_street1',
        'shipping_street2',
        'shipping_zoom',
        'vat_number',
        'mobile_business',
        'mobile_personal',
        'phone_business',
        'phone_personal',
        'social'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'social' => 'json',
        'settings' => 'json',
        'tags' => 'json',
        'attributes' => 'json',
        'meta' => 'json',
    ];

    /**
     * The date attributes.
     *
     * @var array
     */
    protected $dates = ['last_login'];

    public function registerMediaCollections() : void
    {
        $this
            ->addMediaCollection('avatar')
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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function boot()
    {
        parent::boot();

        if (auth()->check()) {
            static::addGlobalScope(new AccountScope(auth()->user()));
        }

        // On create
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->account_id = auth()->user()->account_id;
                $model->created_by = auth()->id();
            }
        });

        // On update
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->user()->id;
            }
        });
    }

    /**
     * Form for creating a new record, per role
     * Note: if a column is required, make sure to add 'required' => true
     *
     * @return array
     */
    public static function getCreateForm()
    {
        $card_statuses = collect(json_decode(file_get_contents(storage_path('json/customers/card_status.json')), true));

        $owner = [
            'tab1' => [
                'text' => __('General'),
                'subs' => [
                    'sub1' => [
                        'items' => [
                            [
                                'type' => 'relation',
                                'relation' => [
                                    'type' => 'belongsTo',
                                    'with' => 'campaign',
                                    'pk' => 'id',
                                    'val' => 'name',
                                    'orderBy' => 'name',
                                    'order' => 'asc'
                                ],
                                'column' => 'campaign_id',
                                'text' => __('Website'),
                                'validate' => 'required',
                                'required' => true
                            ],
                            [
                                'type' => 'text',
                                'column' => 'name',
                                'text' => __('Name'),
                                'validate' => 'required|max:32',
                                'required' => true
                            ],
                            [
                                'type' => 'email',
                                'column' => 'email',
                                'text' => __('E-mail address'),
                                'validate' =>
                                'required|email|max:64',
                                'required' => true
                            ],
                            [
                                'type' => 'hidden',
                                'column' => 'country_code',
                                'validate' => 'required',
                                'required' => true
                            ],
                            [
                                'type' => 'hidden',
                                'column' => 'country_isd_code',
                                'validate' => 'required',
                                'required' => true
                            ],
                            [
                                'type' => 'phone',
                                'column' => 'customer_number',
                                'text' => __('Phone number'),
                                'validate' => 'required',
                                'required' => true
                            ],
                            [
                                'type' => 'date_picker',
                                'column' => 'date_of_birth',
                                'text' => __('Date of Birth'),
                                'validate' => 'nullable',
                            ],
                            [
                                'type' => 'select',
                                'column' => 'gender',
                                'text' => __('Gender'),
                                'validate' => 'nullable',
                                'select_item' => [
                                    [
                                        'text' => 'Male',
                                        'value' => 'M'
                                    ],
                                    [
                                        'text' => 'Female',
                                        'value' => 'F'
                                    ],
                                ],
                            ],
                            [
                                'type' => 'text',
                                'column' => 'street_name',
                                'text' => __('Street Name'),
                                'validate' => 'nullable',
                            ],
                            [
                                'type' => 'text',
                                'column' => 'city',
                                'text' => __('City'),
                                'validate' => 'nullable',
                            ],
                            [
                                'type' => 'text',
                                'column' => 'country',
                                'text' => __('Country'),
                                'validate' => 'nullable',
                            ],
                            [
                                'type' => 'text',
                                'column' => 'card_number',
                                'text' => __('Membership Card number'),
                                'validate' => 'required|numeric|min:10,max:16',
                                'vvalidate' => 'required|numeric|min_value:10,max_value:16',
                                'required' => true
                            ],
                            [
                                'type' => 'select',
                                'column' => 'card_status',
                                'text' => __('Card Status'),
                                'validate' => 'required',
                                'select_item' => $card_statuses->toArray(),
                            ],
                            [
                                'type' => 'select',
                                'column' => 'role',
                                'text' => __('Customer Type'),
                                'validate' => 'required',
                                'select_item' => [
                                    ['text' => 'Wholesale', 'value' => 1],
                                    ['text' => 'Retail', 'value' => 2],
                                    ['text' => 'Premium', 'value' => 3],
                                    ['text' => 'VIP', 'value' => 4],
                                ],
                            ],
                            [
                                'type' => 'password',
                                'column' => 'password',
                                'text' => __('Password'),
                                'hint_edit' => __('Leave blank to keep current password'),
                                'validate' => 'nullable|min:8|max:32',
                                'validate_create' => 'required|min:8|max:32',
                                'required_create' => true
                            ],
                            [
                                'type' => 'image',
                                'image' => [
                                    'thumb_width' => '140px',
                                    'thumb_height' => '140px'],
                                    'column' => 'avatar',
                                    'text' => __('Avatar'),
                                    'validate' => 'nullable|image|max:1000',
                                    'vvalidate' => 'image|size:1000',
                                    'hint' => __('Maximum file size is 1 Mb'),
                                    'icon' => 'account_circle',
                                    'class' => 'img-rounded my-3',
                            ],
                            [
                                'type' => 'boolean',
                                'default' => true,
                                'column' => 'active',
                                'text' => __('Active'),
                                'validate' => 'nullable'
                            ]
                        ]
                    ]
                ]
            ]
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
                                'type' => 'relation',
                                'relation' => [
                                    'type' => 'belongsTo',
                                    'with' => 'campaign',
                                    'pk' => 'id',
                                    'val' => 'name',
                                    'orderBy' => 'name',
                                    'order' => 'asc'
                                ],
                                'column' => 'campaign_id',
                                'text' => __('Website'),
                                'validate' => 'required',
                                'required' => true
                            ],
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
     * Name used in plan limitations (optional)
     *
     * @return string
     */
    public static function getLimitationName()
    {
        return 'customers';
    }

    /**
     * Export class name if table can be exported (optional)
     *
     * @return string
     */
    public static function getExportClass()
    {
        $owner = null;

        $reseller = null;

        $user = 'Platform\Exports\CustomersExport';

        return [
            1 => $owner,
            2 => $reseller,
            3 => $user
        ];
    }

    /**
     * Columns used for filters
     *
     * @return array
     */
    public static function getTableFilters()
    {
        $owner = [
            [
                'column' => 'campaign_id',
                'text' => __('All campaigns'),
                'icon' => 'filter_list',
                'type' => 'relation',
                'default' => null,
                'relation' => [
                    'type' => 'hasOne',
                    'permission' => 'personal',
                    'with' => 'campaign',
                    'table' => 'campaigns',
                    'pk' => 'id',
                    'val' => 'name',
                    'orderBy' => 'name',
                    'order' => 'asc'
                ]
            ]
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
        $owner = [
            'uuid',
            'campaign_id',
            'customer_number',
            'card_number'
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
     * Extra columns used in select queries, hidden from json response
     *
     * @return array
     */
    public static function getExtraQueryColumns()
    {
        $owner = [
            'id',
            'created_by'
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
            'import' => true,
            'detail' => [
                'show' => true,
                'type' => 'table',
                'title' => "Customer points"
            ],
            'actions_width' => '120px'
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
            'items' => __('Customers'),
            'edit_item' => __('Edit customer'),
            'create_item' => __('Create customer'),
            'import' => __('Import customer')
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
        $owner = [
            'view' => 'all',
            'delete' => 'all',
            'update' => 'all',
            'create' => 'all',
            'import' => 'all'
        ];

        $reseller = [
            'view' => 'account',
            'delete' => 'account',
            'update' => 'account',
            'create' => 'account',
            'import' => 'all'
        ];

        $user = [
            'view' => 'personal',
            'delete' => 'personal',
            'update' => 'personal',
            'create' => 'account',
            'import' => 'all'
        ];

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
            [
                'visible' => true,
                'value' => 'avatar',
                'exclude_from_select' => true,
                'width' => '60px',
                'text' => __('Avatar'),
                'align' => 'left',
                'sortable' => false
            ],
            [
                'visible' => true,
                'value' => 'name',
                'text' => __('Name'),
                'align' => 'left',
                'sortable' => true
            ],
            [
                'visible' => true,
                'value' => 'email',
                'text' => __('E-mail'),
                'align' => 'left',
                'sortable' => true
            ],
            [
                'visible' => false,
                'value' => 'role'
            ],
            [
                'visible' => true,
                'value' => 'customer_type',
                'exclude_from_select' => true,
                'text' => __('Customer Type'),
                'align' => 'center',
                'sortable' => true,
            ],
            /*[
                'visible' => true,
                'value' => 'locale',
                'type' => 'locale',
                'text' => __('Locale'),
                'align' => 'left',
                'sortable' => true
            ],*/
            [
                'visible' => true,
                'value' => 'points',
                'exclude_from_select' => true,
                'type' => 'number',
                'text' => __('Available Points'),
                'align' => 'center',
                'sortable' => false
            ],
            [
                'visible' => false,
                'value' => 'country_code',
                'text' => __('Country code'),
                'align' => 'left',
                'sortable' => false,
                'type' => 'country_code',
            ],
            [
                'visible' => false,
                'value' => 'country_isd_code',
                'text' => __('Country Isd code'),
                'align' => 'left',
                'sortable' => false,
                'type' => 'country_isd_code',
            ],
            [
                'visible' => true,
                'exclude_from_select' => true,
                'value' => 'number',
                'text' => __('Customer number'),
                'align' => 'left',
                'sortable' => false,
                'type' => 'text',
            ],
            [
                'visible' => false,
                'value' => 'card_status',
                'text' => __('Card Status'),
                'align' => 'left',
                'sortable' => false,
                'type' => 'card_status',
            ],
            [
                'visible' => true,
                'exclude_from_select' => true,
                'value' => 'card',
                'text' => __('Membership card number'),
                'align' => 'left',
                'sortable' => false,
                'type' => 'text',
            ],
            [
                'visible' => true,
                'value' => 'campaign_text',
                'exclude_from_select' => true,
                'relation' => [
                    'type' => 'hasOne',
                    'permission' => 'personal',
                    'with' => 'campaign',
                    'table' => 'campaigns',
                    'val' => 'name'
                ],
                'text' => __('Website'),
                'align' => 'left',
                'sortable' => false
            ],
            [
                'visible' => true,
                'value' => 'updated_at',
                'type' => 'date_time',
                'format' => 'ago',
                'text' => __('Last activity'),
                'align' => 'left',
                'sortable' => true,
                'default_order' => true
            ],
            [
                'visible' => true,
                'value' => 'active',
                'text' => __('Active'),
                'align' => 'center',
                'sortable' => true,
                'type' => 'boolean'
            ]
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
        $owner = [
            'name',
            'email',
            'customer_number'
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
     * Available actions for data table row, per role
     *
     * @return array
     */
    public static function getActions() {

        $owner = [
            [
                'text' => __('Edit'),
                'action' => 'edit',
                'icon' => 'edit',
                'color' => 'secondary',
                'dark' => false
            ],
            [
                'text' => __('Delete'),
                'action' => 'delete',
                'icon' => 'delete',
                'color' => 'secondary',
                'dark' => true
            ],
            [
                'text' => __('Customer Points'),
                'action' => 'detail',
                'icon' => 'info',
                'color' => 'secondary',
                'dark' => true
            ],
        ];

        $reseller = [
            [
                'text' => __('Edit'),
                'action' => 'edit',
                'icon' => 'edit',
                'color' => 'secondary',
                'dark' => false
            ]
        ];

        $reseller = $owner;

        $user = $owner;

        return [
            1 => $owner,
            2 => $reseller,
            3 => $user
        ];
    }

    public function getCustomerTypeAttribute()
    {
        $types = array(
            1 => 'Wholesale',
            2 => 'Retail',
            3 => 'Premium',
            4 => 'VIP',
        );

        return $types[$this->attributes['role']];
    }

    /**
     * Get campaign points.
     *
     * @return integer
     */
    public function getPointsAttribute()
    {
        $history = $this->history()
            ->where('points_expired_date','>', now())
            ->where('points', '>', 0)
            ->get(DB::raw('points - points_usage as points_remaining'));

        return $history->sum('points_remaining');
    }

    /**
     * Get campaign name
     *
     * @return string
     */
    public function getCampaignTextAttribute()
    {
        return ($this->campaign != null) ? $this->campaign->name : null;
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
     * Format customer number.
     *
     * @return string
     */
    public function getNumberAttribute()
    {
        if ($this->customer_number) {
            $splitCustomerNumber = str_split($this->customer_number, 6);

            $splitCustomerNumber_0 = str_split($splitCustomerNumber[0], 3);

            $mergeCustomerNumber = array_merge($splitCustomerNumber_0, [$splitCustomerNumber[1]]);

            return implode('-', $mergeCustomerNumber);
        }

        return NULL;
    }

    /**
     * Format customer card number.
     *
     * @return string
     */
    public function getCardAttribute()
    {
        if ($this->card_number && $this->card_status === 1) {
            return implode('-', str_split($this->card_number, 4));
        }

        return NULL;
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
            // return (string) \Avatar::create(strtoupper($this->name))->toBase64();

            $config = config('laravolt.avatar');

            return (string) (new Avatar($config))->create(strtoupper($this->name))->toBase64();
        }
    }

    /**
     * Get the customer's history
     */
    public function getHistory($locale = null)
    {
        $history = $this->history;

        if ($locale !== null) {
            app()->setLocale($locale);
        }

        $history = $history->map(function ($record) {
            $record->created_at = $record->created_at->timezone($this->getTimezone());
            $record->description = __($record->description);
            $record->expiry = (
                $record->points > 0 && $record->points_expired_date > now() ?
                'Expires in '. Carbon::now()->longAbsoluteDiffForHumans($record->points_expired_date) :
                ''
            );

            return collect($record)->only(
                'color',
                'created_at',
                'description',
                'icon',
                'icon_size',
                'points',
                'expiry',
                'reward_title',
                'event',
                'date_received'
            );
        });

        return $history;
    }

    /**
     * Get the customer's history with campaign
     */
    public function getHistoryWithCampaign($locale = null)
    {
        $history = $this->history;

        if ($locale !== null) {
            app()->setLocale($locale);
        }

        $history = $history->map(function ($record) {
            $record->created_at = $record->created_at->timezone($this->getTimezone());
            $record->campaign_name = $record->campaign->name;
            $record->description = __($record->description);
            $record->expiry = (
                $record->points > 0 && $record->points_expired_date > now()?
                'Expires in '. Carbon::now()->longAbsoluteDiffForHumans($record->points_expired_date) :
                'Expired ' . Carbon::parse($record->points_expired_date)->diffForHumans()
            );

            return collect($record)->only(
                'color',
                'created_at',
                'description',
                'icon',
                'icon_size',
                'points',
                'expiry',
                'reward_title',
                'event',
                'date_received',
                'campaign_name',
                'campaign_id',
                'id'
            );
        });

        return $history;
    }

    /**
     * Money formatting
     */
    public function formatMoney($amount, $currency = 'TTD', $formatHtml = false)
    {
        if ($currency == null) $currency = 'TTD';

        $value = Money::{$currency}($amount);

        $currencies = new \Money\Currencies\ISOCurrencies();

        $numberFormatter = new \NumberFormatter($this->getLanguage(), \NumberFormatter::CURRENCY);
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
                case 'date_medium': $date = $date->timezone($this->getTimezone())->format($this->getUserDateFormat()); break;
                case 'datetime_medium': $date = $date->timezone($this->getTimezone())->format($this->getUserDateFormat() . ' ' . $this->getUserTimeFormat()); break;
                case 'friendly': $date = $date->timezone($this->getTimezone())->diffForHumans(); break;
            }

            return $date;
        }

        return null;
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
        }

        return $this->language;
    }

    /**
     * User locale
     */
    public function getLocale()
    {
        if ($this->locale === NULL) {
            $language = $this->getLanguage();

            // If there is no default for user's language, use global default
            return config('system.language_defaults.' . $language . '.locale', config('system.default_locale'));
        }

        return $this->locale;
    }

    /**
     * User timezone
     */
    public function getTimezone()
    {
        if ($this->timezone === NULL) {
            $language = $this->getLanguage();

            // If there is no default for user's language, use global default
            return config('system.language_defaults.' . $language . '.timezone', config('system.default_timezone'));
        }

        return $this->timezone;
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
        }

        return $this->currency_code;
    }

    /**
     * Get what data is shown inside the detail dialog
     */

    public function getDetailData()
    {
        $headers = [
        //    [
        //        'text' => "Event",
        //        'value' => "event",
        //        'sortable' => false
        //    ],
            [
                'text' => "Points",
                'value' => "points",
                'sortable' => false
            ],
            [
                'text' => "Description",
                'value' => "description",
                'sortable' => false
            ],
            [
                'text' => "Date Received",
                'value' => "date_received",
                'sortable' => false
            ]
        ];

        $data = $this->getHistory();

        $noDataText = "No points for this customer yet";

        return [
            'headers' => $headers,
            'items' => $data,
            'noDataText' => $noDataText
        ];
    }

    /**
     * Relationships
     * -------------
     */

    public function account()
    {
        return $this->belongsTo(\App\User::class, 'account_id', 'id');
    }

    public function campaign()
    {
        return $this->hasOne(\Platform\Models\Campaign::class, 'id', 'campaign_id');
    }

    public function history()
    {
        return $this->hasMany(\Platform\Models\History::class, 'customer_id', 'id')->orderBy('created_at', 'asc');
    }

    public function credit_request()
    {
        return $this->hasMany(\App\User::class, 'industry_id', 'id');
    }
}
