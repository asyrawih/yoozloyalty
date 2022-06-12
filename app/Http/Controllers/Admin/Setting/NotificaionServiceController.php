<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\SmsApiTrait;
use App\Repositories\ {
    NotificationServiceRepository
};
use App\Http\Requests\NoticationServiceSettings\ {
    StoreRequest,
    UpdateRequest,
    TestRequest
};

class NotificaionServiceController extends Controller
{

    use SmsApiTrait;

    private $notif;

    public function __construct(
        NotificationServiceRepository $notif
    )
    {
        $this->notif = $notif;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->notif->datatable([
            'per_page' => $request->per_page,
            'service_name' => $request->service_name,
            'status' => $request->status
        ]);

        return $this->apiResponse([
            'error' => false,
            'message' => 'Notication Service lists.',
            'data' => $data,
            
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = json_decode(file_get_contents($this->notif->servicesBluePrint()), true);

        return $this->apiResponse([
            'error' => false,
            'message' => 'List Notification Services.',
            'data' => $data
        ]);
    }

    public function getApiScheme($name)
    {
        $schemes = [];

        if ($name !== 'null') {
            $schemes = json_decode(file_get_contents(str_replace('{file}', $name, $this->notif->serviceBluePrint())), true);
        }

        return $this->apiResponse([
            'error' => false,
            'message' => 'Detail Notificaiton Services.',
            'data' => $schemes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return $this->apiResponse($this->notif->store($request));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        return $this->apiResponse(
            $this->notif->update($request, $id)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->apiResponse($this->notif->destroy($id));
    }

    public function test(TestRequest $request)
    {
        $phoneNumber = $request->isd_code . $request->phone_number;

        $this->sendSms(
            $request->isd_code,
            $phoneNumber,
            'Send Test SMS'
        );

        return $this->apiResponse([
            'error' => false,
            'message' => 'Test sms is sent to '. $phoneNumber
        ]);
    }

}
