<?php

namespace Platform\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Platform\Controllers\Core;
use App\Scopes\AccountScope;
use Carbon\Carbon;

class CoupunCode extends Model
{
    use GeneratesUuid;

    protected $table = 'coupun_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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
    public function getDates() {
      return ['created_at', 'updated_at'];
    }

    public static function boot() {
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
     * Relationships
     * -------------
     */

    public function account() {
      return $this->belongsTo(\App\User::class, 'account_id', 'id');
    }

    public function users() {
      return $this->belongsToMany(\App\User::class, 'company_user', 'company_id', 'user_id');
    }
    
    public function rewards() {
      return $this->belongsToMany(\Platform\Models\Reward::class, 'reward_id', 'id');
    }
    
    public function coupunUsed() {
      return $this->hasOne(\Platform\Models\CoupunUsed::class, 'coupun_code_id', 'id');
    }
}
