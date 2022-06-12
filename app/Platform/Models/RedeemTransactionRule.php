<?php

namespace Platform\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use App\Scopes\AccountScope;
use Illuminate\Database\Eloquent\Builder;

class RedeemTransactionRule extends Model
{
    use GeneratesUuid;

    protected $table = 'redeem_transaction_rules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'account_id',
        'user_id',
        'value',
        'minimum_points',
        'maximum_redeem',
        'created_by',
        'updated_by'
    ];

    /**
     * Appended columns.
     *
     * @var array
     */

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
    ];

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
                $model->updated_by = auth()->id();
            }
        });

        // On create
        self::creating(function ($model) {
            if (auth()->check()) {
                $model->account_id = auth()->user()->account_id;

                $model->user_id = auth()->id();

                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
        });
    }

    public function scopeWhereUser(Builder $query, int $user_id): Builder
    {
        return $query->where('user_id', $user_id);
    }

    // /**
    //  * Form for creating a new record, per role
    //  *
    //  * @return array
    //  */
    // public static function getCreateForm() {
    //   $owner = [
    //     'tab1' => [
    //       'subs' => [
    //         'sub1' => [
    //           'items' => [
    //             [
    //               'type' => 'number',
    //               'column' => 'min_amount',
    //               'text' => __('Min Amount'),
    //               'validate' => 'required',
    //               'validation' => 'required_with:max_amount|integer|lt:max_amount',
    //               'vvalidate' => 'required|numeric|max_value:1000',
    //               'required' => true
    //             ],
    //             [
    //               'type' => 'number',
    //               'column' => 'max_amount',
    //               'text' => __('Max Amount'),
    //               'validate' => 'required',
    //               'validation' => 'required_with:min_amount|integer|gt:min_amount',
    //               'vvalidate' => 'required|numeric|min_value:10',
    //               'required' => true
    //             ],
    //             [
    //               'type' => 'conversion_rule',
    //               'column' => 'rule',
    //               'text' => __('Rate Type'),
    //               'validate' => 'required',
    //               'required' => true
    //             ],
    //             [
    //               'type' => 'number',
    //               'column' => 'value',
    //               'text' => __('Value'),
    //               'validate' => 'required',
    //               'vvalidate' => 'required',
    //               'required' => true
    //             ],
    //           ]
    //         ]
    //       ]
    //     ]
    //   ];
    //   $reseller = $owner;
    //   $user = $owner;

    //   return [
    //     1 => $owner,
    //     2 => $reseller,
    //     3 => $user
    //   ];
    // }

    // /**
    //  * Name used in plan limitations (optional)
    //  *
    //  * @return string
    //  */
    // public static function getLimitationName() {
    //   return 'rewards';
    // }

    // /**
    //  * Extra columns used in select queries, exposed in json response
    //  *
    //  * @return array
    //  */
    // public static function getExtraSelectColumns() {
    //   $owner = ['uuid'];
    //   $reseller = $owner;
    //   $user = $owner;

    //   return [
    //     1 => $owner,
    //     2 => $reseller,
    //     3 => $user
    //   ];
    // }

    // /**
    //  * Extra columns used in select queries, hidden from json response
    //  *
    //  * @return array
    //  */
    // public static function getExtraQueryColumns() {
    //   $owner = ['id', 'created_by'];
    //   $reseller = $owner;
    //   $user = $owner;

    //   return [
    //     1 => $owner,
    //     2 => $reseller,
    //     3 => $user
    //   ];
    // }

    // /**
    //  * Generic settings
    //  *
    //  * actions: add actions column (true / false)
    //  *
    //  * @return array
    //  */
    // public static function getSettings() {
    //   $owner = [
    //     'select_all' => true,
    //     'actions' => true,
    //     'create' => true,
    //     'actions_width' => '90px',
    //     'dialog_width' => 640,
    //     'dialog_height' => 380
    //   ];

    //   $reseller = $owner;
    //   $user = $owner;

    //   return [
    //     1 => $owner,
    //     2 => $reseller,
    //     3 => $user
    //   ];
    // }

    // /**
    //  * Language variables
    //  *
    //  * @return array
    //  */
    // public static function getTranslations() {
    //   return [
    //     'items' => __('Redeem Transaction Rule'),
    //     'edit_item' => __('Edit Redeem Transaction Rule'),
    //     'create_item' => __('Create Redeem Transaction Rule'),
    //   ];
    // }

    // /**
    //  * Define per role if and what they can see
    //  *
    //  * all: all records from all accounts
    //  * account: all records from the current account
    //  * personal: only records the current user has created
    //  * created_by: only records created by the user id defined like created_by:1
    //  * none: this role has no permission
    //  *
    //  * @return array
    //  */
    // public static function getPermissions() {
    //   $owner = ['view' => 'all', 'delete' => 'all', 'update' => 'all', 'create' => true];
    //   $reseller = ['view' => 'account', 'delete' => 'account', 'update' => 'account', 'create' => false];
    //   $user = ['view' => 'personal', 'delete' => 'personal', 'update' => 'personal', 'create' => true];

    //   return [
    //     1 => $owner,
    //     2 => $reseller,
    //     3 => $user
    //   ];
    // }

    // /**
    //  * The headers for the data table, per role (value = column name)
    //  *
    //  * @return array
    //  */
    // public static function getHeaders() {
    //   $owner = [
    //     [
    //       'visible' => true,
    //       'value' => 'min_amount',
    //       'text' => __('Min Amount'),
    //       'align' => 'left',
    //       'sortable' => true
    //     ],
    //     [
    //       'visible' => true,
    //       'value' => 'max_amount',
    //       'text' => __('Max Amount'),
    //       'align' => 'left',
    //       'sortable' => true
    //     ],
    //     [
    //       'visible' => true,
    //       'value' => 'rule',
    //       'text' => __('Rule'),
    //       'align' => 'left',
    //       'sortable' => true
    //     ],
    //     [
    //       'visible' => true,
    //       'value' => 'value',
    //       'text' => __('value'),
    //       'align' => 'left',
    //       'sortable' => true
    //     ],
    //   ];
    //   $reseller = $owner;
    //   $user = $owner;

    //   return [
    //     1 => $owner,
    //     2 => $reseller,
    //     3 => $user
    //   ];
    // }

    // /**
    //  * The columns used for searching the table
    //  *
    //  * @return array
    //  */
    // public static function getSearchColumns() {
    //   $owner = ['min_amount', 'max_amount', 'value', 'rule'];
    //   $reseller = $owner;
    //   $user = $owner;

    //   return [
    //     1 => $owner,
    //     2 => $reseller,
    //     3 => $user
    //   ];
    // }

    // /**
    //  * Available actions for data table row, per role
    //  *
    //  * @return array
    //  */
    // public static function getActions() {
    //   $owner = [
    //     ['text' => __('Edit'), 'action' => 'edit', 'icon' => 'edit', 'color' => 'secondary', 'dark' => false],
    //     /*['divider'],*/
    //     ['text' => __('Delete'), 'action' => 'delete', 'icon' => 'delete', 'color' => 'secondary', 'dark' => true]
    //   ];

    //   $reseller = [
    //     ['text' => __('Edit'), 'action' => 'edit', 'icon' => 'edit', 'color' => 'secondary', 'dark' => false]
    //   ];

    //   $reseller = $owner;
    //   $user = $owner;

    //   return [
    //     1 => $owner,
    //     2 => $reseller,
    //     3 => $user
    //   ];
    // }

    // public function getTitleWithPointsAttribute() {
    //   return $this->title . ' (' . $this->points_cost . ')';
    // }

    // /**
    //  * Relationships
    //  * -------------
    //  */
    // public function account() {
    //   return $this->belongsTo(\App\User::class, 'account_id', 'id');
    // }

    // public function users() {
    //   return $this->belongsToMany(\App\User::class, 'company_user', 'company_id', 'user_id');
    // }
}
