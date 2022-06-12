<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Repositories\NotifPusherRepositories;
use Illuminate\Http\Request;

class SendBroadcastNotificationController extends Controller
{
    private $notifPusher;

    public function __construct(NotifPusherRepositories $notifPusher)
    {
        $this->notifPusher = $notifPusher;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $this->validate($request,[
            'title' => 'required',
            'message' => 'required',
            'customers' => 'required'
        ]);

        return $this->apiResponse(
            $this->notifPusher->sendBroadcastCustomer($request)
        );
    }
}
