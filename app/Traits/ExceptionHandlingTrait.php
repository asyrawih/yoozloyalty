<?php

namespace App\Traits;

use App\Libraries\IpAddress;

trait ExceptionHandlingTrait
{
    public function showException($error)
    {

        $ips = in_array(IpAddress::getIpAddress(), explode(',', config('app.APP_DEBUG_IPS')));

        if($ips) {
            return [
                'error' => true,
                'message' => $error,
            ];
        }

        if(request()->ajax()) {
            return [
                'error' => true,
                'message' => 'Internal server error.'
            ];
        }

        return abort(500);
    }

}
