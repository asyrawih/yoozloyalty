<?php

namespace Platform\Models;

use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;

class SmsService extends Model
{
  use GeneratesUuid;

  protected $table = 'sms_services';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'blueprint',
    'country_isd_code',
    'is_active',
    'schema'
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
  ];

  /**
   * Date/time fields that can be used with Carbon.
   *
   * @return array
   */
  public function getDates()
  {
    return [];
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
                'type' => 'select', 
                'column' => 'country_isd_code',  
                'text' => __('Service'), 
                'validate' => 'required', 
                'icon' => 'receipt',
                'required' => true,
                'validate' => 'required', 
                'select_item' => json_decode(file_get_contents(storage_path('json/settings/sms/services.json')), true)
              ],
              [
                'type' => 'phone', 
                'column' => 'name', 
                'text' => __('Name'), 
                'validate' => 'required|max:32', 
                'required' => true, 
                'icon' => 'business'
              ],
              [
                'type' => 'boolean', 
                'default' => true, 
                'column' => 'is_active', 
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
    $owner = ['select_all' => true, 'actions' => true, 'create' => true, 'actions_width' => '90px'];
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
      'items' => __('Sms Services'),
      'edit_item' => __('Edit Service'),
      'create_item' => __('Add Service'),
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
      [
        'visible' => true,
        'value' => 'country_isd_code',
        'text' => __('ISD Code'),
        'align' => 'left',
        'sortable' => false
      ],
      [
        'visible' => true,
        'value' => 'name',
        'text' => __('Service'),
        'align' => 'left',
        'sortable' => true
      ],
      [
        'visible' => true,
        'value' => 'is_active',
        'text' => __('Status'),
        'align' => 'left',
        'sortable' => false
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
   * Relationships
   * -------------
   */

  // public function users()
  // {
  //   return $this->hasMany(\App\User::class, 'plan_id', 'id');
  // }
}
