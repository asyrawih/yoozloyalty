<?php

namespace Platform\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use App\Scopes\AccountScope;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\BusinessFactory;

class Business extends Model implements HasMedia
{

	use GeneratesUuid;
	use InteractsWithMedia;
	use HasFactory;

    protected $table = 'businesses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'industry_id',
        'industry',
        'active',
        'is_online_business',
        'name',
        'short_description',
        'description',
        'email',
        'phone',
        'mobile',
        'website',
        'fax',
        'vat_number',
        'id_number',
        'bank',
        'bank_id',
        'ecode_swift',
        'iban',
        'street1',
        'street2',
        'city',
        'state',
        'postal_code',
        'country_code',
        'zoom',
        'lat',
        'lng',
        'content',
        'social',
        'settings',
        'tags',
        'attributes',
        'meta',
        'timezone_id',
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
    ];

    /**
     * Appended columns.
     *
     * @var array
     */
    protected $appends =
    [
        'logo',
        'main_image',
        'main_image_thumb',
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
        'content' => 'json',
        'social' => 'json',
        'settings' => 'json',
        'tags' => 'json',
        'attributes' => 'json',
        'meta' => 'json'
    ];

    /**
     * Create a new factory instance for the Business model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
      return BusinessFactory::new();
    }

    public function registerMediaCollections() : void
    {
        $this
            ->addMediaCollection('logo')
            ->singleFile();

        $this
            ->addMediaCollection('main_image')
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
    }

    /**
     * Date/time fields that can be used with Carbon.
     *
     * @return array
     */
    public function getDates()
    {
        return [
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
    public static function getCreateForm() {
      $owner = [
        'tab1' => [
          'text' => __('General'),
          'subs' => [
            'sub1' => [
              'items' => [
                ['type' => 'relation', 'relation' => ['type' => 'hasOne', 'permission' => 'all', 'with' => 'industry', 'pk' => 'id', 'val' => 'name', 'orderBy' => 'name', 'order' => 'asc'], 'column' => 'industry_id', 'text' => __('Industry'), 'validate' => 'required', 'required' => true],
                ['type' => 'text', 'column' => 'name', 'text' => __('Store name'), 'validate' => 'required|max:32', 'required' => true],
                ['type' => 'image', 'image' => ['thumb_max_width' => '140px', 'thumb_max_height' => '100px'], 'column' => 'logo', 'text' => __('Logo'), 'validate' => 'nullable|image|max:1000', 'vvalidate' => 'image|size:1000', 'hint' => __('Maximum file size is 1 Mb'), 'icon' => 'attach_file'],
                ['type' => 'image', 'image' => ['thumb_max_width' => '180px', 'thumb_max_height' => '120px'], 'column' => 'main_image', 'text' => __('Picture'), 'validate' => 'nullable|image|max:1000', 'vvalidate' => 'image|size:1000', 'hint' => __('Maximum file size is 1 Mb'), 'icon' => 'attach_file'],
                ['type' => 'text', 'column' => 'short_description', 'text' => __('Short description'), 'validate' => 'required|max:150', 'required' => true],
                ['type' => 'wysiwyg', 'column' => 'description', 'config' => ['toolbar' => ['bold', 'italic', '|', 'link']], 'text' => __('Description'), 'validate' => 'nullable'],
                /*['type' => 'boolean', 'default' => false, 'column' => 'is_online_business', 'text' => __('Is online business'), 'validate' => 'nullable']*/
              ]
            ]
          ]
        ],
        'tab2' => [
          'text' => __('Contact'),
          'subs' => [
            'sub1' => [
              'text' => __('General'),
              'items' => [
                ['type' => 'email', 'column' => 'email', 'text' => __('E-mail address'), 'validate' => 'required|email|max:64', 'icon' => 'mail', 'required' => true],
                ['type' => 'text', 'column' => 'phone', 'text' => __('Phone'), 'validate' => 'nullable|max:32', 'icon' => 'phone'],
                ['type' => 'text', 'column' => 'website', 'text' => __('Website'), 'validate' => 'nullable|max:64', 'icon' => 'language']
              ]
            ],
            'sub2' => [
              'text' => __('Address'),
              'items' => [
                ['type' => 'text', 'column' => 'street1', 'text' => __('Street'), 'validate' => 'nullable|max:64'],
                ['type' => 'text', 'column' => 'postal_code', 'text' => __('Postal code'), 'validate' => 'nullable|max:32'],
                ['type' => 'text', 'column' => 'city', 'text' => __('City'), 'validate' => 'nullable|max:64'],
                ['type' => 'text', 'column' => 'state', 'text' => __('State / Province'), 'validate' => 'nullable|max:64']
              ]
            ]
          ]
        ],
        'tab3' => [
          'text' => __('Social'),
          'description' => __('Links to your social accounts. These will show in the footer of a campaign website.'),
          'subs' => [
            'sub1' => [
              'items' => [
                ['type' => 'text', 'column' => 'social->text', 'default' => 'Connect with us!', 'text' => 'Text', 'validate' => 'nullable|max:250', 'hint' => 'This text is shown in the footer of a campaign.'],
                ['type' => 'text', 'column' => 'social->facebook', 'text' => 'Facebook', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'fab fa-facebook'],
                ['type' => 'text', 'column' => 'social->youtube', 'text' => 'YouTube', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'fab fa-youtube'],
                ['type' => 'text', 'column' => 'social->vimeo', 'text' => 'Vimeo', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'fab fa-vimeo'],
                ['type' => 'text', 'column' => 'social->whatsapp', 'text' => 'WhatsApp', 'placeholder' => 'https://wa.me/<number>', 'validate' => 'nullable|url: {require_protocol: true }|max:64', 'icon' => 'fab fa-whatsapp'],
                ['type' => 'text', 'column' => 'social->instagram', 'text' => 'Instagram', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'fab fa-instagram'],
                ['type' => 'text', 'column' => 'social->twitter', 'text' => 'Twitter', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'fab fa-twitter'],
                ['type' => 'text', 'column' => 'social->linkedin', 'text' => 'LinkedIn', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'fab fa-linkedin'],
                ['type' => 'text', 'column' => 'social->tumblr', 'text' => 'Tumblr', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'fab fa-tumblr'],
                ['type' => 'text', 'column' => 'social->snapchat', 'text' => 'Snapchat', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'fab fa-snapchat'],
                ['type' => 'text', 'column' => 'social->pinterest', 'text' => 'Pinterest', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'fab fa-pinterest'],
                ['type' => 'text', 'column' => 'social->telegram', 'text' => 'Telegram', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'fab fa-telegram'],
                ['type' => 'text', 'column' => 'social->medium', 'text' => 'Medium', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'fab fa-medium']
              ]
            ]
          ]
        ],
        'tab4' => [
          'text' => __('Links'),
          'description' => __('Links that show in the footer of a campaign website. For example links to the company website, or a specific page with additional information about the loyalty campaign.'),
          'subs' => [
            'sub1' => [
              'items' => [
                ['type' => 'text', 'column' => 'content->text1', 'text' => 'Text link #1', 'validate' => 'nullable|max:120', 'icon' => 'title'],
                ['type' => 'text', 'column' => 'content->href1', 'text' => 'Url link #1', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'link'],
                ['type' => 'text', 'column' => 'content->text2', 'text' => 'Text link #2', 'validate' => 'nullable|max:120', 'icon' => 'title'],
                ['type' => 'text', 'column' => 'content->href2', 'text' => 'Url link #2', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'link'],
                ['type' => 'text', 'column' => 'content->text3', 'text' => 'Text link #3', 'validate' => 'nullable|max:120', 'icon' => 'title'],
                ['type' => 'text', 'column' => 'content->href3', 'text' => 'Url link #3', 'validate' => 'nullable|url: {require_protocol: true }|max:512', 'icon' => 'link']
              ]
            ]
          ]
        ],
        'tab5' => [
          'text' => __('Store Hours'),
          'subs' => [
            'sub1' => [
				'items' => [
					[
						'type' => 'relation',
						'relation' =>	[
											'type' => 'hasOne', 'permission' => 'all', 'with' => 'timezone',
											'pk' => 'id',
											'val' => 'timezone_name',
											'orderBy' => 'timezone_name',
											'order' => 'asc'
										],
						'column' => 'timezone_id',
						'text' => __('Time Zone'),
						'validate' => 'required',
						'required' => true
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
						'ref' => 'active_monday_from',
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
						'validate' => 'required_if:active_monday,1|time_greater_than:active_monday_from',
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
						'ref' => 'active_tuesday_from',
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
						'validate' => 'required_if:active_tuesday,1|time_greater_than:active_tuesday_from',
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
						'ref' => 'active_wednesday_from',
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
						'validate' => 'required_if:active_wednesday,1|time_greater_than:active_wednesday_from',
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
						'ref' => 'active_thursday_from',
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
						'validate' => 'required_if:active_thursday,1|time_greater_than:active_thursday_from',
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
						'ref' => 'active_friday_from',
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
						'validate' => 'required_if:active_friday,1|time_greater_than:active_friday_from',
						'placeholder' => 'To (HH:MM)',
						'required' => false,
						'style' => 'width:25%; display: inline-block; margin-left: 2em;'
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
						'ref' => 'active_saturday_from',
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
						'validate' => 'required_if:active_saturday,1|time_greater_than:active_saturday_from',
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
						'ref' => 'active_sunday_from',
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
						'validate' => 'required_if:active_sunday,1|time_greater_than:active_sunday_from',
						'placeholder' => 'To (HH:MM)',
						'required' => false,
						'style' => 'width:25%; display: inline-block; margin-left: 2em;'
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
     * Name used in plan limitations (optional)
     *
     * @return string
     */
    public static function getLimitationName()
    {
        return 'businesses';
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
        $owner = ['select_all' => true, 'actions' => true, 'create' => true, 'dialog_height' => 375];
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
            'items' => __('Stores'),
            'edit_item' => __('Edit Store'),
            'create_item' => __('Create Store'),
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
            ['visible' => true, 'value' => 'name', 'text' => __('Store name'), 'align' => 'left', 'sortable' => true],
            ['visible' => true, 'value' => 'email', 'text' => __('E-mail'), 'align' => 'left', 'sortable' => true],
            ['visible' => true, 'value' => "address|CONCAT(street1, ' ', postal_code, ' ', city) as address", 'text' => __('Address'), 'align' => 'left', 'sortable' => false],
            ['visible' => true, 'value' => 'phone', 'text' => __('Phone'), 'align' => 'left', 'sortable' => false],
            ['visible' => true, 'value' => 'main_image_thumb', 'type' => 'image', 'exclude_from_select' => true, 'max_width' => '140px', 'text' => __('Picture'), 'align' => 'left', 'sortable' => false]
            /*['visible' => true, 'value' => 'is_online_business', 'text' => __('Online business'), 'align' => 'center', 'sortable' => true, 'type' => 'boolean']*/
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

    /**
     * Get contact methods.
     *
     * @return array
     */
    public function getContactMethods()
    {
        $contactMethods = [];

        if ($this->phone !== null) {
            $contactMethods[] = [
                'icon' => 'phone',
                'title' => $this->phone,
                'subtitle' => 'call_us',
                'link' => 'tel:' . str_replace([' ', '(', ')', '-'], '', $this->phone),
                'target' => '_self'
            ];
        }

        if ($this->email !== null) {
            $contactMethods[] = [
                'icon' => 'mail',
                'title' => $this->email,
                'subtitle' => 'send_us_a_mail',
                'link' => 'mailto:' . $this->email,
                'target' => '_self'
            ];
        }

        if ($this->website !== null) {
            $naked_url = str_replace(['http://', 'https://'], '', $this->website);

            $contactMethods[] = [
                'icon' => 'language',
                'title' => $naked_url,
                'subtitle' => 'visit_our_website',
                'link' => '//' . $naked_url,
                'target' => '_blank'
            ];
        }

        if ($this->street1 !== null && $this->city !== null) {
            $address = $this->city;

            if ($this->postal_code !== null) $address .= ', ' . $this->postal_code;

            $contactMethods[] = [
                'icon' => 'location_on',
                'translate' => false,
                'title' => $this->street1,
                'subtitle' => $address,
                'link' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode($this->street1 . ' ' . $address),
                'target' => '_blank'
            ];
        }

      return $contactMethods;
    }

    /**
     * Get social links.
     *
     * @return array
     */
    public function getSocialLinks()
    {
        $socialLinks = [];

        if (isset($this->social['facebook'])) {
            $socialLinks[] = [
                'icon' => 'fab fa-facebook',
                'href' => $this->social['facebook']
            ];
        }

        if (isset($this->social['youtube'])) {
            $socialLinks[] = [
                'icon' => 'fab fa-youtube',
                'href' => $this->social['youtube']
            ];
        }

        if (isset($this->social['vimeo'])) {
            $socialLinks[] = [
                'icon' => 'fab fa-vimeo',
                'href' => $this->social['vimeo']
            ];
        }

        if (isset($this->social['whatsapp'])) {
            $socialLinks[] = [
                'icon' => 'fab fa-whatsapp',
                'href' => $this->social['whatsapp']
            ];
        }

        if (isset($this->social['instagram'])) {
            $socialLinks[] = [
                'icon' => 'fab fa-instagram',
                'href' => $this->social['instagram']
            ];
        }

        if (isset($this->social['twitter'])) {
            $socialLinks[] = [
                'icon' => 'fab fa-twitter',
                'href' => $this->social['twitter']
            ];
        }

        if (isset($this->social['linkedin'])) {
            $socialLinks[] = [
                'icon' => 'fab fa-linkedin',
                'href' => $this->social['linkedin']
            ];
        }

        if (isset($this->social['tumblr'])) {
            $socialLinks[] = [
                'icon' => 'fab fa-tumblr',
                'href' => $this->social['tumblr']
            ];
        }

        if (isset($this->social['snapchat'])) {
            $socialLinks[] = [
                'icon' => 'fab fa-snapchat',
                'href' => $this->social['snapchat']
            ];
        }

        if (isset($this->social['pinterest'])) {
            $socialLinks[] = [
                'icon' => 'fab fa-pinterest',
                'href' => $this->social['pinterest']
            ];
        }

        if (isset($this->social['telegram'])) {
            $socialLinks[] = [
                'icon' => 'fab fa-telegram',
                'href' => $this->social['telegram']
            ];
        }

        if (isset($this->social['medium'])) {
            $socialLinks[] = [
                'icon' => 'fab fa-medium',
                'href' => $this->social['medium']
            ];
        }

        return $socialLinks;
    }

    /**
     * Get external links for top of campaign website.
     *
     * @return array
     */
    public function getExternalLinks()
    {
        $links = [];

        for ($i = 1; $i <= 3; $i++) {
            if (isset($this->content['text' . $i]) && isset($this->content['href' . $i])) {
                $links[] = [
                    'text' => $this->content['text' . $i],
                    'href' => $this->content['href' . $i]
                ];
            }
        }

        return $links;
    }

    /**
     * Images
     * -------------
     */

    public function getLogoAttribute()
    {
        return ($this->getFirstMediaUrl('logo') !== '') ? $this->getFirstMediaUrl('logo') : null;
    }

    public function getMainImageAttribute()
    {
        return ($this->getFirstMediaUrl('main_image') !== '') ? $this->getFirstMediaUrl('main_image') : null;
    }

    public function getMainImageThumbAttribute()
    {
        return ($this->getFirstMediaUrl('main_image') !== '') ? $this->getMedia('main_image')[0]->getUrl('thumb') : null;
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
        return $this->hasOne(\Platform\Models\Campaign::class);
    }

    public function staff()
    {
        return $this->belongsToMany(\App\Staff::class, 'business_staff', 'business_id', 'staff_id');
    }

    public function industry()
    {
        return $this->hasOne(\Platform\Models\Industry::class, 'id', 'industry_id');
    }

    public function timezone()
    {
        return $this->hasOne(\Platform\Models\Timezone::class, 'id', 'timezone_id');
    }

    public function segments()
    {
        return $this->belongsToMany(\Platform\Models\Segment::class, 'business_segment', 'business_id', 'segment_id');
    }
}
