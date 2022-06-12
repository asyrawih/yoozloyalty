<?php

namespace Platform\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Dyrynda\Database\Casts\EfficientUuid;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Industry extends Model implements HasMedia
{
    use GeneratesUuid, InteractsWithMedia;

    protected $table = 'industries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    /**
     * Appended columns.
     *
     * @var array
     */
    protected $appends = [
        'logo',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'media',
    ];

    /**
     * Field mutators.
     *
     * @var array
     */
    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];

    public function registerMediaCollections() : void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    /**
     * Form for creating a new record, per role
     *
     * @return array
     */
    public static function getCreateForm() {
        $owner = [
            'tab1' => [
                'subs' => [
                    'sub1' => [
                        'items' => [
                            [
                                'type' => 'text',
                                'column' => 'name',
                                'text' => __('Name'),
                                'validate' => 'required|max:32',
                                'required' => true,
                                'icon' => 'business'
                            ],
                            [
                                'type' => 'image',
                                'image' => [
                                    'thumb_max_width' => '64px',
                                    'thumb_max_height' => '64px'
                                ],
                                'column' => 'logo',
                                'text' => __('Image'),
                                'validate' => 'nullable|image|max:1000',
                                'vvalidate' => 'image|size:1000',
                                'hint' => __('Maximum file size is 1 Mb'),
                                'icon' => 'attach_file'
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
        $owner = ['id'];
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
            'items' => __('Industries'),
            'edit_item' => __('Edit industry'),
            'create_item' => __('Create industry'),
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
            'create' => true
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
     * The headers for the data table, per role
     *
     * @return array
     */
    public static function getHeaders()
    {
        $owner = [
            [
                'visible' => true,
                'value' => 'logo',
                'type' => 'image',
                'exclude_from_select' => true,
                'max_width' => '60px',
                'text' => __('Image'),
                'align' => 'left',
                'sortable' => false
            ],
            [
                'visible' => true,
                'value' => 'name',
                'text' => __('Industry'),
                'align' => 'left',
                'sortable' => true
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
            [
                'text' => __('Edit'),
                'action' => 'edit',
                'icon' => 'edit',
                'color' => 'secondary',
                'dark' => false
            ],
            /*['divider'],*/
            [
                'text' => __('Delete'),
                'action' => 'delete',
                'icon' => 'delete',
                'color' => 'secondary',
                'dark' => true
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
     * Get JSON translation if exists (resources/lang/[language].json
     *
     * @return string
     */
    public function getNameTranslatedAttribute()
    {
        return __($this->name);
    }

    public function getLogoAttribute()
    {
        return ($this->getFirstMediaUrl('logo') !== '') ? $this->getFirstMediaUrl('logo') : null;
    }

    /**
     * Relationships
     * -------------
     */

    public function business()
    {
        return $this->hasMany(\Platform\Models\Business::class, 'industry_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(\App\User::class, 'industry_id', 'id');
    }
}
