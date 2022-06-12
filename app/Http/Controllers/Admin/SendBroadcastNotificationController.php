<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\NotifPusherRepositories;
use App\User;
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
            'merchants' => 'required'
        ]);

        return $this->apiResponse(
            $this->notifPusher->sendBroadcast($request)
        );
    }
}
