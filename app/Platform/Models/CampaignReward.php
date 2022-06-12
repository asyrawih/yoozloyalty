<?php

namespace Platform\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CampaignReward extends Pivot
{
    use HasFactory;
    protected $table = 'campaign_reward';
    protected $dates = ['active_from','expires_at'];
    protected $fillable = [
        'active_from',
        'expires_at',
        'active_monday',
        'active_monday_from',
        'active_monday_to',
        'active_tuesday',
        'active_tuesday_from',
        'active_tuesday_to',
        'active_wednesday',
        'active_wednesday_from',
        'active_wednesday_to',
        'active_thursday',
        'active_thursday_from',
        'active_thursday_to',
        'active_friday',
        'active_friday_from',
        'active_friday_to',
        'active_saturdayday',
        'active_saturdayday_from',
        'active_saturdayday_to',
        'active_sunday',
        'active_sunday_from',
        'active_sunday_to'
    ];
    public $timestamps = false;
}
