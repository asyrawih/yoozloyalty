<?php

namespace Platform\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use App\Scopes\AccountScope;
use App\User;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;

class Reward extends Model implements HasMedia
{
    use GeneratesUuid;
    use InteractsWithMedia;

    protected $table = 'rewards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id'
    ];

    /**
     * Appended columns.
     *
     * @var array
     */
    protected $appends = [
        'title_with_points',
        'main_image',
        'main_image_thumb',
        'image1',
        'image2',
        'image3',
        'image4',
        'image1_thumb',
        'image2_thumb',
        'image3_thumb',
        'image4_thumb'
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
        'settings' => 'json',
        'tags' => 'json',
        'attributes' => 'json',
        'meta' => 'json',
        'customer_types' => 'array',
    ];

    public function registerMediaCollections() : void
    {

        $this
            ->addMediaCollection('main_image')
            ->singleFile();

        $this
            ->addMediaCollection('image1')
            ->singleFile();

        $this
            ->addMediaCollection('image2')
            ->singleFile();

        $this
            ->addMediaCollection('image3')
            ->singleFile();

        $this
            ->addMediaCollection('image4')
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null) : void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(360)
            ->height(240)
            ->performOnCollections('main_image')
            ->nonQueued();

        $this
            ->addMediaConversion('square')
            ->crop(Manipulations::CROP_CENTER, 256, 256)
            ->performOnCollections('image1', 'image2', 'image3', 'image4')
            ->nonQueued();
    }

    /**
     * Date/time fields that can be used with Carbon.
     *
     * @return array
     */
    public function getDates()
    {
        return [
            'active_from',
            'expires_at',
            'created_at',
            'updated_at'
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new AccountScope(auth()->user()));

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
    }

    /**
     * Form for creating a new record, per role
     *
     * @return array
     */
    public static function getCreateForm()
    {
        // $user = auth()->user();
        $user = User::query()->find(auth()->id());

        $owner = [
            'tab1' => [
                'text' => __('General'),
                'subs' => [
                    'sub1' => [
                        'items' => [
                            [
                                'type' => 'text',
                                'column' => 'title',
                                'text' => __('Offer Name'),
                                'validate' => 'required|max:64',
                                'required' => true
                            ],
                            [
                                'type' => 'number',
                                'column' => 'points_cost',
                                'text' => __('Points to Redeem'),
                                'validate' => 'required|integer|min:1|max:1000000',
                                'vvalidate' => 'required|numeric|min_value:1|max_value:1000000',
                                'required' => true
                            ],
                            [
                                'type' => 'currency',
                                'prefix' => $user->getCurrencyFormat('symbol'),
                                'suffix' => $user->getCurrency(),
                                'column' => 'reward_value',
                                'text' => __('Reward value'),
                                'validate' => "required|decimal:{$user->getCurrencyFormat('fraction_digits')}|min:0|max:1000000",
                                'required' => true
                            ],
                            [
                                'type' => 'multi_select',
                                'column' => 'customer_types',
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
                                'type' => 'boolean',
                                'default' => false,
                                'column' => 'requires_validation',
                                'text' => __('Requires Validation'),
                                'validate' => 'nullable'
                            ],
                            [
                                'type' => 'boolean',
                                'default' => true,
                                'column' => 'active',
                                'text' => __('Active'),
                                'validate' => 'nullable'
                            ],
                            [
                                'type' => 'boolean',
                                'default' => false,
                                'column' => 'delivery_by_coupun',
                                'text' => __('Delivery by coupon'),
                                'validate' => 'nullable'
                            ],
                            [
                                'type' => 'select',
                                'default' => 1,
                                'column' => 'multiple_time',
                                'text' => __('Offer Usage'),
                                'select_item' => [
                                    [
                                        'text' => 'One Time',
                                        'value' => 0,
                                    ],
                                    [
                                        'text' => 'Multiple Time',
                                        'value' => 1,
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            ],
            'tab2' => [
                'text' => __('Date range'),
                'subs' => [
                    'sub1' => [
                        'items' => [
                            [
                                'type' => 'static_paragraph',
                                'column' => 'static_paragraph',
                                'text' => __('Reward offer will follow store timezone after being attached when creating / editting websites.')
                            ],
                            [
                                'type' => 'date_time',
                                'default' => Carbon::now()->format('Y-m-d H:00:00'),
                                'format' => 'LLLL',
                                'column' => 'active_from',
                                'text' => __('Active from'),
                                'validate' => 'required',
                                'required' => true,
                                'icon' => 'calendar_today'
                            ],
                            [
                                'type' => 'date_time',
                                'default' => Carbon::now()->addMonths(18)->format('Y-m-d H:00:00'),
                                'format' => 'LLLL',
                                'column' => 'expires_at',
                                'text' => __('Expires'),
                                'validate' => 'required',
                                'required' => true,
                                'icon' => 'calendar_today'
                            ],
                            [
                                'type' => 'label_only',
                                'column' => 'label_only',
                                'text' => __('Active Happy Hour')
                            ],
                            [
                                'type' => 'boolean_only',
                                'default' => false,
                                'column' => 'active_monday',
                                'text' => __('Monday'),
                                'validate' => 'nullable',
                                'style' => 'width: 30%; display: inline-block;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_monday_from',
                                'text' => __('From Time'),
                                'default' => '00:00',
                                'validate' => 'required_if:active_monday,1',
                                'placeholder' => 'From (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_monday_to',
                                'text' => __('To Time'),
                                'default' => '23:59',
                                'validate' => 'required_if:active_monday,1',
                                'placeholder' => 'To (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ],
                            [
                                'type' => 'boolean_only',
                                'default' => false,
                                'column' => 'active_tuesday',
                                'text' => __('Tuesday'),
                                'validate' => 'nullable',
                                'style' => 'width: 30%; display: inline-block;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_tuesday_from',
                                'text' => __('From Time'),
                                'default' => '00:00',
                                'validate' => 'required_if:active_tuesday,1',
                                'placeholder' => 'From (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_tuesday_to',
                                'text' => __('To Time'),
                                'default' => '23:59',
                                'validate' => 'required_if:active_tuesday,1',
                                'placeholder' => 'To (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ],
                            [
                                'type' => 'boolean_only',
                                'default' => false,
                                'column' => 'active_wednesday',
                                'text' => __('Wednesday'),
                                'validate' => 'nullable',
                                'style' => 'width: 30%; display: inline-block;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_wednesday_from',
                                'text' => __('From Time'),
                                'default' => '00:00',
                                'validate' => 'required_if:active_wednesday,1',
                                'placeholder' => 'From (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_wednesday_to',
                                'text' => __('To Time'),
                                'default' => '23:59',
                                'validate' => 'required_if:active_wednesday,1',
                                'placeholder' => 'To (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ],
                            [
                                'type' => 'boolean_only',
                                'default' => false,
                                'column' => 'active_thursday',
                                'text' => __('Thursday'),
                                'validate' => 'nullable',
                                'style' => 'width: 30%; display: inline-block;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_thursday_from',
                                'text' => __('From Time'),
                                'default' => '00:00',
                                'validate' => 'required_if:active_thursday,1',
                                'placeholder' => 'From (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_thursday_to',
                                'text' => __('To Time'),
                                'default' => '23:59',
                                'validate' => 'required_if:active_thursday,1',
                                'placeholder' => 'To (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ],
                            [
                                'type' => 'boolean_only',
                                'default' => false,
                                'column' => 'active_friday',
                                'text' => __('Friday'),
                                'validate' => 'nullable',
                                'style' => 'width: 30%; display: inline-block;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_friday_from',
                                'text' => __('From Time'),
                                'default' => '00:00',
                                'validate' => 'required_if:active_friday,1',
                                'placeholder' => 'From (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_friday_to',
                                'text' => __('To Time'),
                                'default' => '23:59',
                                'validate' => 'required_if:active_friday,1',
                                'placeholder' => 'To (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ],
                            [
                                'type' => 'label_only',
                                'column' => 'label_only',
                                'text' => __('Active on weekend')
                            ],
                            [
                                'type' => 'boolean_only',
                                'default' => false,
                                'column' => 'active_saturday',
                                'text' => __('Saturday'),
                                'validate' => 'nullable',
                                'style' => 'width: 30%; display: inline-block;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_saturday_from',
                                'text' => __('From Time'),
                                'default' => '00:00',
                                'validate' => 'required_if:active_saturday,1',
                                'placeholder' => 'From (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_saturday_to',
                                'text' => __('To Time'),
                                'default' => '23:59',
                                'validate' => 'required_if:active_saturday,1',
                                'placeholder' => 'To (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ],
                            [
                                'type' => 'boolean_only',
                                'default' => false,
                                'column' => 'active_sunday',
                                'text' => __('Sunday'),
                                'validate' => 'nullable',
                                'style' => 'width: 30%; display: inline-block;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_sunday_from',
                                'text' => __('From Time'),
                                'default' => '00:00',
                                'validate' => 'required_if:active_sunday,1',
                                'placeholder' => 'From (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ],
                            [
                                'type' => 'time_picker',
                                'column' => 'active_sunday_to',
                                'text' => __('To Time'),
                                'default' => '23:59',
                                'validate' => 'required_if:active_sunday,1',
                                'placeholder' => 'To (HH:MM)',
                                'required' => false,
                                'style' => 'width:25%; display: inline-block; margin-left: 2em;'
                            ]
                        ]
                    ]
                ]
            ],
            'tab3' => [
                'text' => __('Description'),
                'subs' => [
                    'sub1' => [
                        'items' => [
                            [
                                'type' => 'wysiwyg',
                                'config' => [
                                    'toolbar' => ['bold', 'italic', '|', 'link']
                                ],
                                'column' => 'description',
                                'text' => __('Description'),
                                'validate' => 'nullable'
                            ],
                        ]
                    ]
                ]
            ],
            'tab4' => [
                'text' => __('Images'),
                'subs' => [
                    'sub1' => [
                        'items' => [
                            [
                                'type' => 'image',
                                'image' => [
                                    'thumb_max_width' => '180px',
                                    'thumb_max_height' => '120px'
                                ],
                                'column' => 'main_image',
                                'text' => __('Main image'),
                                'validate' => 'nullable|image|max:1000',
                                'vvalidate' => 'image|size:1000',
                                'hint' => __('Maximum file size is 1 Mb'),
                                'icon' => 'attach_file'
                            ],
                            [
                                'type' => 'image',
                                'image' => [
                                    'thumb_max_width' => '180px',
                                    'thumb_max_height' => '120px'
                                ],
                                'column' => 'image1',
                                'text' => __('Additional image') . ' 1',
                                'validate' => 'nullable|image|max:1000',
                                'vvalidate' => 'image|size:1000',
                                'hint' => __('Maximum file size is 1 Mb'),
                                'icon' => 'attach_file'
                            ],
                            [
                                'type' => 'image',
                                'image' => [
                                    'thumb_max_width' => '180px',
                                    'thumb_max_height' => '120px'
                                ],
                                'column' => 'image2',
                                'text' => __('Additional image') . ' 2',
                                'validate' => 'nullable|image|max:1000',
                                'vvalidate' => 'image|size:1000',
                                'hint' => __('Maximum file size is 1 Mb'),
                                'icon' => 'attach_file'
                            ],
                            [
                                'type' => 'image',
                                'image' => [
                                    'thumb_max_width' => '180px',
                                    'thumb_max_height' => '120px'
                                ],
                                'column' => 'image3',
                                'text' => __('Additional image') . ' 3',
                                'validate' => 'nullable|image|max:1000',
                                'vvalidate' => 'image|size:1000',
                                'hint' => __('Maximum file size is 1 Mb'),
                                'icon' => 'attach_file'
                            ],
                            [
                                'type' => 'image',
                                'image' => [
                                    'thumb_max_width' => '180px',
                                    'thumb_max_height' => '120px'
                                ],
                                'column' => 'image4',
                                'text' => __('Additional image') . ' 4',
                                'validate' => 'nullable|image|max:1000',
                                'vvalidate' => 'image|size:1000',
                                'hint' => __('Maximum file size is 1 Mb'),
                                'icon' => 'attach_file'
                            ],
                        ]
                    ]
                ]
            ],
            'tab5' => [
                'text' => __('Coupon'),
                'subs' => [
                    'sub1' => [
                        'text' => __('Coupon List'),
                        'items' => [
                            [
                                'type' => 'coupun_list',
                                'column' => 'coupun_list',
                            ],
                        ]
                    ],
                    'sub2' => [
                        'text' => __('Coupon History'),
                        'items' => [
                            [
                                'type' => 'coupun_history',
                                'column' => 'coupun_history',
                            ],
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
        return 'rewards';
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

        $user = 'App\Exports\Merchant\RewardExport';

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
                'column' => 'campaigns',
                'text' => __('All campaigns'),
                'icon' => 'filter_list',
                'type' => 'relation',
                'default' => null,
                'relation' => [
                    'type' => 'belongsToMany',
                    'permission' => 'personal',
                    'with' => 'campaigns',
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
            'import' => true,
            // 'dialog_width' => 640,
            // 'dialog_height' => 380
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
            'items' => __('Reward Offer'),
            'edit_item' => __('Edit Offer'),
            'create_item' => __('Create Offer'),
            'import' => __('Import reward'),
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
            'create' => true,
            'import' => 'all',
        ];

        $reseller = [
            'view' => 'account',
            'delete' => 'account',
            'update' => 'account',
            'create' => false,
            'import' => 'all'
        ];

        $user = [
            'view' => 'personal',
            'delete' => 'personal',
            'update' => 'personal',
            'create' => true,
            'import' => 'all',
        ];

        return [
            1 => $owner,
            2 => $reseller,
            3 => $user
        ];
    }

    /**
     * The headers for the data table, per role (value = column name)
     *
     * @return array
     */
    public static function getHeaders()
    {
        // $user = auth()->user();
        $user = User::query()->find(auth()->id());

        $owner = [
            [
                'visible' => true,
                'value' => 'title',
                'style' => ['min-width'=> '120px'],
                'text' => __('Offer Name'),
                'align' => 'left',
                'sortable' => true
            ],
            [
                'visible' => true,
                'type' => 'reward_value',
                'prefix' => $user->getCurrencyFormat('symbol'),
                'suffix' => $user->getCurrency(),
                'fraction_digits' => $user->getCurrencyFormat('fraction_digits'),
                'value' => 'reward_value',
                'text' => __('Reward Value'),
                'align' => 'left',
                'sortable' => true
            ],
            [
                'visible' => true,
                'value' => 'points_cost',
                'type' => 'number',
                'text' => __('Of Points'),
                'align' => 'center',
                'sortable' => true,
                'default_order' => true
            ],
            [
                'visible' => true,
                'value' => 'number_of_times_redeemed',
                'type' => 'number',
                'text' => __('Redemptions'),
                'align' => 'center',
                'sortable' => true
            ],
            [
                'visible' => true,
                'value' => 'active',
                'text' => __('Active'),
                'align' => 'center',
                'sortable' => true,
                'type' => 'boolean'
            ],
            [
                'visible' => true,
                'value' => 'main_image_thumb',
                'type' => 'image',
                'exclude_from_select' => true,
                'max_width' => '140px',
                'text' => __('Main image'),
                'align' => 'left',
                'sortable' => false
            ],
            [
                'visible' => true,
                'value' => 'requires_validation',
                'text' => __('Requires Validation'),
                'align' => 'center',
                'sortable' => false,
                'type' => 'boolean',
                'max_width' => '50px'
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
            'title',
            'points_cost'
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
    public static function getActions()
    {
        $owner = [
            ['text' => __('Edit'), 'action' => 'edit', 'icon' => 'edit', 'color' => 'secondary', 'dark' => false],
            /*['divider'],*/
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

    public function getTitleWithPointsAttribute()
    {
        return $this->title . ' (' . $this->points_cost . ')';
    }

    /**
     * Images
     * -------------
     */

    public function getMainImageAttribute()
    {
        return ($this->getFirstMediaUrl('main_image') !== '') ? $this->getFirstMediaUrl('main_image') : null;
    }

    public function getMainImageThumbAttribute()
    {
        return ($this->getFirstMediaUrl('main_image') !== '') ? $this->getMedia('main_image')[0]->getUrl('thumb') : null;
    }

    public function getImage1Attribute()
    {
        return ($this->getFirstMediaUrl('image1') !== '') ? $this->getFirstMediaUrl('image1') : null;
    }

    public function getImage1ThumbAttribute()
    {
        return ($this->getFirstMediaUrl('image1') !== '') ? $this->getMedia('image1')[0]->getUrl('square') : null;
    }

    public function getImage2Attribute()
    {
        return ($this->getFirstMediaUrl('image2') !== '') ? $this->getFirstMediaUrl('image2') : null;
    }

    public function getImage2ThumbAttribute()
    {
        return ($this->getFirstMediaUrl('image2') !== '') ? $this->getMedia('image2')[0]->getUrl('square') : null;
    }

    public function getImage3Attribute()
    {
        return ($this->getFirstMediaUrl('image3') !== '') ? $this->getFirstMediaUrl('image3') : null;
    }

    public function getImage3ThumbAttribute()
    {
        return ($this->getFirstMediaUrl('image3') !== '') ? $this->getMedia('image3')[0]->getUrl('square') : null;
    }

    public function getImage4Attribute()
    {
        return ($this->getFirstMediaUrl('image4') !== '') ? $this->getFirstMediaUrl('image4') : null;
    }

    public function getImage4ThumbAttribute()
    {
        return ($this->getFirstMediaUrl('image4') !== '') ? $this->getMedia('image4')[0]->getUrl('square') : null;
    }

    /**
     * Relationships
     * -------------
     */

    public function account()
    {
        return $this->belongsTo(\App\User::class, 'account_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'company_user', 'company_id', 'user_id');
    }

    public function campaigns()
    {
        return $this->belongsToMany(\Platform\Models\Campaign::class, 'campaign_reward', 'reward_id', 'campaign_id')
            ->withPivot(
                'active_from',
                'expires_at',
                'active_monday',
                'active_tuesday',
                'active_wednesday',
                'active_thursday',
                'active_friday',
                'active_saturday',
                'active_sunday',
                'active_monday_from',
                'active_tuesday_from',
                'active_wednesday_from',
                'active_thursday_from',
                'active_friday_from',
                'active_saturday_from',
                'active_sunday_from',
                'active_monday_to',
                'active_tuesday_to',
                'active_wednesday_to',
                'active_thursday_to',
                'active_friday_to',
                'active_saturday_to',
                'active_sunday_to'
            );
    }

    public function coupunCode()
    {
        return $this->hasMany('\Platform\Models\CoupunCode');
    }

    public function coupunUsed()
    {
        return $this->hasMany('\Platform\Models\CoupunUsed')->orderBy('created_at', 'desc');
    }

    public function coupunCodeisActive()
    {
        $coupun = $this->hasMany('\Platform\Models\CoupunCode')->where('status', 0)->count();

        return $coupun ? true : false;
    }
}
