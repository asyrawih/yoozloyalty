<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Str;
use App\Scopes\AccountScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\StaffFactory;
use Illuminate\Support\Facades\DB;
use Laravolt\Avatar\Avatar;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

class Staff extends Authenticatable implements JWTSubject, HasMedia
{

    use GeneratesUuid, Notifiable, InteractsWithMedia, HasFactory;

    protected $table = 'staff';

    /**
    * The attributes that are mass assignable.
    * @var array
    */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        'businesses_text',
        'staff_id',
        'staff_role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
        'account',
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
        'date_of_birth',
        'phone',
        'mobile',
        'website',
        'fax',
        'street1',
        'street2',
        'city',
        'state',
        'postal_code',
        'country_code',
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
        'id',
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
        'gender',
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
        'uuid' => EfficientUuid::class,
        'email_verified_at' => 'datetime',
        'social' => 'json',
        'settings' => 'json',
        'tags' => 'json',
        'attributes' => 'json',
        'meta' => 'json'
    ];

    /**
    * Export class name if table can be exported (optional)
    *
    * @return string
    */
    public static function getExportClass()
    {
        $owner = null;
        $reseller = null;
        $user = 'Platform\Exports\StaffsExport';

        return [
            1 => $owner,
            2 => $reseller,
            3 => $user
        ];
    }

    /**
     * Create a new factory instance for the Staff model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return StaffFactory::new();
    }

    public function registerMediaCollections() : void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    public function registerMediaConversions(Media $media = null) : void
    {
        $this->addMediaConversion('avatar')
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

        static::retrieved(function ($model) {});

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->user()->id;
            }
        });

        // On create
        self::creating(function ($model) {
            if (auth()->check()) {
                $model->account_id = auth()->user()->account_id;
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
        $roles = collect(json_decode(file_get_contents(storage_path('json/staff/roles.json')), true));

        $owner = [
            'tab1' => [
                'text' => __('General'),
                'subs' => [
                    'sub1' => [
                      'items' => [
                            ['type' => 'relation', 'relation' => ['type' => 'belongsToMany', 'permission' => 'personal', 'with' => 'businesses', 'remote_pk' => 'business_id', 'table' => 'businesses', 'pk' => 'id', 'val' => 'name', 'orderBy' => 'name', 'order' => 'asc'], 'text' => __('Store Name'), 'validate' => 'required', 'required' => true],
                            ['type' => 'text', 'column' => 'name', 'text' => __('Name'), 'validate' => 'required|max:32', 'required' => true],
                            ['type' => 'email', 'column' => 'email', 'text' => __('E-mail address'), 'validate' => 'required|email|max:64', 'required' => true],
                            ['type' => 'password', 'column' => 'password', 'text' => __('Password'), 'hint_edit' => __('Leave blank to keep current password'), 'validate' => 'nullable|min:8|max:32', 'validate_create' => 'required|min:8|max:32', 'required_create' => true],
                            ['type' => 'image', 'image' => ['thumb_width' => '140px', 'thumb_height' => '140px'], 'column' => 'avatar', 'text' => __('Avatar'), 'validate' => 'nullable|image|max:1000', 'vvalidate' => 'image|size:1000', 'hint' => __('Maximum file size is 1 Mb'), 'icon' => 'account_circle', 'class' => 'img-rounded my-3'],
                            [
                                'type' => 'select',
                                'column' => 'role',
                                'text' => __('Role'),
                                'validate' => 'required',
                                'select_item' => $roles->map(function ($role) {
                                    return [
                                        'text' => Str::title($role['name']),
                                        'value' => (int) $role['id']
                                    ];
                                })->toArray(),
                            ],
                            ['type' => 'boolean', 'default' => true, 'column' => 'active', 'text' => __('Active'), 'validate' => 'nullable'],
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
     * Name used in plan limitations (optional)
     *
     * @return string
     */
    public static function getLimitationName()
    {
        return 'staff';
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
        $owner = ['select_all' => true, 'actions' => true, 'create' => true];
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
            'items' => __('Staff members'),
            'edit_item' => __('Edit staff member'),
            'create_item' => __('Create staff member'),
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
        $reseller = ['view' => 'account', 'delete' => 'account', 'update' => 'account', 'create' => false];
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
            ['visible' => true, 'value' => 'avatar', 'exclude_from_select' => true, 'width' => '60px', 'text' => __('Avatar'), 'align' => 'left', 'sortable' => false],
            ['visible' => true, 'value' => 'name', 'text' => __('Name'), 'align' => 'left', 'sortable' => true],
            ['visible' => false, 'value' => 'role'],
            ['visible' => true, 'value' => 'staff_id', 'exclude_from_select' => true, 'width' => '60px', 'text' => __('Staff ID'), 'align' => 'center', 'sortable' => true],
            ['visible' => true, 'value' => 'staff_role', 'exclude_from_select' => true, 'text' => __('Role'), 'align' => 'center', 'sortable' => true, 'type' => 'role'],
            ['visible' => true, 'value' => 'email', 'text' => __('E-mail'), 'align' => 'left', 'sortable' => true],
            ['visible' => true, 'value' => 'businesses_text', 'exclude_from_select' => true, 'relation' => ['type' => 'belongsToMany', 'with' => 'businesses', 'table' => 'businesses', 'val' => 'name'], 'text' => __('Stores'), 'align' => 'left', 'sortable' => false],
            ['visible' => true, 'value' => 'active', 'text' => __('Active'), 'align' => 'center', 'sortable' => true, 'type' => 'boolean'],
            ['visible' => true, 'value' => 'updated_at', 'type' => 'date_time', 'format' => 'ago', 'text' => __('Last activity'), 'align' => 'left', 'sortable' => true, 'default_order' => true]
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
        $owner = [
            ['text' => __('Edit'), 'action' => 'edit', 'icon' => 'edit', 'color' => 'secondary', 'dark' => false],
            ['text' => __('Delete'), 'action' => 'delete', 'icon' => 'delete', 'color' => 'secondary', 'dark' => true]
        ];

        $reseller = [
            ['text' => __('Edit'), 'action' => 'edit', 'icon' => 'edit', 'color' => 'secondary', 'dark' => false]
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
     * Get avatar.
     *
     * @return string for use in <img> src
     */
    public function getAvatarAttribute()
    {
        if ($this->getFirstMediaUrl('avatar') !== '') {
            return $this->getFirstMediaUrl('avatar', 'avatar');
        } else {
            $config = config('laravolt.avatar');

            return (string) (new Avatar($config))->create(strtoupper($this->name))->toBase64();
        }
    }

    /**
    * Get the staff member's history
    */
    public function getHistory($campaign_id, $limit = 20)
    {
        $this->setRelation('history', $this->history->where('campaign_id', $campaign_id)->take($limit));

        $history = $this->history;

        $history = $history->map(function ($record) {
            $record->created_at = $record->created_at->timezone($this->getTimezone());

            $segments = $record->segments->map(function ($record) {
                return collect($record)->only('name');
            });

            $record->segment_details = $segments;

            $record->customer_details = collect($record->customer)->only('avatar', 'name', 'number', 'points');

            return collect($record)->only('color', 'created_at', 'description', 'icon', 'points', 'reward_title', 'segment_details', 'customer_details', 'staff_name');
        });

        return $history;
    }

    /**
     * Money formatting
     */
    public function formatMoney($amount, $currency = 'USD', $formatHtml = false)
    {

        if ($currency == null) $currency = 'USD';

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

        if ($date) {
            switch ($format) {
                case 'date_medium': $date = $date->timezone($this->getTimezone())->format($this->getUserDateFormat()); break;
                case 'datetime_medium': $date = $date->timezone($this->getTimezone())->format($this->getUserDateFormat() . ' ' . $this->getUserTimeFormat()); break;
                case 'friendly': $date = $date->timezone($this->getTimezone())->diffForHumans(); break;
            }

            return $date;

        } else {
            return NULL;
        }
    }

    /**
     * Check if user was online recently.
     *
     * @return boolean
     */
    public function getRecentlyOnline($minutes = 10)
    {
        $lastActivity = strtotime(\Carbon\Carbon::now()->subMinutes($minutes));

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
            return config('system.language_defaults.' . $language . '.locale', config('system.default_locale'));
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

            return config('system.language_defaults.' . $language . '.timezone', config('system.default_timezone'));
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
     * Get comma separated business names
     *
     * @return string
     */
    public function getBusinessesTextAttribute()
    {
        $businesses = '';
        foreach ($this->businesses as $i => $business) {
            if (isset($business->name)) {
                $businesses .= $business->name;
                if ($i < count($this->businesses) - 1) $businesses .= ', ';
            }
        }

        return $businesses;
    }

    public function getStaffIdAttribute()
    {
        return $this->id;
    }

    public function getStaffRoleAttribute()
    {
        $roles = collect(json_decode(file_get_contents(storage_path('json/staff/roles.json')), true));

        return $roles->firstWhere('id', $this->attributes['role']);
    }

    public function account()
    {
        return $this->belongsTo(\App\User::class, 'account_id', 'id');
    }

    public function businesses()
    {
        return $this->belongsToMany(\Platform\Models\Business::class, 'business_staff', 'staff_id', 'business_id');
    }

    public function customer()
    {
        return $this->belongsTo(\App\Customer::class, 'customer_id', 'id');
    }

    public function history()
    {
        return $this->hasMany(\Platform\Models\History::class, 'staff_id', 'id')->orderBy('created_at', 'desc');
    }

}
