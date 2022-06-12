<?php

namespace Platform\Models;

use App\Customer;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PlanChangeRequest extends Model
{
    const APPROVED_STATUS = 'approved';
    const REJECTED_STATUS = 'rejected';
    const CANCELLED_STATUS = 'cancelled';
    const PENDING_STATUS = 'pending';

    const statuses = [
        'approved' => "Approved",
        'rejected' => "Rejected",
        'cancelled' => "Cancelled",
        'pending' => "Pending"
    ];

    protected $table = 'plan_change_requests';

    protected $fillable = [
        'previous_plan_id',
        'new_plan_id',
        'created_by',
        'approved_by',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d'
    ];

    protected $appends = [
        'status_text',
        'type',
        'previous_plan_name',
        'new_plan_name'
    ];

    public function getStatusTextAttribute(){
        return self::statuses[$this->status];
    }

    public function getTypeAttribute(){
        if($this->previous_plan_id === $this->new_plan_id){
            return 'renew';
        } else {
            return 'change';
        }
    }

    public function getPreviousPlanNameAttribute(){
        return $this->previousPlan->name ?? "";
    }

    public function getNewPlanNameAttribute(){
        return $this->newPlan->name;
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
                            ['type' => 'text', 'column' => 'name', 'text' => __('Name'), 'validate' => 'required|max:32', 'required' => true, 'icon' => 'business']
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
    public static function getExtraSelectColumns() {
        $owner = ['previous_plan_id', 'new_plan_id'];
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
    public static function getExtraQueryColumns() {
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
    public static function getSettings() {
        $owner = ['select_all' => false, 'actions' => false, 'create' => false, 'actions_width' => '90px'];
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
            'items' => __('Plan Change History'),
            'edit_item' => __('Edit plan change request'),
            'create_item' => __('Create plan change request'),
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
        $owner = ['view' => 'personal', 'delete' => 'personal', 'update' => 'personal', 'create' => false];
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
    public static function getHeaders() {
        $owner = [
            ['visible' => true, 'exclude_from_select' => true, 'value' => 'previous_plan_name', 'text' => __('Previous plan'), 'align' => 'left', 'sortable' => false],
            ['visible' => true, 'exclude_from_select' => true, 'value' => 'new_plan_name', 'text' => __('New plan'), 'align' => 'left', 'sortable' => false],
            ['visible' => true, 'value' => 'status', 'text' => __('Status'), 'align' => 'left', 'sortable' => false],
            ['visible' => true, 'value' => 'created_at', 'text' => __('Requested on'), 'align' => 'left', 'sortable' => false],
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
     * Get JSON translation if exists (resources/lang/[language].json
     *
     * @return string
     */
    public function getNameTranslatedAttribute() {
        return __($this->name);
    }

    public function user(){
        return $this->belongsTo(Customer::class, 'created_by', 'id');
    }

    public function previousPlan(){
        return $this->belongsTo(Plan::class, 'previous_plan_id', 'id')->select(['id', 'name']);
    }

    public function newPlan(){
        return $this->belongsTo(Plan::class, 'new_plan_id', 'id')->select(['id', 'name']);
    }

    public function approvedBy(){
        return $this->hasOne(User::class, 'approved_by', 'id');
    }


}
