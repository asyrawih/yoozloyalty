<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\SmsApiTrait;
use App\Repositories\ {
    SmsSetingsRepository,
    CountryRepository
};
use App\Http\Requests\SmsSettings\ {
    StoreRequest,
    UpdateRequest,
    TestRequest
};

class SmsSettingController extends Controller
{

    use SmsApiTrait;

    const RESOURCE = 'newpanel.backend.pages.app-settings.sms.';

    private $sms, $country;

    public function __construct(
        SmsSetingsRepository $sms,
        CountryRepository $country
    )
    {
        $this->sms = $sms;
        $this->country = $country;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->sms->datatable([
            'per_page' => $request->per_page,
            'isd_code' => $request->isd_code,
            'sms_services' => $request->sms_services,
            'status' => $request->status
        ]);

        return $this->apiResponse([
            'error' => false,
            'message' => 'Sms Service lists.',
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
        $data = json_decode(file_get_contents($this->sms->servicesBluePrint()), true);

        return $this->apiResponse([
            'error' => false,
            'message' => 'List SMS Services.',
            'data' => $data
        ]);
    }

    public function getApiScheme($name)
    {
        $schemes = [];

        if ($name !== 'null') {
            $schemes = json_decode(file_get_contents(str_replace('{file}', $name, $this->sms->serviceBluePrint())), true);
        }

        return $this->apiResponse([
            'error' => false,
            'message' => 'Detail SMS Services.',
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
        return $this->apiResponse($this->sms->store($request));
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['services'] = json_decode(file_get_contents($this->sms->servicesBluePrint()), true);

        $data['countries'] = $this->country->where('country_status', $this->country->status(1), 'all');

        $data['data'] = $this->sms->find($id);

        $data['schemes'] = json_decode($data['data']->schema);

        return $this->apiResponse([
            'error' => false,
            'message' => 'Success.',
            'data' => [
                'body' => view(self::RESOURCE.'.edit', $data)->render(),
            ]
        ]);
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
            $this->sms->update($request, $id)
        );
    }

    public function delete($id)
    {
        $data['data'] = $this->sms->find($id);
        return $this->apiResponse([
            'error' => false,
            'data' => [
                'body' => view(self::RESOURCE.'delete', $data)->render()
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->apiResponse($this->sms->destroy($id));
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
