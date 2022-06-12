<?php

namespace Platform\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use App\Scopes\AccountScope;

class CreditRequest extends Model
{
    use GeneratesUuid;

    protected $table = 'credit_requests';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $fillable = [
        'uuid',
        'account_id',
        'customer_id',
        'campaign_id',
        'receipt_number',
        'receipt_amount',
        'points',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $appends = [
        'name',
        'number',
        'email',
        'campaign_text',
    ];

    protected $hidden = [
        'account',
        'customer',
        'campaign'
    ];

    protected $casts = [
        'created_at' => "datetime:d/m/Y H:i",
    ];

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
            }
        });
    }

    /**
     * Columns used for filters
     *
     * @return array
     */
    public static function getTableFilters()
    {
        $owner = [];
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
        $owner = ['uuid', 'campaign_id', 'customer_id'];
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
        $owner = ['id', 'account_id', 'created_by'];
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
            'create' => false,
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
    public static function getTranslations() {
      return [
        'items' => __('Points Credit Request'),
        'edit_item' => __('Edit point credit request'),
        'create_item' => __('Create point credit request'),
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
    public static function getPermissions() {
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
    public static function getHeaders() {
      $owner = [
        ['visible' => true, 'value' => 'name', 'exclude_from_select' => true, 'text' => __('Name'), 'align' => 'left', 'sortable' => true],
        ['visible' => true, 'value' => 'email', 'exclude_from_select' => true, 'text' => __('Email'), 'align' => 'left', 'sortable' => true],
        ['visible' => true, 'value' => 'number', 'exclude_from_select' => true, 'text' => __('Number'), 'align' => 'left', 'sortable' => true],
        ['visible' => true, 'value' => 'campaign_text', 'exclude_from_select' => true, 'text' => __('Website'), 'align' => 'left', 'sortable' => true, 'type' => 'campaign_link'],
        ['visible' => true, 'value' => 'points', 'text' => __('Points Request'), 'align' => 'left', 'sortable' => true],
        ['visible' => true, 'value' => 'status', 'text' => __('Status'), 'align' => 'left', 'sortable' => true, 'type' => 'credit_request_status'],
        // ['visible' => true, 'value' => 'businesses_text', 'exclude_from_select' => true, 'relation' => ['type' => 'belongsToMany', 'with' => 'businesses', 'table' => 'businesses', 'val' => 'name'], 'text' => __('Store Name'), 'align' => 'left', 'sortable' => false]
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
    public static function getSearchColumns() {
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
    public static function getActions() {
      $owner = [
        [
              'text' => __('Update Status'),
              'action' => 'credit_request_status',
              'icon' => 'edit',
              'color' => 'secondary',
              'dark' => false
          ],
        /*['divider'],*/
        // ['text' => __('Delete'), 'action' => 'delete', 'icon' => 'delete', 'color' => 'secondary', 'dark' => true]
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
     * Get campaign name
     *
     * @return string
     */
    public function getCampaignTextAttribute()
    {
        if ($this->campaign) {
            return $this->campaign->name;
        }

        return NULL;

        // return ($this->account != null) ? '//' . $this->account->app_host . '/campaign/' . $this->campaign->slug : null;
    }

    /**
     * Format customer number.
     *
     * @return string
     */
    public function getNumberAttribute()
    {
        if ($this->customer && $this->customer->customer_number) {
            $customerNumber = $this->customer->customer_number;

            $splitCustomerNumber = str_split($customerNumber, 6);

            $splitCustomerNumber_0 = str_split($splitCustomerNumber[0], 3);

            $mergeCustomerNumber = array_merge($splitCustomerNumber_0, [$splitCustomerNumber[1]]);

            return implode('-', $mergeCustomerNumber);
        }

        return NULL;
    }

    /**
     * Customer name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return ($this->customer) ? $this->customer->name : NULL;
    }

    /**
     * Customer email.
     *
     * @return string
     */
    public function getEmailAttribute()
    {
        return ($this->customer) ? $this->customer->email : NULL;
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
        return $this->belongsTo(\Platform\Models\Campaign::class, 'campaign_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(\App\Customer::class, 'customer_id', 'id');
    }
}
