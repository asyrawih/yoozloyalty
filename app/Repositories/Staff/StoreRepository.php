<?php

namespace App\Repositories\Staff;

use Platform\Models\Business;
use App\Repositories\BaseRepository;

class StoreRepository extends BaseRepository
{
    public static function getAllStore()
    {
        return Business::all();
    }

    public static function isStoreOperational($business_id) {
        $business = Business::select(
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
        )->with(
            [ 'timezone' => function($q) {
                    $q->select('id','timezone_name');
                }
            ]
        )->where('id', $business_id)->first();

        $businessTimezone = $business->timezone->timezone_name;
        $currentDate = \Carbon\Carbon::now()->setTimezone($businessTimezone)->format('Y-m-d ');
        $currentDay = \Carbon\Carbon::now()->setTimezone($businessTimezone)->format('l');

        $active_from = $currentDate . $business->{"active_" . strtolower($currentDay) . "_from"}.':00';
        $active_to = $currentDate . $business->{"active_" . strtolower($currentDay) . "_to"}.':00';

        $is_active = (int) $business->{"active_" . strtolower($currentDay)};
        $active_from = (int) date('YmdHis',strtotime($active_from));
        $active_to = (int) date('YmdHis',strtotime($active_to));
        $client_datetime = (int) \Carbon\Carbon::createFromFormat('YmdHis', date('YmdHis'))->setTimezone($businessTimezone)->format('YmdHis');

        if($is_active < 1){
            return false;
        } else if($client_datetime < $active_from) {
            return false;
        } else if($client_datetime > $active_to) {
            return false;
        } else {
            return true;
        }

        // For testing purpose:
        return  [ 'tz' => $businessTimezone
                , 'is_active' => $is_active
                , 'from' => $active_from
                , 'to' => $active_to
                , 'client_time' => $client_datetime
                ];
    }
}
